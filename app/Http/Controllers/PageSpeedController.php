<?php

namespace App\Http\Controllers;

use App\Models\PageSpeedAudit;
use App\Services\PageSpeedService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class PageSpeedController extends Controller
{
    protected $pageSpeedService;

    public function __construct(PageSpeedService $pageSpeedService)
    {
        $this->pageSpeedService = $pageSpeedService;
    }

    /**
     * Show the page speed checker tool.
     */
    public function index()
    {
        $user = Auth::user();
        $recentAudits = collect([]);
        $totalAudits = 0;
        $avgPerformanceScore = 0;

        if ($user) {
            $recentAudits = PageSpeedAudit::forUser($user->id)
                ->orderBy('analyzed_at', 'desc')
                ->take(5)
                ->get();
                
            $totalAudits = PageSpeedAudit::forUser($user->id)->count();
            $avgPerformanceScore = PageSpeedAudit::forUser($user->id)
                ->whereNotNull('performance_score')
                ->avg('performance_score') ?? 0;
        }

        return view('tools.page-speed', [
            'recentAudits' => $recentAudits,
            'totalAudits' => $totalAudits,
            'avgPerformanceScore' => round($avgPerformanceScore, 1),
            'isDemo' => !$user
        ]);
    }

    /**
     * Analyze URL for page speed.
     */
    public function analyze(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|string|url|max:2048',
            'strategy' => 'required|in:desktop,mobile',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input provided.',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $url = $request->input('url');
        $strategy = $request->input('strategy');

        // For demo mode, return mock data
        if (!$user) {
            return $this->getDemoData($url, $strategy);
        }

        try {
            $result = $this->pageSpeedService->analyzeUrl($url, $strategy, $user->id);

            if ($result['success']) {
                $message = $result['message'] ?? 'Page speed analysis completed successfully.';
                
                // Add context about data source
                if (isset($result['source'])) {
                    if ($result['source'] === 'fallback') {
                        $message = $result['message'] ?? 'Analysis completed using simulated data due to API limitations.';
                    } elseif ($result['source'] === 'google_api') {
                        $message = 'Analysis completed using real Google PageSpeed Insights data.';
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => $result['data'],
                    'source' => $result['source'] ?? 'api'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Analysis failed.'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during analysis: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show audit history.
     */
    public function history()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        $audits = $this->pageSpeedService->getAuditHistory($user->id, 20);

        return view('tools.page-speed-history', [
            'audits' => $audits
        ]);
    }

    /**
     * Show detailed audit report.
     */
    public function show($id)
    {
        $user = Auth::user();
        $audit = $this->pageSpeedService->getAudit($id, $user ? $user->id : null);

        if (!$audit) {
            abort(404, 'Audit not found.');
        }

        $recommendations = $this->pageSpeedService->getRecommendations($audit);

        return view('tools.page-speed-report', [
            'audit' => $audit,
            'recommendations' => $recommendations
        ]);
    }

    /**
     * Export audit report as PDF.
     */
    public function exportPdf($id)
    {
        $user = Auth::user();
        $audit = $this->pageSpeedService->getAudit($id, $user ? $user->id : null);

        if (!$audit) {
            abort(404, 'Audit not found.');
        }

        $recommendations = $this->pageSpeedService->getRecommendations($audit);

        $pdf = Pdf::loadView('reports.page-speed-pdf', [
            'audit' => $audit,
            'recommendations' => $recommendations
        ]);

        $filename = 'page-speed-report-' . $audit->id . '-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Delete an audit.
     */
    public function delete($id): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required.'
            ], 401);
        }

        $deleted = $this->pageSpeedService->deleteAudit($id, $user->id);

        if ($deleted) {
            return response()->json([
                'success' => true,
                'message' => 'Audit deleted successfully.'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Audit not found or cannot be deleted.'
            ], 404);
        }
    }

    /**
     * Get demo data for non-authenticated users.
     */
    private function getDemoData(string $url, string $strategy): JsonResponse
    {
        // Simulate realistic but varied demo data
        $performanceScore = rand(65, 95);
        $baseTime = $strategy === 'mobile' ? 1.5 : 1.0;
        
        $demoData = [
            'url' => $url,
            'strategy' => $strategy,
            'performance_score' => $performanceScore,
            'accessibility_score' => rand(85, 98),
            'best_practices_score' => rand(80, 95),
            'seo_score' => rand(88, 100),
            'first_contentful_paint' => round($baseTime + (rand(0, 100) / 100), 2),
            'largest_contentful_paint' => round($baseTime * 1.8 + (rand(0, 150) / 100), 2),
            'total_blocking_time' => rand(50, 400),
            'cumulative_layout_shift' => round(rand(1, 25) / 100, 3),
            'speed_index' => round($baseTime * 2.2 + (rand(0, 200) / 100), 2),
            'first_meaningful_paint' => round($baseTime * 1.1 + (rand(0, 80) / 100), 2),
            'time_to_interactive' => round($baseTime * 3.5 + (rand(0, 300) / 100), 2),
            'max_potential_fid' => rand(30, 180),
            'page_title' => parse_url($url, PHP_URL_HOST),
            'analyzed_at' => now()->toISOString(),
            'lighthouse_version' => '10.4.0',
            'demo' => true
        ];

        return response()->json([
            'success' => true,
            'message' => 'Demo analysis completed. Sign up for real-time data and history tracking.',
            'data' => $demoData,
            'demo' => true
        ]);
    }
}
