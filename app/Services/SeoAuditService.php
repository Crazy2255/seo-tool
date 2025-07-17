<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use DOMDocument;
use DOMXPath;

class SeoAuditService
{
    protected Client $httpClient;
    protected array $userAgents = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
        'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
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
     * Perform comprehensive SEO audit
     */
    public function performAudit(string $url): array
    {
        try {
            $startTime = microtime(true);
            
            // Validate URL
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                throw new \Exception('Invalid URL provided');
            }
            
            // Fetch page content
            $response = $this->httpClient->get($url, [
                'timeout' => 30,
                'http_errors' => false // Don't throw exceptions for HTTP errors
            ]);
            $html = $response->getBody()->getContents();
            $statusCode = $response->getStatusCode();
            
            $endTime = microtime(true);
            $pageLoadSpeed = round(($endTime - $startTime) * 1000, 2); // milliseconds

            // Parse HTML
            $dom = new DOMDocument();
            libxml_use_internal_errors(true); // Suppress HTML parsing warnings
            @$dom->loadHTML($html);
            libxml_clear_errors();
            $xpath = new DOMXPath($dom);

            // Extract SEO data
            $seoData = [
                'url' => $url,
                'status_code' => $statusCode,
                'page_load_speed' => $pageLoadSpeed,
                'title' => $this->extractTitle($xpath),
                'meta_description' => $this->extractMetaDescription($xpath),
                'meta_keywords' => $this->extractMetaKeywords($xpath),
                'h1_tags' => $this->extractH1Tags($xpath),
                'h2_tags' => $this->extractH2Tags($xpath),
                'canonical_url' => $this->extractCanonicalUrl($xpath),
                'robots_meta' => $this->extractRobotsMeta($xpath),
                'internal_links_count' => $this->countInternalLinks($xpath, $url),
                'external_links_count' => $this->countExternalLinks($xpath, $url),
                'images_count' => $this->countImages($xpath),
                'images_without_alt' => $this->countImagesWithoutAlt($xpath),
                'word_count' => $this->countWords($html),
                'ssl_certificate' => $this->checkSSL($url),
                'mobile_friendly' => $this->checkMobileFriendly($xpath),
                'schema_markup' => $this->checkSchemaMarkup($html),
                'social_meta_tags' => $this->extractSocialMetaTags($xpath),
                'sitemap_url' => $this->findSitemap($url),
                'robots_txt_status' => $this->checkRobotsTxt($url),
                'broken_links' => $this->findBrokenLinks($xpath, $url),
                'audit_score' => 0,
                'recommendations' => []
            ];

            // Calculate audit score and recommendations
            $seoData = $this->calculateAuditScore($seoData);

            return $seoData;

        } catch (RequestException $e) {
            throw new \Exception('Failed to fetch URL: ' . $e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception('Error during audit: ' . $e->getMessage());
        }
    }

    /**
     * Perform site audit with detailed analysis
     */
    public function performSiteAudit(string $url): array
    {
        $audit = $this->performAudit($url);
        
        // Additional site-specific checks
        $audit['performance_metrics'] = $this->getPerformanceMetrics($url);
        $audit['technical_seo'] = $this->getTechnicalSeoAnalysis($url);
        $audit['content_analysis'] = $this->getContentAnalysis($audit);
        
        return $audit;
    }

    /**
     * Analyze meta tags specifically
     */
    public function analyzeMetaTags(string $url): array
    {
        try {
            $response = $this->httpClient->get($url);
            $html = $response->getBody()->getContents();
            
            $dom = new DOMDocument();
            @$dom->loadHTML($html);
            $xpath = new DOMXPath($dom);

            return [
                'title' => [
                    'content' => $this->extractTitle($xpath),
                    'length' => strlen($this->extractTitle($xpath)),
                    'recommendations' => $this->getTitleRecommendations($this->extractTitle($xpath))
                ],
                'meta_description' => [
                    'content' => $this->extractMetaDescription($xpath),
                    'length' => strlen($this->extractMetaDescription($xpath)),
                    'recommendations' => $this->getMetaDescriptionRecommendations($this->extractMetaDescription($xpath))
                ],
                'meta_keywords' => $this->extractMetaKeywords($xpath),
                'canonical_url' => $this->extractCanonicalUrl($xpath),
                'robots_meta' => $this->extractRobotsMeta($xpath),
                'og_tags' => $this->extractOpenGraphTags($xpath),
                'twitter_cards' => $this->extractTwitterCards($xpath),
                'other_meta_tags' => $this->extractOtherMetaTags($xpath)
            ];

        } catch (\Exception $e) {
            throw new \Exception('Error analyzing meta tags: ' . $e->getMessage());
        }
    }

