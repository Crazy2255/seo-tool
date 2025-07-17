<?php

namespace App\Services;

use App\Models\CompetitorInsight;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use DOMDocument;
use DOMXPath;

class CompetitorAnalysisService
{
    protected $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36';
    
    public function analyzeCompetitor(string $url, int $userId): CompetitorInsight
    {
        $startTime = microtime(true);
        
        // Normalize URL
        $normalizedUrl = $this->normalizeUrl($url);
        $domain = parse_url($normalizedUrl, PHP_URL_HOST);
        
        // Create or update competitor insight record
        $insight = CompetitorInsight::updateOrCreate(
            [
                'user_id' => $userId,
                'domain' => $domain,
            ],
            [
                'url' => $normalizedUrl,
                'scan_status' => 'pending',
                'scan_error' => null,
            ]
        );
        
        try {
            // Fetch website content
            $response = Http::timeout(30)
                ->withHeaders([
                    'User-Agent' => $this->userAgent,
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                    'Accept-Language' => 'en-US,en;q=0.5',
                    'Accept-Encoding' => 'gzip, deflate',
                    'Connection' => 'keep-alive',
                ])
                ->get($normalizedUrl);
                
            if (!$response->successful()) {
                throw new \Exception("HTTP Error: " . $response->status());
            }
            
            $htmlContent = $response->body();
            
            // Parse HTML content
            $dom = new DOMDocument();
            @$dom->loadHTML($htmlContent);
            $xpath = new DOMXPath($dom);
            
            // Extract basic page information
            $title = $this->extractTitle($xpath);
            $description = $this->extractDescription($xpath);
            
            // Analyze marketing tools and strategies
            $analyticsTools = $this->detectAnalyticsTools($htmlContent, $xpath);
            $socialPixels = $this->detectSocialPixels($htmlContent, $xpath);
            $chatWidgets = $this->detectChatWidgets($htmlContent, $xpath);
            $emailMarketing = $this->detectEmailMarketing($htmlContent, $xpath);
            $advertisingNetworks = $this->detectAdvertisingNetworks($htmlContent, $xpath);
            $retargetingTools = $this->detectRetargetingTools($htmlContent, $xpath);
            $seoTools = $this->detectSeoTools($htmlContent, $xpath);
            $opengraphTags = $this->extractOpengraphTags($xpath);
            $schemaMarkup = $this->extractSchemaMarkup($htmlContent, $xpath);
            $utmParameters = $this->detectUtmParameters($htmlContent, $xpath);
            $blogAnalysis = $this->analyzeBlogContent($normalizedUrl, $xpath, $htmlContent);
            $contentStrategy = $this->analyzeContentStrategy($xpath);
            $performanceTools = $this->detectPerformanceTools($htmlContent, $xpath);
            $securityTools = $this->detectSecurityTools($htmlContent, $xpath);
            $cmsDetection = $this->detectCms($htmlContent, $xpath);
            
            // Analyze backlinks and keywords
            $backlinkData = $this->analyzeBacklinks($normalizedUrl, $xpath, $htmlContent);
            $keywordData = $this->analyzeKeywords($normalizedUrl, $xpath, $htmlContent);
            
            // Generate strategy summary and insights
            $strategySummary = $this->generateStrategySummary([
                'analytics_tools' => $analyticsTools,
                'social_pixels' => $socialPixels,
                'chat_widgets' => $chatWidgets,
                'email_marketing' => $emailMarketing,
                'advertising_networks' => $advertisingNetworks,
                'retargeting_tools' => $retargetingTools,
                'seo_tools' => $seoTools,
                'opengraph_tags' => $opengraphTags,
                'schema_markup' => $schemaMarkup,
                'utm_parameters' => $utmParameters,
                'blog_analysis' => $blogAnalysis,
                'content_strategy' => $contentStrategy,
                'performance_tools' => $performanceTools,
                'security_tools' => $securityTools,
                'cms_detection' => $cmsDetection,
                'backlinks' => $backlinkData,
                'keywords' => $keywordData,
            ]);
            
            $actionableInsights = $this->generateActionableInsights($strategySummary);
            $totalToolsDetected = $this->countTotalTools($strategySummary);
            $strategyScore = $this->calculateStrategyScore($strategySummary);
            
            $scanDuration = round(microtime(true) - $startTime, 2);
            
            // Update the insight record
            $insight->update([
                'title' => $title,
                'description' => $description,
                'analytics_tools' => $analyticsTools,
                'social_pixels' => $socialPixels,
                'chat_widgets' => $chatWidgets,
                'email_marketing' => $emailMarketing,
                'advertising_networks' => $advertisingNetworks,
                'retargeting_tools' => $retargetingTools,
                'seo_tools' => $seoTools,
                'opengraph_tags' => $opengraphTags,
                'schema_markup' => $schemaMarkup,
                'utm_parameters' => $utmParameters,
                'blog_analysis' => $blogAnalysis,
                'content_strategy' => $contentStrategy,
                'performance_tools' => $performanceTools,
                'security_tools' => $securityTools,
                'cms_detection' => $cmsDetection,
                'backlinks_data' => $backlinkData,
                'total_backlinks' => $backlinkData['total_backlinks'] ?? 0,
                'keywords_data' => $keywordData,
                'total_keywords' => $keywordData['total_keywords'] ?? 0,
                'strategy_summary' => $strategySummary,
                'actionable_insights' => $actionableInsights,
                'total_tools_detected' => $totalToolsDetected,
                'strategy_score' => $strategyScore,
                'last_scanned_at' => now(),
                'scan_duration_seconds' => $scanDuration,
                'scan_status' => 'completed',
            ]);
            
        } catch (\Exception $e) {
            Log::error("Competitor analysis failed for {$normalizedUrl}: " . $e->getMessage());
            
            $insight->update([
                'scan_status' => 'failed',
                'scan_error' => $e->getMessage(),
                'scan_duration_seconds' => round(microtime(true) - $startTime, 2),
            ]);
        }
        
        return $insight->fresh();
    }
    
