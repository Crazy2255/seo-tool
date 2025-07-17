<?php

namespace App\Services;

use App\Models\MetaTagAudit;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Log;

class MetaAnalyzerService
{
    protected Client $httpClient;
    protected array $userAgents = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
    ];

    public function __construct()
    {
        $this->httpClient = new Client([
            'timeout' => 30,
            'verify' => false,
            'headers' => [
                'User-Agent' => $this->userAgents[array_rand($this->userAgents)]
            ]
        ]);
    }

    /**
     * Analyze meta tags for a given URL
     */
    public function analyzeUrl(string $url, ?int $userId = null): array
    {
        try {
            // Validate and normalize URL
            $cleanUrl = $this->normalizeUrl($url);
            
            // Fetch HTML content
            $htmlContent = $this->fetchHtmlContent($cleanUrl);
            
            // Parse HTML and extract meta tags
            $metaData = $this->parseHtmlContent($htmlContent, $cleanUrl);
            
            // Calculate score and recommendations
            $metaData['score'] = $this->calculateScore($metaData);
            $metaData['recommendations'] = $this->generateRecommendations($metaData);
            $metaData['issues_found'] = $this->identifyIssues($metaData);
            
            // Save to database if user is provided
            if ($userId) {
                $this->saveAudit($metaData, $userId, $cleanUrl);
            }
            
            return $metaData;
            
        } catch (\Exception $e) {
            Log::error('Meta analyzer error: ' . $e->getMessage());
            throw new \Exception('Failed to analyze URL: ' . $e->getMessage());
        }
    }

    /**
     * Normalize URL format
     */
    private function normalizeUrl(string $url): string
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            if (!preg_match('/^https?:\/\//', $url)) {
                $url = 'https://' . $url;
            }
        }
        
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Invalid URL format');
        }
        
        return $url;
    }

    /**
     * Fetch HTML content from URL
     */
    private function fetchHtmlContent(string $url): string
    {
        try {
            $response = $this->httpClient->get($url, [
                'headers' => [
                    'User-Agent' => $this->userAgents[array_rand($this->userAgents)],
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language' => 'en-US,en;q=0.5',
                    'Accept-Encoding' => 'gzip, deflate',
                    'Connection' => 'keep-alive'
                ],
                'timeout' => 30
            ]);

            $statusCode = $response->getStatusCode();
            if ($statusCode !== 200) {
                throw new \Exception("HTTP {$statusCode} received");
            }

            return $response->getBody()->getContents();
            
        } catch (RequestException $e) {
            throw new \Exception('Failed to fetch URL: ' . $e->getMessage());
        }
    }

    /**
     * Parse HTML content and extract meta tags
     */
    private function parseHtmlContent(string $html, string $url): array
    {
        // Clean and prepare HTML
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        @$dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        
        $xpath = new DOMXPath($dom);
        
        $metaData = [
            'url' => $url,
            'status_code' => 200,
            'analyzed_at' => now()
        ];

        // Extract title tag
        $titleNodes = $xpath->query('//title');
        $metaData['title'] = $titleNodes->length > 0 ? trim($titleNodes->item(0)->textContent) : null;
        $metaData['title_length'] = strlen($metaData['title'] ?? '');

        // Extract meta description
        $descriptionNode = $xpath->query('//meta[@name="description"]/@content');
        $metaData['meta_description'] = $descriptionNode->length > 0 ? trim($descriptionNode->item(0)->value) : null;
        $metaData['meta_description_length'] = strlen($metaData['meta_description'] ?? '');

        // Extract meta keywords
        $keywordsNode = $xpath->query('//meta[@name="keywords"]/@content');
        $metaData['meta_keywords'] = $keywordsNode->length > 0 ? trim($keywordsNode->item(0)->value) : null;

        // Extract canonical URL
        $canonicalNode = $xpath->query('//link[@rel="canonical"]/@href');
        $metaData['canonical_url'] = $canonicalNode->length > 0 ? trim($canonicalNode->item(0)->value) : null;

        // Extract robots directive
        $robotsNode = $xpath->query('//meta[@name="robots"]/@content');
        $metaData['robots'] = $robotsNode->length > 0 ? trim($robotsNode->item(0)->value) : null;

        // Extract viewport
        $viewportNode = $xpath->query('//meta[@name="viewport"]/@content');
        $metaData['viewport'] = $viewportNode->length > 0 ? trim($viewportNode->item(0)->value) : null;

        // Extract Open Graph tags
        $metaData['og_title'] = $this->getMetaProperty($xpath, 'og:title');
        $metaData['og_description'] = $this->getMetaProperty($xpath, 'og:description');
        $metaData['og_image'] = $this->getMetaProperty($xpath, 'og:image');
        $metaData['og_type'] = $this->getMetaProperty($xpath, 'og:type');
        $metaData['og_url'] = $this->getMetaProperty($xpath, 'og:url');

        // Extract Twitter Card tags
        $metaData['twitter_card'] = $this->getMetaProperty($xpath, 'twitter:card');
        $metaData['twitter_title'] = $this->getMetaProperty($xpath, 'twitter:title');
        $metaData['twitter_description'] = $this->getMetaProperty($xpath, 'twitter:description');
        $metaData['twitter_image'] = $this->getMetaProperty($xpath, 'twitter:image');

        // Extract heading tags
        $metaData['h1_tags'] = $this->extractHeadings($xpath, 'h1');
        $metaData['h2_tags'] = $this->extractHeadings($xpath, 'h2');

        // Image analysis
        $images = $xpath->query('//img');
        $metaData['total_images'] = $images->length;
        $metaData['img_alt_missing'] = 0;
        
        foreach ($images as $img) {
            if (!$img->hasAttribute('alt') || empty(trim($img->getAttribute('alt')))) {
                $metaData['img_alt_missing']++;
            }
        }

        return $metaData;
    }

    /**
     * Get meta property value
     */
    private function getMetaProperty(DOMXPath $xpath, string $property): ?string
    {
        $node = $xpath->query("//meta[@property='{$property}']/@content");
        if ($node->length === 0) {
            $node = $xpath->query("//meta[@name='{$property}']/@content");
        }
        return $node->length > 0 ? trim($node->item(0)->value) : null;
    }

    /**
     * Extract heading tags
     */
    private function extractHeadings(DOMXPath $xpath, string $tag): array
    {
        $headings = [];
        $nodes = $xpath->query("//{$tag}");
        
        foreach ($nodes as $node) {
            $text = trim($node->textContent);
            if (!empty($text)) {
                $headings[] = $text;
            }
        }
        
        return $headings;
    }

    /**
     * Calculate SEO score
     */
    private function calculateScore(array $metaData): int
    {
        $score = 100;

        // Title tag scoring
        if (empty($metaData['title'])) {
            $score -= 15;
        } elseif ($metaData['title_length'] < 30 || $metaData['title_length'] > 60) {
            $score -= 10;
        }

        // Meta description scoring
        if (empty($metaData['meta_description'])) {
            $score -= 15;
        } elseif ($metaData['meta_description_length'] < 120 || $metaData['meta_description_length'] > 160) {
            $score -= 10;
        }

        // Essential meta tags
        if (empty($metaData['viewport'])) $score -= 10;
        if (empty($metaData['canonical_url'])) $score -= 5;
        if (empty($metaData['robots'])) $score -= 5;

        // Open Graph tags
        if (empty($metaData['og_title'])) $score -= 8;
        if (empty($metaData['og_description'])) $score -= 8;
        if (empty($metaData['og_image'])) $score -= 7;

        // H1 tags
        $h1Count = count($metaData['h1_tags'] ?? []);
        if ($h1Count === 0) {
            $score -= 10;
        } elseif ($h1Count > 1) {
            $score -= 5;
        }

        // Image alt tags
        if ($metaData['total_images'] > 0) {
            $altMissingPercentage = ($metaData['img_alt_missing'] / $metaData['total_images']) * 100;
            if ($altMissingPercentage > 50) {
                $score -= 10;
            } elseif ($altMissingPercentage > 25) {
                $score -= 5;
            }
        }

        return max(0, $score);
    }

    /**
     * Generate recommendations
     */
    private function generateRecommendations(array $metaData): array
    {
        $recommendations = [];

        // Title recommendations
        if (empty($metaData['title'])) {
            $recommendations[] = 'Add a title tag to your page';
        } elseif ($metaData['title_length'] < 30) {
            $recommendations[] = 'Your title tag is too short. Consider making it 30-60 characters';
        } elseif ($metaData['title_length'] > 60) {
            $recommendations[] = 'Your title tag is too long. Consider shortening it to under 60 characters';
        }

        // Meta description recommendations
        if (empty($metaData['meta_description'])) {
            $recommendations[] = 'Add a meta description to your page';
        } elseif ($metaData['meta_description_length'] < 120) {
            $recommendations[] = 'Your meta description is too short. Consider making it 120-160 characters';
        } elseif ($metaData['meta_description_length'] > 160) {
            $recommendations[] = 'Your meta description is too long. Consider shortening it to under 160 characters';
        }

        // Technical recommendations
        if (empty($metaData['viewport'])) {
            $recommendations[] = 'Add a viewport meta tag for mobile responsiveness';
        }

        if (empty($metaData['canonical_url'])) {
            $recommendations[] = 'Consider adding a canonical URL to avoid duplicate content issues';
        }

        // Social media recommendations
        if (empty($metaData['og_title'])) {
            $recommendations[] = 'Add Open Graph title for better social media sharing';
        }

        if (empty($metaData['og_description'])) {
            $recommendations[] = 'Add Open Graph description for better social media sharing';
        }

        if (empty($metaData['og_image'])) {
            $recommendations[] = 'Add Open Graph image for better social media sharing';
        }

        // Structure recommendations
        $h1Count = count($metaData['h1_tags'] ?? []);
        if ($h1Count === 0) {
            $recommendations[] = 'Add an H1 tag to your page for better structure';
        } elseif ($h1Count > 1) {
            $recommendations[] = 'Use only one H1 tag per page for better SEO';
        }

        // Image recommendations
        if ($metaData['img_alt_missing'] > 0) {
            $recommendations[] = "Add alt text to {$metaData['img_alt_missing']} images for better accessibility";
        }

        return $recommendations;
    }

    /**
     * Identify issues
     */
    private function identifyIssues(array $metaData): array
    {
        $issues = [];

        if (empty($metaData['title'])) $issues[] = 'missing_title';
        if (empty($metaData['meta_description'])) $issues[] = 'missing_meta_description';
        if (empty($metaData['viewport'])) $issues[] = 'missing_viewport';
        if (count($metaData['h1_tags'] ?? []) === 0) $issues[] = 'missing_h1';
        if (count($metaData['h1_tags'] ?? []) > 1) $issues[] = 'multiple_h1';
        if ($metaData['img_alt_missing'] > 0) $issues[] = 'missing_alt_text';

        return $issues;
    }

    /**
     * Save audit to database
     */
    private function saveAudit(array $metaData, int $userId, string $url): MetaTagAudit
    {
        return MetaTagAudit::create([
            'user_id' => $userId,
            'url' => $url,
            'title' => $metaData['title'],
            'title_length' => $metaData['title_length'],
            'meta_description' => $metaData['meta_description'],
            'meta_description_length' => $metaData['meta_description_length'],
            'meta_keywords' => $metaData['meta_keywords'],
            'canonical_url' => $metaData['canonical_url'],
            'robots' => $metaData['robots'],
            'viewport' => $metaData['viewport'],
            'og_title' => $metaData['og_title'],
            'og_description' => $metaData['og_description'],
            'og_image' => $metaData['og_image'],
            'og_type' => $metaData['og_type'],
            'og_url' => $metaData['og_url'],
            'twitter_card' => $metaData['twitter_card'],
            'twitter_title' => $metaData['twitter_title'],
            'twitter_description' => $metaData['twitter_description'],
            'twitter_image' => $metaData['twitter_image'],
            'h1_tags' => $metaData['h1_tags'],
            'h2_tags' => $metaData['h2_tags'],
            'img_alt_missing' => $metaData['img_alt_missing'],
            'total_images' => $metaData['total_images'],
            'status_code' => $metaData['status_code'],
            'issues_found' => $metaData['issues_found'],
            'recommendations' => $metaData['recommendations'],
            'score' => $metaData['score'],
            'analyzed_at' => $metaData['analyzed_at']
        ]);
    }

    /**
     * Get audit history for a user
     */
    public function getAuditHistory(int $userId, int $limit = 10): array
    {
        return MetaTagAudit::where('user_id', $userId)
            ->orderBy('analyzed_at', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Delete audit by ID
     */
    public function deleteAudit(int $auditId, int $userId): bool
    {
        return MetaTagAudit::where('id', $auditId)
            ->where('user_id', $userId)
            ->delete() > 0;
    }
}