    /**
     * Check page speed performance
     */
    public function checkPageSpeed(string $url): array
    {
        $startTime = microtime(true);
        
        try {
            $response = $this->httpClient->get($url);
            $endTime = microtime(true);
            
            $loadTime = round(($endTime - $startTime) * 1000, 2);
            $contentSize = strlen($response->getBody()->getContents());
            
            return [
                'load_time_ms' => $loadTime,
                'load_time_seconds' => round($loadTime / 1000, 2),
                'content_size_bytes' => $contentSize,
                'content_size_kb' => round($contentSize / 1024, 2),
                'status_code' => $response->getStatusCode(),
                'recommendations' => $this->getSpeedRecommendations($loadTime, $contentSize),
                'performance_grade' => $this->getPerformanceGrade($loadTime)
            ];

        } catch (\Exception $e) {
            throw new \Exception('Error checking page speed: ' . $e->getMessage());
        }
    }

    /**
     * Check for broken links
     */
    public function checkBrokenLinks(string $url): array
    {
        try {
            $response = $this->httpClient->get($url);
            $html = $response->getBody()->getContents();
            
            $dom = new DOMDocument();
            @$dom->loadHTML($html);
            $xpath = new DOMXPath($dom);

            $links = $xpath->query('//a[@href]');
            $brokenLinks = [];
            $totalLinks = $links->length;
            $checkedLinks = 0;

            foreach ($links as $link) {
                $href = $link->getAttribute('href');
                
                // Convert relative URLs to absolute
                $absoluteUrl = $this->convertToAbsoluteUrl($href, $url);
                
                if ($absoluteUrl && $this->isValidUrl($absoluteUrl)) {
                    $status = $this->checkLinkStatus($absoluteUrl);
                    
                    if ($status >= 400) {
                        $brokenLinks[] = [
                            'url' => $absoluteUrl,
                            'anchor_text' => trim($link->textContent),
                            'status_code' => $status,
                            'link_type' => $this->getLinkType($absoluteUrl, $url)
                        ];
                    }
                    $checkedLinks++;
                }
            }

            return [
                'total_links' => $totalLinks,
                'checked_links' => $checkedLinks,
                'broken_links_count' => count($brokenLinks),
                'broken_links' => $brokenLinks,
                'health_percentage' => $checkedLinks > 0 ? round((($checkedLinks - count($brokenLinks)) / $checkedLinks) * 100, 1) : 100
            ];

        } catch (\Exception $e) {
            throw new \Exception('Error checking broken links: ' . $e->getMessage());
        }
    }

    // Private helper methods

    private function extractTitle(DOMXPath $xpath): string
    {
        $titleNodes = $xpath->query('//title');
        return $titleNodes->length > 0 ? trim($titleNodes->item(0)->textContent) : '';
    }

    private function extractMetaDescription(DOMXPath $xpath): string
    {
        $metaNodes = $xpath->query('//meta[@name="description"]/@content');
        return $metaNodes->length > 0 ? trim($metaNodes->item(0)->textContent) : '';
    }

    private function extractMetaKeywords(DOMXPath $xpath): string
    {
        $metaNodes = $xpath->query('//meta[@name="keywords"]/@content');
        return $metaNodes->length > 0 ? trim($metaNodes->item(0)->textContent) : '';
    }

    private function extractH1Tags(DOMXPath $xpath): array
    {
        $h1Nodes = $xpath->query('//h1');
        $h1Tags = [];
        foreach ($h1Nodes as $node) {
            $h1Tags[] = trim($node->textContent);
        }
        return $h1Tags;
    }

    private function extractH2Tags(DOMXPath $xpath): array
    {
        $h2Nodes = $xpath->query('//h2');
        $h2Tags = [];
        foreach ($h2Nodes as $node) {
            $h2Tags[] = trim($node->textContent);
        }
        return array_slice($h2Tags, 0, 10); // Limit to first 10
    }