    protected function normalizeUrl(string $url): string
    {
        if (!str_starts_with($url, 'http://') && !str_starts_with($url, 'https://')) {
            $url = 'https://' . $url;
        }
        
        return rtrim($url, '/');
    }
    
    protected function extractTitle(DOMXPath $xpath): ?string
    {
        $titleNode = $xpath->query('//title')->item(0);
        return $titleNode ? trim($titleNode->textContent) : null;
    }
    
    protected function extractDescription(DOMXPath $xpath): ?string
    {
        $descNode = $xpath->query('//meta[@name="description"]/@content')->item(0);
        return $descNode ? trim($descNode->textContent) : null;
    }
    
    protected function detectAnalyticsTools(string $html, DOMXPath $xpath): array
    {
        $tools = [];
        
        // Google Analytics
        if (preg_match('/gtag\(|GoogleAnalyticsObject|google-analytics\.com|gtm\.js/', $html)) {
            $tools[] = [
                'name' => 'Google Analytics',
                'type' => 'Web Analytics',
                'detected_via' => 'Script detection',
                'confidence' => 'High'
            ];
        }
        
        // Google Tag Manager
        if (preg_match('/googletagmanager\.com\/gtm\.js/', $html)) {
            $tools[] = [
                'name' => 'Google Tag Manager',
                'type' => 'Tag Management',
                'detected_via' => 'Script detection',
                'confidence' => 'High'
            ];
        }
        
        // Adobe Analytics
        if (preg_match('/omniture\.com|adobe\.com\/experience-platform/', $html)) {
            $tools[] = [
                'name' => 'Adobe Analytics',
                'type' => 'Web Analytics',
                'detected_via' => 'Script detection',
                'confidence' => 'High'
            ];
        }
        
        // Mixpanel
        if (preg_match('/mixpanel\.com/', $html)) {
            $tools[] = [
                'name' => 'Mixpanel',
                'type' => 'Product Analytics',
                'detected_via' => 'Script detection',
                'confidence' => 'High'
            ];
        }
        
        return $tools;
    }
    
    protected function detectSocialPixels(string $html, DOMXPath $xpath): array
    {
        $pixels = [];
        
        // Facebook Pixel
        if (preg_match('/facebook\.com\/tr\?|fbq\(/', $html)) {
            $pixels[] = [
                'name' => 'Facebook Pixel',
                'platform' => 'Facebook',
                'detected_via' => 'Script detection',
                'confidence' => 'High'
            ];
        }
        
        // Twitter Pixel
        if (preg_match('/twitter\.com\/i\/adsct|twq\(/', $html)) {
            $pixels[] = [
                'name' => 'Twitter Pixel',
                'platform' => 'Twitter',
                'detected_via' => 'Script detection',
                'confidence' => 'High'
            ];
        }
        
        // LinkedIn Insight Tag
        if (preg_match('/linkedin\.com\/li\.lms-analytics/', $html)) {
            $pixels[] = [
                'name' => 'LinkedIn Insight Tag',
                'platform' => 'LinkedIn',
                'detected_via' => 'Script detection',
                'confidence' => 'High'
            ];
        }
        
        // Pinterest Tag
        if (preg_match('/pinterest\.com\/ct\//', $html)) {
            $pixels[] = [
                'name' => 'Pinterest Tag',
                'platform' => 'Pinterest',
                'detected_via' => 'Script detection',
                'confidence' => 'High'
            ];
        }
        
        return $pixels;
    }
    
