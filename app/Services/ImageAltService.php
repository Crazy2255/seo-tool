<?php

namespace App\Services;

use App\Models\ImageAltAudit;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use DOMDocument;
use DOMXPath;

class ImageAltService
{
    private $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36';

    /**
     * Analyze images on a single page or multiple pages.
     */
    public function analyzeImages(string $url, ?int $userId = null, bool $multiPage = false, int $maxPages = 5): array
    {
        try {
            // Validate URL
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                throw new \InvalidArgumentException('Invalid URL provided');
            }

            $startTime = microtime(true);
            $pagesToCrawl = $multiPage ? $this->discoverPages($url, $maxPages) : [$url];
            $allImageData = [];
            $crawlSummary = [];

            foreach ($pagesToCrawl as $pageUrl) {
                try {
                    $pageResult = $this->analyzeSinglePage($pageUrl);
                    $allImageData = array_merge($allImageData, $pageResult['images']);
                    $crawlSummary[] = [
                        'url' => $pageUrl,
                        'total_images' => count($pageResult['images']),
                        'page_title' => $pageResult['page_title'],
                        'status' => 'success'
                    ];
                } catch (\Exception $e) {
                    Log::warning('Failed to analyze page', [
                        'url' => $pageUrl,
                        'error' => $e->getMessage()
                    ]);
                    $crawlSummary[] = [
                        'url' => $pageUrl,
                        'total_images' => 0,
                        'page_title' => null,
                        'status' => 'failed',
                        'error' => $e->getMessage()
                    ];
                }
            }

            // Analyze all collected images
            $analysis = $this->analyzeImageData($allImageData);
            $mainPageTitle = $crawlSummary[0]['page_title'] ?? parse_url($url, PHP_URL_HOST);

            $auditData = [
                'user_id' => $userId,
                'url' => $url,
                'page_title' => $mainPageTitle,
                'total_images' => $analysis['total_images'],
                'images_without_alt' => $analysis['images_without_alt'],
                'images_with_empty_alt' => $analysis['images_with_empty_alt'],
                'images_with_good_alt' => $analysis['images_with_good_alt'],
                'images_with_issues' => $analysis['images_with_issues'],
                'images_data' => $allImageData,
                'crawl_summary' => $crawlSummary,
                'pages_crawled' => count($pagesToCrawl),
                'is_multi_page' => $multiPage,
                'analyzed_at' => Carbon::now(),
            ];

            // Store in database if user is provided
            if ($userId) {
                $audit = ImageAltAudit::create($auditData);
                $auditData['id'] = $audit->id;
            }

            $processingTime = round(microtime(true) - $startTime, 2);

            return [
                'success' => true,
                'data' => $auditData,
                'processing_time' => $processingTime,
                'message' => 'Image accessibility analysis completed successfully.'
            ];

        } catch (\Exception $e) {
            Log::error('Image ALT analysis failed', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Analysis failed: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Analyze a single page for images.
     */
    private function analyzeSinglePage(string $url): array
    {
        // Fetch page content
        $response = Http::withHeaders([
            'User-Agent' => $this->userAgent,
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language' => 'en-US,en;q=0.5',
            'Accept-Encoding' => 'gzip, deflate',
            'Connection' => 'keep-alive',
        ])->timeout(30)->get($url);

        if (!$response->successful()) {
            throw new \Exception("Failed to fetch page: HTTP {$response->status()}");
        }

        $html = $response->body();
        
        // Parse HTML
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        
        // Extract page title
        $titleNodes = $xpath->query('//title');
        $pageTitle = $titleNodes->length > 0 ? trim($titleNodes->item(0)->textContent) : '';

        // Find all img elements
        $imgElements = $xpath->query('//img');
        $images = [];

        foreach ($imgElements as $img) {
            $src = $img->getAttribute('src');
            $alt = $img->getAttribute('alt');
            $title = $img->getAttribute('title');
            $width = $img->getAttribute('width');
            $height = $img->getAttribute('height');

            // Skip if no src
            if (empty($src)) {
                continue;
            }

            // Convert relative URLs to absolute
            $absoluteSrc = $this->makeAbsoluteUrl($src, $url);

            // Get surrounding context
            $context = $this->getImageContext($img, $xpath);

            // Analyze the image
            $analysis = $this->analyzeImageAlt($alt, $src, $context);

            $images[] = [
                'src' => $absoluteSrc,
                'original_src' => $src,
                'alt' => $alt,
                'title' => $title,
                'width' => $width,
                'height' => $height,
                'context' => $context,
                'analysis' => $analysis,
                'page_url' => $url,
                'file_name' => basename(parse_url($absoluteSrc, PHP_URL_PATH)),
                'file_extension' => pathinfo($absoluteSrc, PATHINFO_EXTENSION),
            ];
        }

        return [
            'page_title' => $pageTitle,
            'images' => $images
        ];
    }

    /**
     * Analyze the quality of alt text for an image.
     */
    private function analyzeImageAlt(string $alt, string $src, array $context): array
    {
        $issues = [];
        $recommendations = [];
        $score = 100;
        $status = 'good';

        // Check if alt attribute exists and is not empty
        if (empty($alt)) {
            $issues[] = 'Missing or empty alt attribute';
            $recommendations[] = 'Add descriptive alt text that explains what the image shows';
            $score = 0;
            $status = 'missing';
        } else {
            // Analyze alt text quality
            $altLower = strtolower(trim($alt));
            $altLength = strlen($alt);

            // Check for common issues
            if ($altLength < 3) {
                $issues[] = 'Alt text too short';
                $recommendations[] = 'Use more descriptive alt text (aim for 5-125 characters)';
                $score -= 30;
            } elseif ($altLength > 125) {
                $issues[] = 'Alt text too long';
                $recommendations[] = 'Consider shortening alt text (125 characters max recommended)';
                $score -= 20;
            }

            // Check for redundant phrases
            $redundantPhrases = [
                'image of', 'picture of', 'photo of', 'graphic of', 'illustration of',
                'icon of', 'logo of', 'image showing', 'picture showing'
            ];

            foreach ($redundantPhrases as $phrase) {
                if (strpos($altLower, $phrase) !== false) {
                    $issues[] = "Contains redundant phrase: '{$phrase}'";
                    $recommendations[] = "Remove redundant phrases like '{$phrase}' - screen readers already announce it's an image";
                    $score -= 15;
                    break;
                }
            }

            // Check for keyword stuffing (repeated words)
            $words = str_word_count($altLower, 1);
            $wordCounts = array_count_values($words);
            $repeatedWords = array_filter($wordCounts, function($count) { return $count > 2; });

            if (!empty($repeatedWords)) {
                $issues[] = 'Possible keyword stuffing detected';
                $recommendations[] = 'Avoid repeating keywords unnecessarily in alt text';
                $score -= 25;
            }

            // Check for filename-based alt text
            $fileName = basename(parse_url($src, PHP_URL_PATH), '.' . pathinfo($src, PATHINFO_EXTENSION));
            $cleanFileName = preg_replace('/[^a-z0-9]/', '', strtolower($fileName));
            $cleanAlt = preg_replace('/[^a-z0-9]/', '', $altLower);

            if ($cleanAlt === $cleanFileName || similar_text($cleanAlt, $cleanFileName) > strlen($cleanAlt) * 0.8) {
                $issues[] = 'Alt text appears to be based on filename';
                $recommendations[] = 'Replace filename-based alt text with meaningful description';
                $score -= 40;
            }

            // Check for generic text
            $genericTexts = [
                'image', 'picture', 'photo', 'graphic', 'icon', 'logo', 'banner',
                'click here', 'read more', 'untitled', 'img', 'placeholder'
            ];

            if (in_array($altLower, $genericTexts)) {
                $issues[] = 'Generic or non-descriptive alt text';
                $recommendations[] = 'Replace with specific, meaningful description of the image content';
                $score -= 50;
            }

            // Determine final status
            if (!empty($issues)) {
                $status = $score < 50 ? 'poor' : 'needs_improvement';
            }
        }

        // Positive recommendations for good alt text
        if (empty($issues)) {
            $recommendations[] = 'Alt text looks good! It\'s descriptive and appropriate length.';
        }

        return [
            'score' => max(0, $score),
            'status' => $status,
            'issues' => $issues,
            'recommendations' => $recommendations,
            'length' => strlen($alt),
            'word_count' => str_word_count($alt)
        ];
    }

    /**
     * Get context around an image element.
     */
    private function getImageContext(DOMElement $img, DOMXPath $xpath): array
    {
        $context = [
            'parent_tag' => $img->parentNode ? $img->parentNode->nodeName : null,
            'parent_text' => '',
            'surrounding_text' => '',
            'caption' => '',
            'aria_label' => $img->getAttribute('aria-label'),
            'aria_describedby' => $img->getAttribute('aria-describedby'),
        ];

        // Get parent element text
        if ($img->parentNode) {
            $parentText = trim($img->parentNode->textContent);
            $context['parent_text'] = substr($parentText, 0, 200);
        }

        // Look for figure/figcaption
        $figureParent = $img->parentNode;
        while ($figureParent && $figureParent->nodeName !== 'figure') {
            $figureParent = $figureParent->parentNode;
            if (!$figureParent || $figureParent->nodeName === 'body') break;
        }

        if ($figureParent && $figureParent->nodeName === 'figure') {
            $captions = $xpath->query('.//figcaption', $figureParent);
            if ($captions->length > 0) {
                $context['caption'] = trim($captions->item(0)->textContent);
            }
        }

        return $context;
    }

    /**
     * Analyze overall image data statistics.
     */
    private function analyzeImageData(array $images): array
    {
        $stats = [
            'total_images' => count($images),
            'images_without_alt' => 0,
            'images_with_empty_alt' => 0,
            'images_with_good_alt' => 0,
            'images_with_issues' => 0,
        ];

        foreach ($images as $image) {
            $status = $image['analysis']['status'];
            
            switch ($status) {
                case 'missing':
                    $stats['images_without_alt']++;
                    break;
                case 'good':
                    $stats['images_with_good_alt']++;
                    break;
                case 'needs_improvement':
                case 'poor':
                    $stats['images_with_issues']++;
                    break;
            }

            // Check for empty alt specifically
            if (isset($image['alt']) && $image['alt'] === '') {
                $stats['images_with_empty_alt']++;
            }
        }

        return $stats;
    }

    /**
     * Discover additional pages to crawl from the main page.
     */
    private function discoverPages(string $baseUrl, int $maxPages): array
    {
        $pages = [$baseUrl];
        
        try {
            $response = Http::withHeaders(['User-Agent' => $this->userAgent])
                ->timeout(15)
                ->get($baseUrl);

            if (!$response->successful()) {
                return $pages;
            }

            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($response->body());
            libxml_clear_errors();

            $xpath = new DOMXPath($dom);
            $links = $xpath->query('//a[@href]');

            $baseDomain = parse_url($baseUrl, PHP_URL_HOST);
            $discoveredUrls = [];

            foreach ($links as $link) {
                $href = $link->getAttribute('href');
                $absoluteUrl = $this->makeAbsoluteUrl($href, $baseUrl);
                
                // Only include same-domain URLs
                if (parse_url($absoluteUrl, PHP_URL_HOST) === $baseDomain) {
                    $discoveredUrls[] = $absoluteUrl;
                }

                if (count($discoveredUrls) >= $maxPages - 1) {
                    break;
                }
            }

            // Remove duplicates and add to pages
            $discoveredUrls = array_unique($discoveredUrls);
            $pages = array_merge($pages, array_slice($discoveredUrls, 0, $maxPages - 1));

        } catch (\Exception $e) {
            Log::warning('Failed to discover additional pages', [
                'url' => $baseUrl,
                'error' => $e->getMessage()
            ]);
        }

        return array_unique($pages);
    }

    /**
     * Convert relative URL to absolute URL.
     */
    private function makeAbsoluteUrl(string $url, string $baseUrl): string
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        }

        $baseComponents = parse_url($baseUrl);
        
        if (strpos($url, '//') === 0) {
            return $baseComponents['scheme'] . ':' . $url;
        }

        if (strpos($url, '/') === 0) {
            return $baseComponents['scheme'] . '://' . $baseComponents['host'] . 
                   (isset($baseComponents['port']) ? ':' . $baseComponents['port'] : '') . $url;
        }

        $basePath = isset($baseComponents['path']) ? dirname($baseComponents['path']) : '';
        if ($basePath === '.') $basePath = '';

        return $baseComponents['scheme'] . '://' . $baseComponents['host'] . 
               (isset($baseComponents['port']) ? ':' . $baseComponents['port'] : '') . 
               $basePath . '/' . $url;
    }