    private function extractCanonicalUrl(DOMXPath $xpath): string
    {
        $canonicalNodes = $xpath->query('//link[@rel="canonical"]/@href');
        return $canonicalNodes->length > 0 ? trim($canonicalNodes->item(0)->textContent) : '';
    }

    private function extractRobotsMeta(DOMXPath $xpath): string
    {
        $robotsNodes = $xpath->query('//meta[@name="robots"]/@content');
        return $robotsNodes->length > 0 ? trim($robotsNodes->item(0)->textContent) : '';
    }

    private function countInternalLinks(DOMXPath $xpath, string $baseUrl): int
    {
        $domain = parse_url($baseUrl, PHP_URL_HOST);
        $links = $xpath->query('//a[@href]');
        $count = 0;
        
        foreach ($links as $link) {
            $href = $link->getAttribute('href');
            if (strpos($href, $domain) !== false || strpos($href, '/') === 0) {
                $count++;
            }
        }
        
        return $count;
    }

    private function countExternalLinks(DOMXPath $xpath, string $baseUrl): int
    {
        $domain = parse_url($baseUrl, PHP_URL_HOST);
        $links = $xpath->query('//a[@href]');
        $count = 0;
        
        foreach ($links as $link) {
            $href = $link->getAttribute('href');
            if (filter_var($href, FILTER_VALIDATE_URL) && strpos($href, $domain) === false) {
                $count++;
            }
        }
        
        return $count;
    }

    private function countImages(DOMXPath $xpath): int
    {
        return $xpath->query('//img')->length;
    }

    private function countImagesWithoutAlt(DOMXPath $xpath): int
    {
        return $xpath->query('//img[not(@alt) or @alt=""]')->length;
    }

    private function countWords(string $html): int
    {
        $text = strip_tags($html);
        $text = preg_replace('/\s+/', ' ', $text);
        return str_word_count($text);
    }

    private function checkSSL(string $url): bool
    {
        return strpos($url, 'https://') === 0;
    }

    private function checkMobileFriendly(DOMXPath $xpath): bool
    {
        $viewport = $xpath->query('//meta[@name="viewport"]');
        return $viewport->length > 0;
    }

    private function checkSchemaMarkup(string $html): array
    {
        $schemas = [];
        
        // Check for JSON-LD
        if (preg_match_all('/<script[^>]*type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/is', $html, $matches)) {
            foreach ($matches[1] as $jsonLd) {
                $decoded = json_decode(trim($jsonLd), true);
                if ($decoded && isset($decoded['@type'])) {
                    $schemas[] = $decoded['@type'];
                }
            }
        }
        
        return array_unique($schemas);
    }

    private function extractSocialMetaTags(DOMXPath $xpath): array
    {
        return [
            'og_tags' => $this->extractOpenGraphTags($xpath),
            'twitter_cards' => $this->extractTwitterCards($xpath)
        ];
    }

    private function extractOpenGraphTags(DOMXPath $xpath): array
    {
        $ogTags = [];
        $nodes = $xpath->query('//meta[starts-with(@property, "og:")]');
        
        foreach ($nodes as $node) {
            $property = $node->getAttribute('property');
            $content = $node->getAttribute('content');
            $ogTags[$property] = $content;
        }
        
        return $ogTags;
    }

    private function extractTwitterCards(DOMXPath $xpath): array
    {
        $twitterTags = [];
        $nodes = $xpath->query('//meta[starts-with(@name, "twitter:")]');
        
        foreach ($nodes as $node) {
            $name = $node->getAttribute('name');
            $content = $node->getAttribute('content');
            $twitterTags[$name] = $content;
        }
        
        return $twitterTags;
    }

    private function extractOtherMetaTags(DOMXPath $xpath): array
    {
        $metaTags = [];
        $nodes = $xpath->query('//meta[@name and @content]');
        
        foreach ($nodes as $node) {
            $name = $node->getAttribute('name');
            $content = $node->getAttribute('content');
            
            if (!in_array($name, ['description', 'keywords', 'robots', 'viewport'])) {
                $metaTags[$name] = $content;
            }
        }
        
        return $metaTags;
    }