    protected function detectChatWidgets(string $html, DOMXPath $xpath): array
    {
        $widgets = [];
        
        // Intercom
        if (preg_match('/intercom\.io|Intercom\(/', $html)) {
            $widgets[] = [
                'name' => 'Intercom',
                'type' => 'Customer Support',
                'detected_via' => 'Script detection',
                'confidence' => 'High'
            ];
        }
        
        // Zendesk Chat
        if (preg_match('/zendesk\.com\/chat|zdassets\.com/', $html)) {
            $widgets[] = [
                'name' => 'Zendesk Chat',
                'type' => 'Customer Support',
                'detected_via' => 'Script detection',
                'confidence' => 'High'
            ];
        }
        
        // Drift
        if (preg_match('/drift\.com|driftt\.com/', $html)) {
            $widgets[] = [
                'name' => 'Drift',
                'type' => 'Conversational Marketing',
                'detected_via' => 'Script detection',
                'confidence' => 'High'
            ];
        }
        
        // Crisp
        if (preg_match('/crisp\.chat/', $html)) {
            $widgets[] = [
                'name' => 'Crisp',
                'type' => 'Customer Support',
                'detected_via' => 'Script detection',
                'confidence' => 'High'
            ];
        }
        
        return $widgets;
    }
    
    protected function detectEmailMarketing(string $html, DOMXPath $xpath): array
    {
        $tools = [];
        
        // Mailchimp
        if (preg_match('/mailchimp\.com|mc\.us\d+\.list-manage\.com/', $html)) {
            $tools[] = [
                'name' => 'Mailchimp',
                'type' => 'Email Marketing',
                'detected_via' => 'Script/Form detection',
                'confidence' => 'High'
            ];
        }
        
        // Klaviyo
        if (preg_match('/klaviyo\.com/', $html)) {
            $tools[] = [
                'name' => 'Klaviyo',
                'type' => 'Email Marketing',
                'detected_via' => 'Script detection',
                'confidence' => 'High'
            ];
        }
        
        // ConvertKit
        if (preg_match('/convertkit\.com/', $html)) {
            $tools[] = [
                'name' => 'ConvertKit',
                'type' => 'Email Marketing',
                'detected_via' => 'Script detection',
                'confidence' => 'High'
            ];
        }
        
        return $tools;
    }
    
    protected function detectAdvertisingNetworks(string $html, DOMXPath $xpath): array
    {
        $networks = [];
        
        // Google Ads
        if (preg_match('/googlesyndication\.com|googleadservices\.com/', $html)) {
            $networks[] = [
                'name' => 'Google Ads',
                'type' => 'Search/Display Advertising',
                'detected_via' => 'Script detection',
                'confidence' => 'High'
            ];
        }
        
        // Facebook Ads
        if (preg_match('/facebook\.com\/tr\?|fbq\(/', $html)) {
            $networks[] = [
                'name' => 'Facebook Ads',
                'type' => 'Social Media Advertising',
                'detected_via' => 'Pixel detection',
                'confidence' => 'High'
            ];
        }
        
        // Microsoft Advertising (Bing)
        if (preg_match('/bing\.com\/uet/', $html)) {
            $networks[] = [
                'name' => 'Microsoft Advertising',
                'type' => 'Search Advertising',
                'detected_via' => 'Script detection',
                'confidence' => 'High'
            ];
        }
        
        return $networks;
    }
    