    /**
     * Generate demo data for testing.
     */
    public function generateDemoData(): array
    {
        $demoImages = [
            [
                'src' => 'https://example.com/hero-banner.jpg',
                'original_src' => '/images/hero-banner.jpg',
                'alt' => 'Team of professionals working together in modern office',
                'title' => '',
                'width' => '800',
                'height' => '400',
                'context' => [
                    'parent_tag' => 'div',
                    'parent_text' => 'Welcome to our company',
                    'caption' => '',
                ],
                'analysis' => [
                    'score' => 95,
                    'status' => 'good',
                    'issues' => [],
                    'recommendations' => ['Alt text looks good! It\'s descriptive and appropriate length.'],
                    'length' => 52,
                    'word_count' => 8
                ],
                'page_url' => 'https://example.com',
                'file_name' => 'hero-banner.jpg',
                'file_extension' => 'jpg',
            ],
            [
                'src' => 'https://example.com/product.png',
                'original_src' => '/images/product.png',
                'alt' => '',
                'title' => '',
                'width' => '300',
                'height' => '300',
                'context' => [
                    'parent_tag' => 'a',
                    'parent_text' => 'Buy now',
                    'caption' => '',
                ],
                'analysis' => [
                    'score' => 0,
                    'status' => 'missing',
                    'issues' => ['Missing or empty alt attribute'],
                    'recommendations' => ['Add descriptive alt text that explains what the image shows'],
                    'length' => 0,
                    'word_count' => 0
                ],
                'page_url' => 'https://example.com',
                'file_name' => 'product.png',
                'file_extension' => 'png',
            ],
            [
                'src' => 'https://example.com/logo.svg',
                'original_src' => '/images/logo.svg',
                'alt' => 'logo logo logo company logo',
                'title' => '',
                'width' => '150',
                'height' => '50',
                'context' => [
                    'parent_tag' => 'header',
                    'parent_text' => 'Company Name',
                    'caption' => '',
                ],
                'analysis' => [
                    'score' => 60,
                    'status' => 'needs_improvement',
                    'issues' => ['Possible keyword stuffing detected'],
                    'recommendations' => ['Avoid repeating keywords unnecessarily in alt text'],
                    'length' => 27,
                    'word_count' => 5
                ],
                'page_url' => 'https://example.com',
                'file_name' => 'logo.svg',
                'file_extension' => 'svg',
            ]
        ];

        $stats = [
            'total_images' => 3,
            'images_without_alt' => 1,
            'images_with_empty_alt' => 1,
            'images_with_good_alt' => 1,
            'images_with_issues' => 1,
        ];

        return [
            'success' => true,
            'data' => [
                'url' => 'https://example.com',
                'page_title' => 'Example Website - Demo',
                'total_images' => $stats['total_images'],
                'images_without_alt' => $stats['images_without_alt'],
                'images_with_empty_alt' => $stats['images_with_empty_alt'],
                'images_with_good_alt' => $stats['images_with_good_alt'],
                'images_with_issues' => $stats['images_with_issues'],
                'images_data' => $demoImages,
                'crawl_summary' => [
                    [
                        'url' => 'https://example.com',
                        'total_images' => 3,
                        'page_title' => 'Example Website - Demo',
                        'status' => 'success'
                    ]
                ],
                'pages_crawled' => 1,
                'is_multi_page' => false,
                'analyzed_at' => Carbon::now(),
            ],
            'processing_time' => 1.5,
            'message' => 'Demo analysis completed successfully.'
        ];
    }
}