    private function findSitemap(string $url): string
    {
        $baseUrl = parse_url($url, PHP_URL_SCHEME) . '://' . parse_url($url, PHP_URL_HOST);
        $sitemapUrl = $baseUrl . '/sitemap.xml';
        
        try {
            $response = $this->httpClient->head($sitemapUrl);
            return $response->getStatusCode() === 200 ? $sitemapUrl : '';
        } catch (\Exception $e) {
            return '';
        }
    }

    private function checkRobotsTxt(string $url): string
    {
        $baseUrl = parse_url($url, PHP_URL_SCHEME) . '://' . parse_url($url, PHP_URL_HOST);
        $robotsUrl = $baseUrl . '/robots.txt';
        
        try {
            $response = $this->httpClient->get($robotsUrl);
            return $response->getStatusCode() === 200 ? 'found' : 'not_found';
        } catch (\Exception $e) {
            return 'not_found';
        }
    }

    private function findBrokenLinks(DOMXPath $xpath, string $baseUrl): array
    {
        // This is a simplified version - the full implementation is in checkBrokenLinks method
        return [];
    }

    private function calculateAuditScore(array $seoData): array
    {
        $score = 0;
        $recommendations = [];
        
        // Title optimization (15 points)
        if (!empty($seoData['title'])) {
            $titleLength = strlen($seoData['title']);
            if ($titleLength >= 30 && $titleLength <= 60) {
                $score += 15;
            } else {
                $score += 5;
                $recommendations[] = 'Optimize title length (30-60 characters recommended)';
            }
        } else {
            $recommendations[] = 'Add a page title';
        }
        
        // Meta description (15 points)
        if (!empty($seoData['meta_description'])) {
            $descLength = strlen($seoData['meta_description']);
            if ($descLength >= 120 && $descLength <= 160) {
                $score += 15;
            } else {
                $score += 5;
                $recommendations[] = 'Optimize meta description length (120-160 characters recommended)';
            }
        } else {
            $recommendations[] = 'Add a meta description';
        }
        
        // H1 tags (10 points)
        if (!empty($seoData['h1_tags'])) {
            if (count($seoData['h1_tags']) === 1) {
                $score += 10;
            } else {
                $score += 5;
                $recommendations[] = 'Use exactly one H1 tag per page';
            }
        } else {
            $recommendations[] = 'Add an H1 tag';
        }
        
        // SSL Certificate (10 points)
        if ($seoData['ssl_certificate']) {
            $score += 10;
        } else {
            $recommendations[] = 'Install SSL certificate for HTTPS';
        }
        
        // Page speed (15 points)
        if ($seoData['page_load_speed'] <= 3000) {
            $score += 15;
        } elseif ($seoData['page_load_speed'] <= 5000) {
            $score += 10;
        } else {
            $score += 5;
            $recommendations[] = 'Improve page load speed (currently ' . $seoData['page_load_speed'] . 'ms)';
        }
        
        // Images with alt text (10 points)
        if ($seoData['images_count'] > 0) {
            $altRatio = 1 - ($seoData['images_without_alt'] / $seoData['images_count']);
            $score += round($altRatio * 10);
            
            if ($seoData['images_without_alt'] > 0) {
                $recommendations[] = 'Add alt text to ' . $seoData['images_without_alt'] . ' images';
            }
        }
        
        // Mobile friendly (10 points)
        if ($seoData['mobile_friendly']) {
            $score += 10;
        } else {
            $recommendations[] = 'Make website mobile-friendly';
        }
        
        // Canonical URL (5 points)
        if (!empty($seoData['canonical_url'])) {
            $score += 5;
        } else {
            $recommendations[] = 'Add canonical URL';
        }
        
        // Schema markup (5 points)
        if (!empty($seoData['schema_markup'])) {
            $score += 5;
        } else {
            $recommendations[] = 'Add structured data (Schema markup)';
        }
        
        // Sitemap (5 points)
        if (!empty($seoData['sitemap_url'])) {
            $score += 5;
        } else {
            $recommendations[] = 'Create and submit XML sitemap';
        }
        
        $seoData['audit_score'] = $score;
        $seoData['recommendations'] = $recommendations;
        
        return $seoData;
    }