    protected function detectRetargetingTools(string $html, DOMXPath $xpath): array
    {
        $tools = [];
        
        // AdRoll
        if (preg_match('/adroll\.com/', $html)) {
            $tools[] = [
                'name' => 'AdRoll',
                'type' => 'Retargeting',
                'detected_via' => 'Script detection',
                'confidence' => 'High'
            ];
        }
        
        // Criteo
        if (preg_match('/criteo\.com/', $html)) {
            $tools[] = [
                'name' => 'Criteo',
                'type' => 'Retargeting',
                'detected_via' => 'Script detection',
                'confidence' => 'High'
            ];
        }
        
        // Perfect Audience
        if (preg_match('/perfectaudience\.com/', $html)) {
            $tools[] = [
                'name' => 'Perfect Audience',
                'type' => 'Retargeting',
                'detected_via' => 'Script detection',
                'confidence' => 'High'
            ];
        }
        
        return $tools;
    }
    
    protected function detectSeoTools(string $html, DOMXPath $xpath): array
    {
        $tools = [];
        
        // Yoast SEO
        if (preg_match('/yoast|wpseo/', $html)) {
            $tools[] = [
                'name' => 'Yoast SEO',
                'type' => 'SEO Plugin',
                'detected_via' => 'Meta tag detection',
                'confidence' => 'High'
            ];
        }
        
        // All in One SEO
        if (preg_match('/All in One SEO/', $html)) {
            $tools[] = [
                'name' => 'All in One SEO',
                'type' => 'SEO Plugin',
                'detected_via' => 'Meta tag detection',
                'confidence' => 'High'
            ];
        }
        
        // Rank Math
        if (preg_match('/rank-math/', $html)) {
            $tools[] = [
                'name' => 'Rank Math',
                'type' => 'SEO Plugin',
                'detected_via' => 'Meta tag detection',
                'confidence' => 'High'
            ];
        }
        
        return $tools;
    }
    
    protected function extractOpengraphTags(DOMXPath $xpath): array
    {
        $ogTags = [];
        
        $ogNodes = $xpath->query('//meta[starts-with(@property, "og:")]');
        foreach ($ogNodes as $node) {
            $property = $node->getAttribute('property');
            $content = $node->getAttribute('content');
            
            if ($property && $content) {
                $ogTags[$property] = $content;
            }
        }
        
        return $ogTags;
    }
    
    protected function extractSchemaMarkup(string $html, DOMXPath $xpath): array
    {
        $schemas = [];
        
        // JSON-LD Schema
        $jsonLdNodes = $xpath->query('//script[@type="application/ld+json"]');
        foreach ($jsonLdNodes as $node) {
            $content = trim($node->textContent);
            if ($content) {
                $decoded = json_decode($content, true);
                if (json_last_error() === JSON_ERROR_NONE && isset($decoded['@type'])) {
                    $schemas[] = [
                        'type' => $decoded['@type'],
                        'format' => 'JSON-LD',
                        'content' => $decoded
                    ];
                }
            }
        }
        
        // Microdata
        $microdataNodes = $xpath->query('//*[@itemtype]');
        foreach ($microdataNodes as $node) {
            $itemType = $node->getAttribute('itemtype');
            if ($itemType) {
                $schemas[] = [
                    'type' => basename($itemType),
                    'format' => 'Microdata',
                    'itemtype' => $itemType
                ];
            }
        }
        
        return $schemas;
    }
    
    protected function detectUtmParameters(string $html, DOMXPath $xpath): array
    {
        $utmParams = [];
        
        // Look for UTM parameters in links
        $links = $xpath->query('//a[@href]');
        foreach ($links as $link) {
            $href = $link->getAttribute('href');
            if (preg_match('/utm_/', $href)) {
                $parsed = parse_url($href);
                if (isset($parsed['query'])) {
                    parse_str($parsed['query'], $params);
                    $utmKeys = array_filter($params, fn($key) => str_starts_with($key, 'utm_'), ARRAY_FILTER_USE_KEY);
                    
                    if (!empty($utmKeys)) {
                        $utmParams[] = [
                            'url' => $href,
                            'parameters' => $utmKeys
                        ];
                    }
                }
            }
        }
        
        return $utmParams;
    }
    
