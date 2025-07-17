<?php

namespace App\Http\Controllers;

use App\Models\Backlink;
use App\Services\BacklinkService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BacklinkController extends Controller
{
    protected BacklinkService $backlinkService;

    public function __construct(BacklinkService $backlinkService)
    {
        $this->backlinkService = $backlinkService;
    }

    /**
     * Get all backlinks for the authenticated user
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        $backlinks = Backlink::byUser($user->id)
            ->with('seoAudit')
            ->orderBy('domain_authority', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $backlinks
        ]);
    }

    /**
     * Analyze backlinks for a specific domain
     */
    public function analyze(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'domain' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $domain = $request->domain;

            // Analyze backlinks for the domain
            $backlinkData = $this->backlinkService->analyzeBacklinks($domain);

            // Store new backlinks in database
            foreach ($backlinkData['backlinks'] as $backlinkInfo) {
                $existingBacklink = Backlink::byUser($user->id)
                    ->where('source_url', $backlinkInfo['source_url'])
                    ->where('target_url', $backlinkInfo['target_url'])
                    ->first();

                if (!$existingBacklink) {
                    Backlink::create(array_merge($backlinkInfo, [
                        'user_id' => $user->id,
                        'discovered_date' => now(),
                        'status' => 'active'
                    ]));
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Backlink analysis completed',
                'data' => $backlinkData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error analyzing backlinks: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get backlink analytics and metrics
     */
    public function analytics(Request $request): JsonResponse
    {
        $user = Auth::user();
        $domain = $request->get('domain');

        try {
            $analytics = $this->backlinkService->getBacklinkAnalytics($user->id, $domain);

            return response()->json([
                'success' => true,
                'data' => $analytics
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting backlink analytics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check the status of existing backlinks
     */
    public function checkStatus(Request $request): JsonResponse
    {
        $user = Auth::user();
        $backlinkIds = $request->get('backlink_ids', []);

        if (empty($backlinkIds)) {
            // Check all backlinks for the user
            $backlinks = Backlink::byUser($user->id)->get();
        } else {
            $backlinks = Backlink::byUser($user->id)->whereIn('id', $backlinkIds)->get();
        }

        $updated = 0;
        $errors = [];

        foreach ($backlinks as $backlink) {
            try {
                $status = $this->backlinkService->checkBacklinkStatus($backlink->source_url, $backlink->target_url);
                
                $backlink->update([
                    'status' => $status['status'],
                    'last_checked' => now()
                ]);
                
                $updated++;
            } catch (\Exception $e) {
                $errors[] = "Error checking backlink {$backlink->id}: " . $e->getMessage();
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Checked {$updated} backlinks successfully",
            'updated_count' => $updated,
            'errors' => $errors
        ]);
    }

    /**
     * Find competitor backlinks
     */
    public function findCompetitors(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'domain' => 'required|string|max:255',
            'competitor_domains' => 'required|array|min:1',
            'competitor_domains.*' => 'string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $analysis = $this->backlinkService->compareCompetitorBacklinks(
                $request->domain,
                $request->competitor_domains
            );

            return response()->json([
                'success' => true,
                'data' => $analysis
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error analyzing competitor backlinks: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get backlink opportunities
     */
    public function opportunities(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'domain' => 'required|string|max:255',
            'niche' => 'string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $opportunities = $this->backlinkService->findBacklinkOpportunities(
                $request->domain,
                $request->get('niche', '')
            );

            return response()->json([
                'success' => true,
                'data' => $opportunities
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error finding backlink opportunities: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a specific backlink
     */
    public function update($id, Request $request): JsonResponse
    {
        $user = Auth::user();
        $backlink = Backlink::byUser($user->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'anchor_text' => 'string|max:255',
            'rel_attribute' => 'string|max:50',
            'status' => 'in:active,lost,broken'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $backlink->update($request->only(['anchor_text', 'rel_attribute', 'status']));

        return response()->json([
            'success' => true,
            'message' => 'Backlink updated successfully',
            'data' => $backlink
        ]);
    }

    /**
     * Delete a backlink
     */
    public function destroy($id): JsonResponse
    {
        $user = Auth::user();
        $backlink = Backlink::byUser($user->id)->findOrFail($id);
        $backlink->delete();

        return response()->json([
            'success' => true,
            'message' => 'Backlink deleted successfully'
        ]);
    }

    /**
     * Export backlinks to CSV
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        $backlinks = Backlink::byUser($user->id)->get();

        $filename = 'backlinks_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($backlinks) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'ID', 'Source URL', 'Target URL', 'Anchor Text', 
                'Domain Authority', 'Page Authority', 'Spam Score',
                'Link Type', 'Rel Attribute', 'Status', 'Discovered Date'
            ]);

            // Add data rows
            foreach ($backlinks as $backlink) {
                fputcsv($file, [
                    $backlink->id,
                    $backlink->source_url,
                    $backlink->target_url,
                    $backlink->anchor_text,
                    $backlink->domain_authority,
                    $backlink->page_authority,
                    $backlink->spam_score,
                    $backlink->link_type,
                    $backlink->rel_attribute,
                    $backlink->status,
                    $backlink->discovered_date->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Check backlinks for a domain and store results
     */
    public function checkBacklinks(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'domain' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $domain = $this->cleanDomain($request->domain);
            
            // For demo purposes, handle both authenticated and guest users
            if (Auth::check()) {
                $user = Auth::user();
                $userId = $user->id;
            } else {
                $userId = null; // Guest user
            }

            // Check if we already have recent data for this domain
            $existingBacklinks = Backlink::where('domain', $domain)
                ->when($userId, function($query, $userId) {
                    return $query->where('user_id', $userId);
                })
                ->where('found_at', '>', now()->subHours(24)) // Within last 24 hours
                ->get();

            if ($existingBacklinks->count() > 0) {
                // Return existing data
                $backlinks = $existingBacklinks->toArray();
            } else {
                // Fetch new backlink data using enhanced analysis
                $backlinks = $this->fetchBacklinksFromSources($domain);
                
                // Store new backlinks in database (only for authenticated users)
                if ($userId) {
                    foreach ($backlinks as $backlinkData) {
                        Backlink::create([
                            'user_id' => $userId,
                            'domain' => $domain,
                            'source_url' => $backlinkData['source_url'],
                            'target_url' => $backlinkData['target_url'],
                            'anchor_text' => $backlinkData['anchor_text'],
                            'link_type' => $backlinkData['link_type'],
                            'domain_authority' => $backlinkData['domain_authority'],
                            'page_authority' => $backlinkData['page_authority'],
                            'spam_score' => $backlinkData['spam_score'],
                            'content_summary' => $backlinkData['content_summary'] ?? null,
                            'page_type' => $backlinkData['page_type'] ?? null,
                            'status' => 'active',
                            'found_at' => now(),
                            'last_checked' => now()
                        ]);
                    }
                }
            }

            // Calculate summary statistics
            $summary = [
                'total_backlinks' => count($backlinks),
                'unique_domains' => $this->countUniqueDomains($backlinks),
                'dofollow_count' => count(array_filter($backlinks, fn($b) => $b['link_type'] === 'dofollow')),
                'nofollow_count' => count(array_filter($backlinks, fn($b) => $b['link_type'] === 'nofollow')),
                'average_domain_authority' => $this->calculateAverage($backlinks, 'domain_authority'),
                'average_spam_score' => $this->calculateAverage($backlinks, 'spam_score')
            ];

            return response()->json([
                'success' => true,
                'message' => 'Backlinks analyzed successfully',
                'data' => [
                    'domain' => $domain,
                    'summary' => $summary,
                    'backlinks' => $backlinks,
                    'checked_at' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking backlinks: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch backlinks from various sources using enhanced analysis
     */
    private function fetchBacklinksFromSources(string $domain): array
    {
        // Use the enhanced BacklinkService analysis
        $result = $this->backlinkService->analyzeBacklinksEnhanced($domain);
        
        // Return the backlinks array from the service
        return $result['backlinks'] ?? [];
    }

    /**
     * Check SEO Review Tools for backlinks
     */
    private function checkSEOReviewTools(string $domain): array
    {
        // In a real implementation, you would make HTTP requests to free backlink tools
        // For demo purposes, we'll simulate realistic backlink data
        
        $sources = [
            'blog.example.com',
            'news.website.com',
            'forum.community.org',
            'directory.business.net',
            'social.platform.com'
        ];
        
        $backlinks = [];
        foreach ($sources as $source) {
            $backlinks[] = [
                'source_url' => "https://{$source}/article-mentioning-{$domain}",
                'target_url' => "https://{$domain}",
                'source_domain' => $source,
                'anchor_text' => $this->generateAnchorText($domain),
                'link_type' => rand(0, 100) > 25 ? 'dofollow' : 'nofollow',
                'domain_authority' => rand(20, 90),
                'page_authority' => rand(15, 80),
                'spam_score' => rand(0, 25),
                'rel_attribute' => rand(0, 100) > 75 ? 'nofollow' : null,
                'found_at' => now()
            ];
        }
        
        return $backlinks;
    }

    /**
     * Check OpenLinkProfiler for backlinks
     */
    private function checkOpenLinkProfiler(string $domain): array
    {
        // Simulate OpenLinkProfiler data
        return [
            [
                'source_url' => "https://reddit.com/r/webdev/comments/abc123",
                'target_url' => "https://{$domain}",
                'source_domain' => 'reddit.com',
                'anchor_text' => 'check this site',
                'link_type' => 'nofollow',
                'domain_authority' => 95,
                'page_authority' => 40,
                'spam_score' => 2,
                'rel_attribute' => 'nofollow',
                'found_at' => now()
            ]
        ];
    }

    /**
     * Check Google for backlinks using link: operator
     */
    private function checkGoogleBacklinks(string $domain): array
    {
        // Simulate Google search results
        return [
            [
                'source_url' => "https://github.com/user/project",
                'target_url' => "https://{$domain}",
                'source_domain' => 'github.com',
                'anchor_text' => $domain,
                'link_type' => 'dofollow',
                'domain_authority' => 100,
                'page_authority' => 60,
                'spam_score' => 0,
                'rel_attribute' => null,
                'found_at' => now()
            ]
        ];
    }

    /**
     * Generate realistic anchor text
     */
    private function generateAnchorText(string $domain): string
    {
        $anchors = [
            $domain,
            'visit website',
            'check this out',
            'read more here',
            'learn more',
            'official website',
            'homepage',
            str_replace('.com', '', $domain),
            'click here',
            'view site'
        ];
        
        return $anchors[array_rand($anchors)];
    }

    /**
     * Remove duplicate backlinks
     */
    private function removeDuplicateBacklinks(array $backlinks): array
    {
        $unique = [];
        $seen = [];
        
        foreach ($backlinks as $backlink) {
            $key = $backlink['source_url'] . '|' . $backlink['target_url'];
            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $unique[] = $backlink;
            }
        }
        
        return $unique;
    }

    /**
     * Clean domain name
     */
    private function cleanDomain(string $domain): string
    {
        $domain = strtolower(trim($domain));
        $domain = preg_replace('/^(https?:\/\/)?(www\.)?/', '', $domain);
        return rtrim($domain, '/');
    }

    /**
     * Calculate average for a specific field
     */
    private function calculateAverage(array $items, string $field): float
    {
        if (empty($items)) return 0;
        
        $sum = array_sum(array_column($items, $field));
        return round($sum / count($items), 1);
    }

    /**
     * Count unique domains from backlinks
     */
    private function countUniqueDomains(array $backlinks): int
    {
        $domains = [];
        foreach ($backlinks as $backlink) {
            $sourceUrl = $backlink['source_url'] ?? '';
            if (!empty($sourceUrl)) {
                $domain = parse_url($sourceUrl, PHP_URL_HOST);
                if (!empty($domain)) {
                    // Remove www. prefix for consistency
                    $domain = preg_replace('/^www\./', '', $domain);
                    $domains[$domain] = true;
                }
            }
        }
        
        return count($domains);
    }
}