    private function getTitleRecommendations(string $title): array
    {
        $recommendations = [];
        $length = strlen($title);
        
        if (empty($title)) {
            $recommendations[] = 'Add a page title';
        } elseif ($length < 30) {
            $recommendations[] = 'Title is too short. Consider adding more descriptive text.';
        } elseif ($length > 60) {
            $recommendations[] = 'Title is too long. It may be truncated in search results.';
        } else {
            $recommendations[] = 'Title length is optimal.';
        }
        
        return $recommendations;
    }

    private function getMetaDescriptionRecommendations(string $description): array
    {
        $recommendations = [];
        $length = strlen($description);
        
        if (empty($description)) {
            $recommendations[] = 'Add a meta description';
        } elseif ($length < 120) {
            $recommendations[] = 'Meta description is too short. Consider adding more details.';
        } elseif ($length > 160) {
            $recommendations[] = 'Meta description is too long. It may be truncated in search results.';
        } else {
            $recommendations[] = 'Meta description length is optimal.';
        }
        
        return $recommendations;
    }

    private function getSpeedRecommendations(float $loadTime, int $contentSize): array
    {
        $recommendations = [];
        
        if ($loadTime > 3000) {
            $recommendations[] = 'Page load time is slow. Consider optimizing images and reducing HTTP requests.';
        }
        
        if ($contentSize > 1024 * 1024) { // 1MB
            $recommendations[] = 'Page size is large. Consider compressing images and minifying CSS/JS.';
        }
        
        if ($loadTime <= 2000) {
            $recommendations[] = 'Excellent page load speed!';
        }
        
        return $recommendations;
    }

    private function getPerformanceGrade(float $loadTime): string
    {
        if ($loadTime <= 1500) return 'A';
        if ($loadTime <= 2500) return 'B';
        if ($loadTime <= 4000) return 'C';
        if ($loadTime <= 6000) return 'D';
        return 'F';
    }