    protected function analyzeBlogContent(string $baseUrl, DOMXPath $xpath, string $html): array
    {
        $analysis = [
            'has_blog' => false,
            'blog_links' => [],
            'estimated_frequency' => 'Unknown',
            'content_indicators' => []
        ];
        
        // Look for blog indicators
        $blogIndicators = ['blog', 'news', 'articles', 'insights', 'resources'];
        $links = $xpath->query('//a[@href]');
        
        foreach ($links as $link) {
            $href = $link->getAttribute('href');
            $text = trim($link->textContent);
            
            foreach ($blogIndicators as $indicator) {
                if (stripos($href, $indicator) !== false || stripos($text, $indicator) !== false) {
                    $analysis['has_blog'] = true;
                    $analysis['blog_links'][] = [
                        'url' => $href,
                        'text' => $text,
                        'indicator' => $indicator
                    ];
                    break;
                }
            }
        }
        
        // Look for date patterns in content
        $datePatterns = [
            '/\d{4}-\d{2}-\d{2}/',
            '/\d{1,2}\/\d{1,2}\/\d{4}/',
            '/\w+ \d{1,2}, \d{4}/'
        ];
        
        foreach ($datePatterns as $pattern) {
            if (preg_match($pattern, $html)) {
                $analysis['content_indicators'][] = 'Date patterns found';
                break;
            }
        }
        
        return $analysis;
    }
    
    protected function analyzeContentStrategy(DOMXPath $xpath): array
    {
        $strategy = [
            'content_types' => [],
            'keyword_density' => [],
            'content_length' => 0,
            'readability_indicators' => []
        ];
        
        // Count content types
        $images = $xpath->query('//img')->length;
        $videos = $xpath->query('//video')->length;
        $forms = $xpath->query('//form')->length;
        $headings = $xpath->query('//h1|//h2|//h3|//h4|//h5|//h6')->length;
        
        if ($images > 0) $strategy['content_types'][] = "Images ({$images})";
        if ($videos > 0) $strategy['content_types'][] = "Videos ({$videos})";
        if ($forms > 0) $strategy['content_types'][] = "Forms ({$forms})";
        if ($headings > 0) $strategy['content_types'][] = "Headings ({$headings})";
        
        // Basic content length
        $bodyNode = $xpath->query('//body')->item(0);
        if ($bodyNode) {
            $strategy['content_length'] = strlen(strip_tags($bodyNode->textContent));
        }
        
        return $strategy;
    }
    
    protected function detectPerformanceTools(string $html, DOMXPath $xpath): array
    {
        $tools = [];
        
        // CDN Detection
        if (preg_match('/cloudflare\.com|cloudfront\.net|maxcdn\.com/', $html)) {
            $tools[] = [
                'name' => 'CDN',
                'type' => 'Performance',
                'detected_via' => 'URL detection',
                'confidence' => 'High'
            ];
        }
        
        // Lazy Loading
        if (preg_match('/loading="lazy"|data-src/', $html)) {
            $tools[] = [
                'name' => 'Lazy Loading',
                'type' => 'Performance',
                'detected_via' => 'Attribute detection',
                'confidence' => 'High'
            ];
        }
        
        return $tools;
    }
    
    protected function detectSecurityTools(string $html, DOMXPath $xpath): array
    {
        $tools = [];
        
        // Cloudflare
        if (preg_match('/cloudflare\.com/', $html)) {
            $tools[] = [
                'name' => 'Cloudflare',
                'type' => 'Security/CDN',
                'detected_via' => 'Script detection',
                'confidence' => 'High'
            ];
        }
        
        // reCAPTCHA
        if (preg_match('/recaptcha|google\.com\/recaptcha/', $html)) {
            $tools[] = [
                'name' => 'reCAPTCHA',
                'type' => 'Security',
                'detected_via' => 'Script detection',
                'confidence' => 'High'
            ];
        }
        
        return $tools;
    }
    
    protected function detectCms(string $html, DOMXPath $xpath): array
    {
        $cms = [];
        
        // WordPress
        if (preg_match('/wp-content|wp-includes|wordpress/', $html)) {
            $cms[] = [
                'name' => 'WordPress',
                'type' => 'CMS',
                'detected_via' => 'Path detection',
                'confidence' => 'High'
            ];
        }
        
        // Shopify
        if (preg_match('/shopify\.com|myshopify\.com/', $html)) {
            $cms[] = [
                'name' => 'Shopify',
                'type' => 'E-commerce',
                'detected_via' => 'Script detection',
                'confidence' => 'High'
            ];
        }
        
        // Squarespace
        if (preg_match('/squarespace\.com/', $html)) {
            $cms[] = [
                'name' => 'Squarespace',
                'type' => 'Website Builder',
                'detected_via' => 'Script detection',
                'confidence' => 'High'
            ];
        }
        
        return $cms;
    }
    
