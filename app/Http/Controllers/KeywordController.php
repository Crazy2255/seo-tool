<?php

namespace App\Http\Controllers;

use App\Models\Keyword;
use App\Services\KeywordTrackingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class KeywordController extends Controller
{
    protected KeywordTrackingService $keywordService;

    public function __construct(KeywordTrackingService $keywordService)
    {
        $this->keywordService = $keywordService;
    }

    /**
     * Get all keywords for the authenticated user
     */
    public function index(): JsonResponse
    {
        // For demo purposes, handle unauthenticated users
        if (Auth::check()) {
            $keywords = Keyword::byUser(Auth::id())
                ->with('seoAudit')
                ->orderBy('created_at', 'desc')
                ->get(); // Use get() instead of paginate() for simpler API response
        } else {
            // Return demo data for unauthenticated users
            $keywords = $this->getDemoKeywords();
        }

        return response()->json([
            'success' => true,
            'data' => $keywords
        ]);
    }

    /**
     * Store a new keyword for tracking
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'keyword' => 'required|string|max:255',
            'url' => 'required|url',
            'country' => 'nullable|string|max:2',
            'city' => 'nullable|string|max:100',
            'language' => 'nullable|string|max:5'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Debug authentication status
            $authCheck = Auth::check();
            $authId = Auth::id();
            
            // For demo mode (unauthenticated users), return demo data
            if (!$authCheck) {
                $demoKeyword = [
                    'id' => time(),
                    'keyword' => $request->keyword,
                    'url' => $request->url,
                    'current_position' => rand(1, 50),
                    'previous_position' => rand(1, 50),
                    'search_volume' => rand(1000, 15000),
                    'difficulty' => rand(20, 90),
                    'cpc' => round(rand(50, 500) / 100, 2),
                    'country' => $request->get('country', 'US'),
                    'city' => $request->get('city', ''),
                    'language' => $request->get('language', 'en'),
                    'tracked_date' => now()->toISOString()
                ];

                return response()->json([
                    'success' => true,
                    'message' => 'Demo keyword created! Sign up to track real rankings.',
                    'debug' => ['auth_check' => false, 'session_id' => session()->getId()],
                    'data' => $demoKeyword
                ]);
            }

            // Check if keyword already exists for this user and URL (only for authenticated users)
            $existingKeyword = Keyword::byUser(Auth::id())
                ->where('keyword', $request->keyword)
                ->where('url', $request->url)
                ->first();

            if ($existingKeyword) {
                return response()->json([
                    'success' => false,
                    'message' => 'Keyword is already being tracked for this URL'
                ], 409);
            }

            // Track the keyword
            try {
                $keywordData = $this->keywordService->trackKeyword(
                    $request->keyword,
                    $request->url,
                    $request->get('country', 'US'),
                    $request->get('language', 'en')
                );

                // Add user_id and additional fields for authenticated users
                $keywordData['user_id'] = Auth::id();
                $keywordData['city'] = $request->get('city', '');
                $keywordData['tracked_date'] = now();

                \Log::info('Attempting to create keyword with data: ' . json_encode($keywordData));
                
                $keyword = Keyword::create($keywordData);
                
                \Log::info('Keyword created successfully: ' . $keyword->id);
            } catch (\Exception $innerEx) {
                \Log::error('Error in keyword creation process: ' . $innerEx->getMessage());
                \Log::error($innerEx->getTraceAsString());
                throw $innerEx;
            }

            return response()->json([
                'success' => true,
                'message' => 'Keyword tracking started successfully',
                'data' => $keyword
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error tracking keyword: ' . $e->getMessage(),
                'debug' => [
                    'auth_check' => Auth::check(),
                    'auth_id' => Auth::id(),
                    'session_id' => session()->getId(),
                    'exception_trace' => $e->getTraceAsString()
                ]
            ], 500);
        }
    }

    /**
     * Update keyword tracking data
     */
    public function update($id, Request $request): JsonResponse
    {
        // For demo purposes, handle unauthenticated users
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please log in to update keywords'
            ], 401);
        }

        $user = Auth::user();
        $keyword = Keyword::byUser($user->id)->findOrFail($id);

        try {
            // Get updated ranking data
            $updatedData = $this->keywordService->updateKeywordRanking($keyword);
            
            $keyword->update($updatedData);

            return response()->json([
                'success' => true,
                'message' => 'Keyword ranking updated successfully',
                'data' => $keyword
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating keyword: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a keyword from tracking
     */
    public function destroy($id): JsonResponse
    {
        // For demo purposes, handle unauthenticated users
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please log in to delete keywords'
            ], 401);
        }

        $user = Auth::user();
        $keyword = Keyword::byUser($user->id)->findOrFail($id);
        $keyword->delete();

        return response()->json([
            'success' => true,
            'message' => 'Keyword removed from tracking'
        ]);
    }

    /**
     * Bulk update all keywords for a user
     */
    public function bulkUpdate(): JsonResponse
    {
        // For demo purposes, handle unauthenticated users
        if (!Auth::check()) {
            return response()->json([
                'success' => true,
                'message' => 'Demo: Updated 3 keywords successfully',
                'updated_count' => 3,
                'errors' => []
            ]);
        }

        $user = Auth::user();
        $keywords = Keyword::byUser($user->id)->get();

        $updated = 0;
        $errors = [];

        foreach ($keywords as $keyword) {
            try {
                $updatedData = $this->keywordService->updateKeywordRanking($keyword);
                $keyword->update($updatedData);
                $updated++;
            } catch (\Exception $e) {
                $errors[] = "Error updating keyword '{$keyword->keyword}': " . $e->getMessage();
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Updated {$updated} keywords successfully",
            'updated_count' => $updated,
            'errors' => $errors
        ]);
    }

    /**
     * Get keyword analytics and trends
     */
    public function analytics(Request $request): JsonResponse
    {
        // For demo purposes, handle unauthenticated users
        if (!Auth::check()) {
            return response()->json([
                'success' => true,
                'data' => $this->getDemoAnalytics()
            ]);
        }

        $user = Auth::user();
        $period = $request->get('period', '30'); // days

        $analytics = $this->keywordService->getKeywordAnalytics($user->id, $period);

        return response()->json([
            'success' => true,
            'data' => $analytics
        ]);
    }

    /**
     * Search for keyword suggestions
     */
    public function suggestions(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'seed_keyword' => 'required|string|max:255',
            'country' => 'string|max:2|default:US'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $suggestions = $this->keywordService->getKeywordSuggestions(
                $request->seed_keyword,
                $request->get('country', 'US')
            );

            return response()->json([
                'success' => true,
                'data' => $suggestions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting keyword suggestions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check keyword ranking manually
     */
    public function checkRanking(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'keyword' => 'required|string|max:255',
            'url' => 'required|url',
            'country' => 'string|max:2|default:US'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $ranking = $this->keywordService->checkKeywordRanking(
                $request->keyword,
                $request->url,
                $request->get('country', 'US')
            );

            return response()->json([
                'success' => true,
                'data' => $ranking
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking keyword ranking: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get demo keywords for unauthenticated users
     */
    private function getDemoKeywords(): array
    {
        return [
            [
                'id' => 1,
                'keyword' => 'SEO tools',
                'url' => 'https://example.com',
                'current_position' => 15,
                'previous_position' => 18,
                'search_volume' => 12000,
                'difficulty' => 65,
                'cpc' => 2.45,
                'country' => 'US',
                'city' => 'new-york',
                'language' => 'en',
                'tracked_date' => now()->subDays(1)->toISOString()
            ],
            [
                'id' => 2,
                'keyword' => 'keyword rank tracker',
                'url' => 'https://example.com/keyword-tracker',
                'current_position' => 8,
                'previous_position' => 12,
                'search_volume' => 3200,
                'difficulty' => 45,
                'cpc' => 3.80,
                'country' => 'AE',
                'city' => 'dubai',
                'language' => 'en',
                'tracked_date' => now()->subHours(6)->toISOString()
            ],
            [
                'id' => 3,
                'keyword' => 'website audit',
                'url' => 'https://example.com/audit',
                'current_position' => 5,
                'previous_position' => 5,
                'search_volume' => 8500,
                'difficulty' => 55,
                'cpc' => 4.25,
                'country' => 'UK',
                'city' => 'london',
                'language' => 'en',
                'tracked_date' => now()->subDays(2)->toISOString()
            ]
        ];
    }

    /**
     * Get demo analytics for unauthenticated users
     */
    private function getDemoAnalytics(): array
    {
        return [
            'total_keywords' => 3,
            'average_position' => 9.3,
            'improved_rankings' => 2,
            'declined_rankings' => 0,
            'stable_rankings' => 1,
            'total_search_volume' => 23700,
            'visibility_score' => 78.5,
            'performance_trend' => [
                ['date' => now()->subDays(7)->format('Y-m-d'), 'average_position' => 12.1],
                ['date' => now()->subDays(6)->format('Y-m-d'), 'average_position' => 11.8],
                ['date' => now()->subDays(5)->format('Y-m-d'), 'average_position' => 11.2],
                ['date' => now()->subDays(4)->format('Y-m-d'), 'average_position' => 10.7],
                ['date' => now()->subDays(3)->format('Y-m-d'), 'average_position' => 10.1],
                ['date' => now()->subDays(2)->format('Y-m-d'), 'average_position' => 9.8],
                ['date' => now()->subDays(1)->format('Y-m-d'), 'average_position' => 9.3],
            ]
        ];
    }
}