    private function convertToAbsoluteUrl(string $url, string $baseUrl): string
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        }
        
        if (strpos($url, '/') === 0) {
            $parsed = parse_url($baseUrl);
            return $parsed['scheme'] . '://' . $parsed['host'] . $url;
        }
        
        return '';
    }

    private function isValidUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    private function checkLinkStatus(string $url): int
    {
        try {
            $response = $this->httpClient->head($url, [
                'timeout' => 10,
                'http_errors' => false // Don't throw exceptions for HTTP errors
            ]);
            return $response->getStatusCode();
        } catch (\Exception $e) {
            // Return 0 for connection errors, timeouts, etc.
            return 0;
        }
    }

    private function getLinkType(string $url, string $baseUrl): string
    {
        $baseDomain = parse_url($baseUrl, PHP_URL_HOST);
        $linkDomain = parse_url($url, PHP_URL_HOST);
        
        return $baseDomain === $linkDomain ? 'internal' : 'external';
    }

    private function getPerformanceMetrics(string $url): array
    {
        // Additional performance metrics can be added here
        return [
            'compression_enabled' => $this->checkGzipCompression($url),
            'cache_headers' => $this->checkCacheHeaders($url),
            'minification_opportunities' => $this->checkMinificationOpportunities($url)
        ];
    }

    private function getTechnicalSeoAnalysis(string $url): array
    {
        return [
            'url_structure' => $this->analyzeUrlStructure($url),
            'redirect_chain' => $this->checkRedirectChain($url),
            'hreflang_tags' => $this->checkHreflangTags($url)
        ];
    }

    private function getContentAnalysis(array $auditData): array
    {
        return [
            'content_quality_score' => $this->calculateContentQuality($auditData),
            'keyword_density' => $this->analyzeKeywordDensity($auditData),
            'readability_score' => $this->calculateReadabilityScore($auditData)
        ];
    }

    // Additional helper methods for extended functionality
    private function checkGzipCompression(string $url): bool
    {
        try {
            $response = $this->httpClient->head($url);
            $encoding = $response->getHeaderLine('Content-Encoding');
            return strpos($encoding, 'gzip') !== false;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function checkCacheHeaders(string $url): array
    {
        try {
            $response = $this->httpClient->head($url);
            return [
                'cache_control' => $response->getHeaderLine('Cache-Control'),
                'expires' => $response->getHeaderLine('Expires'),
                'etag' => $response->getHeaderLine('ETag')
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    private function checkMinificationOpportunities(string $url): array
    {
        // Simplified implementation
        return [
            'css_minification' => 'Check CSS files for minification opportunities',
            'js_minification' => 'Check JavaScript files for minification opportunities',
            'html_minification' => 'Check HTML for minification opportunities'
        ];
    }

    private function analyzeUrlStructure(string $url): array
    {
        $parsed = parse_url($url);
        $path = $parsed['path'] ?? '/';
        
        return [
            'is_friendly' => strpos($path, '?') === false && strpos($path, '&') === false,
            'length' => strlen($url),
            'has_trailing_slash' => substr($path, -1) === '/',
            'depth' => substr_count(trim($path, '/'), '/') + 1
        ];
    }

    private function checkRedirectChain(string $url): array
    {
        try {
            // Configure Guzzle to track redirects
            $response = $this->httpClient->get($url, [
                'allow_redirects' => [
                    'max' => 10,
                    'strict' => true,
                    'referer' => true,
                    'track_redirects' => true
                ]
            ]);
            
            // Get redirect history from headers
            $redirectHistory = $response->getHeader('X-Guzzle-Redirect-History');
            $redirectStatusHistory = $response->getHeader('X-Guzzle-Redirect-Status-History');
            
            return [
                'redirect_count' => count($redirectHistory),
                'final_url' => $url, // Since we don't have getEffectiveUri(), use the original URL
                'redirect_chain' => array_map(function($uri, $status) {
                    return ['uri' => $uri, 'status' => $status ?? 'unknown'];
                }, $redirectHistory, $redirectStatusHistory ?: [])
            ];
        } catch (\Exception $e) {
            return [
                'redirect_count' => 0,
                'final_url' => $url,
                'redirect_chain' => []
            ];
        }
    }

    private function checkHreflangTags(string $url): array
    {
        try {
            $response = $this->httpClient->get($url);
            $html = $response->getBody()->getContents();
            
            $dom = new DOMDocument();
            @$dom->loadHTML($html);
            $xpath = new DOMXPath($dom);
            
            $hreflangTags = [];
            $nodes = $xpath->query('//link[@rel="alternate" and @hreflang]');
            
            foreach ($nodes as $node) {
                $hreflangTags[] = [
                    'hreflang' => $node->getAttribute('hreflang'),
                    'href' => $node->getAttribute('href')
                ];
            }
            
            return $hreflangTags;
        } catch (\Exception $e) {
            return [];
        }
    }

    private function calculateContentQuality(array $auditData): float
    {
        $score = 0;
        
        // Word count scoring
        if ($auditData['word_count'] >= 300) $score += 25;
        elseif ($auditData['word_count'] >= 150) $score += 15;
        else $score += 5;
        
        // Header structure scoring
        if (!empty($auditData['h1_tags']) && count($auditData['h1_tags']) === 1) $score += 25;
        if (!empty($auditData['h2_tags'])) $score += 20;
        
        // Meta data scoring
        if (!empty($auditData['title'])) $score += 15;
        if (!empty($auditData['meta_description'])) $score += 15;
        
        return round($score, 1);
    }

    private function analyzeKeywordDensity(array $auditData): array
    {
        // Simplified keyword density analysis
        $title = $auditData['title'] ?? '';
        $metaDesc = $auditData['meta_description'] ?? '';
        
        $words = array_merge(
            explode(' ', strtolower($title)),
            explode(' ', strtolower($metaDesc))
        );
        
        $wordCount = array_count_values(array_filter($words, function($word) {
            return strlen($word) > 3;
        }));
        
        arsort($wordCount);
        
        return array_slice($wordCount, 0, 10, true);
    }

    private function calculateReadabilityScore(array $auditData): float
    {
        // Simplified readability calculation based on content structure
        $score = 50; // Base score
        
        if (!empty($auditData['h1_tags'])) $score += 10;
        if (!empty($auditData['h2_tags'])) $score += 10;
        if ($auditData['word_count'] >= 300) $score += 15;
        if ($auditData['word_count'] <= 2000) $score += 15;
        
        return min(100, round($score, 1));
    }
}