    protected function generateStrategySummary(array $data): array
    {
        $strategies = [];
        
        if (!empty($data['analytics_tools'])) {
            $strategies[] = [
                'category' => 'Analytics',
                'tools' => count($data['analytics_tools']),
                'confidence' => 'High',
                'description' => 'Active web analytics tracking'
            ];
        }
        
        if (!empty($data['social_pixels'])) {
            $strategies[] = [
                'category' => 'Social Media Marketing',
                'tools' => count($data['social_pixels']),
                'confidence' => 'High',
                'description' => 'Social media advertising pixels detected'
            ];
        }
        
        if (!empty($data['advertising_networks'])) {
            $strategies[] = [
                'category' => 'Paid Advertising',
                'tools' => count($data['advertising_networks']),
                'confidence' => 'High',
                'description' => 'Active paid advertising campaigns'
            ];
        }
        
        if (!empty($data['retargeting_tools'])) {
            $strategies[] = [
                'category' => 'Retargeting',
                'tools' => count($data['retargeting_tools']),
                'confidence' => 'High',
                'description' => 'Retargeting campaigns active'
            ];
        }
        
        if (!empty($data['email_marketing'])) {
            $strategies[] = [
                'category' => 'Email Marketing',
                'tools' => count($data['email_marketing']),
                'confidence' => 'High',
                'description' => 'Email marketing tools integrated'
            ];
        }
        
        if (!empty($data['chat_widgets'])) {
            $strategies[] = [
                'category' => 'Customer Support',
                'tools' => count($data['chat_widgets']),
                'confidence' => 'High',
                'description' => 'Live chat support available'
            ];
        }
        
        if (!empty($data['seo_tools']) || !empty($data['opengraph_tags']) || !empty($data['schema_markup'])) {
            $strategies[] = [
                'category' => 'SEO Optimization',
                'tools' => count($data['seo_tools']) + (empty($data['opengraph_tags']) ? 0 : 1) + count($data['schema_markup']),
                'confidence' => 'High',
                'description' => 'SEO optimization tools and markup'
            ];
        }
        
        if (!empty($data['cms_detection'])) {
            $strategies[] = [
                'category' => 'Platform',
                'tools' => count($data['cms_detection']),
                'confidence' => 'High',
                'description' => 'CMS/Platform identified'
            ];
        }
        
        return $strategies;
    }
    
    protected function generateActionableInsights(array $strategySummary): array
    {
        $insights = [];
        
        $categories = array_column($strategySummary, 'category');
        
        if (!in_array('Analytics', $categories)) {
            $insights[] = [
                'type' => 'opportunity',
                'category' => 'Analytics',
                'message' => 'No analytics tools detected. Consider implementing Google Analytics for better data insights.',
                'priority' => 'High'
            ];
        }
        
        if (!in_array('Social Media Marketing', $categories)) {
            $insights[] = [
                'type' => 'opportunity',
                'category' => 'Social Media',
                'message' => 'No social media pixels found. Consider adding Facebook Pixel for better audience tracking.',
                'priority' => 'Medium'
            ];
        }
        
        if (!in_array('SEO Optimization', $categories)) {
            $insights[] = [
                'type' => 'opportunity',
                'category' => 'SEO',
                'message' => 'Limited SEO optimization detected. Consider implementing structured data and better meta tags.',
                'priority' => 'High'
            ];
        }
        
        if (!in_array('Customer Support', $categories)) {
            $insights[] = [
                'type' => 'opportunity',
                'category' => 'Customer Support',
                'message' => 'No live chat detected. Consider adding chat widget for better customer engagement.',
                'priority' => 'Medium'
            ];
        }
        
        if (in_array('Retargeting', $categories)) {
            $insights[] = [
                'type' => 'competitive_advantage',
                'category' => 'Retargeting',
                'message' => 'Competitor is using retargeting. Consider implementing similar strategies.',
                'priority' => 'High'
            ];
        }
        
        if (in_array('Paid Advertising', $categories)) {
            $insights[] = [
                'type' => 'competitive_advantage',
                'category' => 'Advertising',
                'message' => 'Competitor is actively running paid ads. Monitor their ad strategy.',
                'priority' => 'High'
            ];
        }
        
        return $insights;
    }
    
    protected function countTotalTools(array $strategySummary): int
    {
        return array_sum(array_column($strategySummary, 'tools'));
    }
    
    protected function calculateStrategyScore(array $strategySummary): float
    {
        $maxScore = 100;
        $categoryWeights = [
            'Analytics' => 20,
            'SEO Optimization' => 25,
            'Social Media Marketing' => 15,
            'Paid Advertising' => 15,
            'Email Marketing' => 10,
            'Customer Support' => 10,
            'Retargeting' => 5
        ];
        
        $score = 0;
        $categories = array_column($strategySummary, 'category');
        
        foreach ($categoryWeights as $category => $weight) {
            if (in_array($category, $categories)) {
                $score += $weight;
            }
        }
        
        return round($score, 2);
    }
    
    /**
     * Analyze backlinks from the website
     */
    protected function analyzeBacklinks(string $url, DOMXPath $xpath, string $htmlContent): array
    {
        $backlinks = [];
        $totalBacklinks = 0;
        
        try {
            // Extract external links from the page
            $links = $xpath->query('//a[@href]');
            $externalLinks = [];
            $domain = parse_url($url, PHP_URL_HOST);
            
            foreach ($links as $link) {
                $href = $link->getAttribute('href');
                if (empty($href)) continue;
                
                // Convert relative URLs to absolute
                if (str_starts_with($href, '/')) {
                    $href = rtrim($url, '/') . $href;
                } elseif (!str_starts_with($href, 'http')) {
                    continue; // Skip non-HTTP links
                }
                
                $linkDomain = parse_url($href, PHP_URL_HOST);
                if ($linkDomain && $linkDomain !== $domain) {
                    $externalLinks[] = [
                        'url' => $href,
                        'domain' => $linkDomain,
                        'anchor_text' => trim($link->textContent),
                        'rel' => $link->getAttribute('rel'),
                        'is_nofollow' => str_contains($link->getAttribute('rel'), 'nofollow'),
                    ];
                }
            }
            
            // Analyze link quality and patterns
            $linkDomains = array_count_values(array_column($externalLinks, 'domain'));
            $highAuthorityDomains = $this->identifyHighAuthorityDomains($linkDomains);
            
            $totalBacklinks = count($externalLinks);
            
            $backlinks = [
                'total_backlinks' => $totalBacklinks,
                'external_links' => array_slice($externalLinks, 0, 50), // Limit to first 50
                'domains_linked_to' => count($linkDomains),
                'high_authority_domains' => $highAuthorityDomains,
                'nofollow_percentage' => $totalBacklinks > 0 ? round((count(array_filter($externalLinks, fn($link) => $link['is_nofollow'])) / $totalBacklinks) * 100, 2) : 0,
                'analysis_date' => now()->toDateTimeString(),
            ];
            
        } catch (\Exception $e) {
            Log::error("Backlink analysis failed: " . $e->getMessage());
            $backlinks = [
                'total_backlinks' => 0,
                'external_links' => [],
                'domains_linked_to' => 0,
                'high_authority_domains' => [],
                'nofollow_percentage' => 0,
                'analysis_date' => now()->toDateTimeString(),
                'error' => $e->getMessage(),
            ];
        }
        
        return $backlinks;
    }
    
    /**
     * Analyze keywords from the website content
     */
    protected function analyzeKeywords(string $url, DOMXPath $xpath, string $htmlContent): array
    {
        $keywords = [];
        $totalKeywords = 0;
        
        try {
            // Extract text content from important elements
            $titleText = $this->extractTitle($xpath);
            $descriptionText = $this->extractDescription($xpath);
            $headings = $this->extractHeadings($xpath);
            $bodyText = $this->extractBodyText($xpath);
            
            // Combine all text content
            $allText = implode(' ', array_filter([
                $titleText,
                $descriptionText,
                implode(' ', $headings),
                substr($bodyText, 0, 5000) // Limit body text for performance
            ]));
            
            // Extract keywords from meta tags
            $metaKeywords = $this->extractMetaKeywords($xpath);
            
            // Extract keywords from content
            $contentKeywords = $this->extractContentKeywords($allText);
            
            // Analyze keyword density
            $keywordDensity = $this->calculateKeywordDensity($allText);
            
            // Get top keywords
            $topKeywords = array_slice($contentKeywords, 0, 100);
            $totalKeywords = count($topKeywords);
            
            $keywords = [
                'total_keywords' => $totalKeywords,
                'meta_keywords' => $metaKeywords,
                'content_keywords' => $topKeywords,
                'keyword_density' => $keywordDensity,
                'title_keywords' => $this->extractContentKeywords($titleText),
                'heading_keywords' => $this->extractContentKeywords(implode(' ', $headings)),
                'analysis_date' => now()->toDateTimeString(),
            ];
            
        } catch (\Exception $e) {
            Log::error("Keyword analysis failed: " . $e->getMessage());
            $keywords = [
                'total_keywords' => 0,
                'meta_keywords' => [],
                'content_keywords' => [],
                'keyword_density' => [],
                'title_keywords' => [],
                'heading_keywords' => [],
                'analysis_date' => now()->toDateTimeString(),
                'error' => $e->getMessage(),
            ];
        }
        
        return $keywords;
    }
    
    /**
     * Identify high authority domains
     */
    protected function identifyHighAuthorityDomains(array $linkDomains): array
    {
        $highAuthorityDomains = [
            'facebook.com', 'twitter.com', 'linkedin.com', 'instagram.com', 'youtube.com',
            'google.com', 'wikipedia.org', 'github.com', 'stackoverflow.com', 'medium.com',
            'reddit.com', 'quora.com', 'pinterest.com', 'tumblr.com', 'wordpress.com',
            'blogspot.com', 'amazon.com', 'apple.com', 'microsoft.com', 'adobe.com'
        ];
        
        return array_filter($linkDomains, function($domain) use ($highAuthorityDomains) {
            return in_array($domain, $highAuthorityDomains);
        }, ARRAY_FILTER_USE_KEY);
    }
    
    /**
     * Extract meta keywords
     */
    protected function extractMetaKeywords(DOMXPath $xpath): array
    {
        $keywords = [];
        
        $metaKeywords = $xpath->query('//meta[@name="keywords"]/@content');
        if ($metaKeywords->length > 0) {
            $content = $metaKeywords->item(0)->nodeValue;
            $keywords = array_map('trim', explode(',', $content));
        }
        
        return array_filter($keywords);
    }
    
    /**
     * Extract headings from the page
     */
    protected function extractHeadings(DOMXPath $xpath): array
    {
        $headings = [];
        
        for ($i = 1; $i <= 6; $i++) {
            $elements = $xpath->query("//h{$i}");
            foreach ($elements as $element) {
                $headings[] = trim($element->textContent);
            }
        }
        
        return array_filter($headings);
    }
    
    /**
     * Extract body text content
     */
    protected function extractBodyText(DOMXPath $xpath): string
    {
        $bodyElements = $xpath->query('//body//text()[not(ancestor::script) and not(ancestor::style) and not(ancestor::noscript)]');
        $text = '';
        
        foreach ($bodyElements as $element) {
            $text .= ' ' . trim($element->nodeValue);
        }
        
        return trim($text);
    }
    
    /**
     * Extract keywords from content using basic text analysis
     */
    protected function extractContentKeywords(string $text): array
    {
        if (empty($text)) return [];
        
        // Clean and normalize text
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\s]/', ' ', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        
        // Split into words
        $words = array_filter(explode(' ', $text));
        
        // Remove common stop words
        $stopWords = ['the', 'is', 'at', 'which', 'on', 'and', 'a', 'to', 'as', 'are', 'was', 'will', 'be', 'has', 'have', 'had', 'do', 'does', 'did', 'can', 'could', 'should', 'would', 'may', 'might', 'must', 'shall', 'will', 'of', 'in', 'for', 'with', 'by', 'from', 'about', 'into', 'through', 'during', 'before', 'after', 'above', 'below', 'up', 'down', 'out', 'off', 'over', 'under', 'again', 'further', 'then', 'once'];
        
        $filteredWords = array_filter($words, function($word) use ($stopWords) {
            return strlen($word) > 2 && !in_array($word, $stopWords);
        });
        
        // Count word frequency
        $wordCounts = array_count_values($filteredWords);
        arsort($wordCounts);
        
        // Return top keywords with their counts
        $keywords = [];
        foreach ($wordCounts as $word => $count) {
            if ($count > 1) { // Only include words that appear more than once
                $keywords[] = [
                    'keyword' => $word,
                    'count' => $count,
                    'density' => round(($count / count($words)) * 100, 2)
                ];
            }
        }
        
        return $keywords;
    }
    
    /**
     * Calculate keyword density for the most common words
     */
    protected function calculateKeywordDensity(string $text): array
    {
        $keywords = $this->extractContentKeywords($text);
        return array_slice($keywords, 0, 20); // Return top 20 keywords by density
    }
}
