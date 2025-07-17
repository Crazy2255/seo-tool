<?php

namespace App\Http\Controllers;

use App\Models\CompetitorInsight;
use App\Services\CompetitorAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * CompetitorAnalysisController handles competitor analysis functionalities.
 * It allows users to perform analyses, view recent analyses, and manage their insights.
 * This controller provides methods for analyzing competitor websites, viewing analysis details,
 * managing analysis history, and exporting analysis reports as PDFs.
 */
class CompetitorAnalysisController extends Controller
{
    protected $competitorAnalysisService;

    public function __construct(CompetitorAnalysisService $competitorAnalysisService)
    {
        $this->competitorAnalysisService = $competitorAnalysisService;
    }

    public function index()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return view('tools.competitor-analysis', [
                'recentAnalyses' => collect([]),
                'totalAnalyses' => 0,
                'avgStrategyScore' => 0,
                'totalToolsFound' => 0,
                'totalBacklinks' => 0,
                'totalKeywords' => 0,
                'isDemo' => true
            ]);
        }
        
        $user = Auth::user();
        
        // Get recent analyses
        $recentAnalyses = CompetitorInsight::forUser($user->id)
            ->completed()
            ->orderBy('last_scanned_at', 'desc')
            ->take(10)
            ->get();
        
        // Get statistics
        $totalAnalyses = CompetitorInsight::forUser($user->id)->completed()->count();
        $avgStrategyScore = CompetitorInsight::forUser($user->id)
            ->completed()
            ->avg('strategy_score') ?? 0;
        $totalToolsFound = CompetitorInsight::forUser($user->id)
            ->completed()
            ->sum('total_tools_detected');
        $totalBacklinks = CompetitorInsight::forUser($user->id)
            ->completed()
            ->sum('total_backlinks');
        $totalKeywords = CompetitorInsight::forUser($user->id)
            ->completed()
            ->sum('total_keywords');
        
        return view('tools.competitor-analysis', [
            'recentAnalyses' => $recentAnalyses,
            'totalAnalyses' => $totalAnalyses,
            'avgStrategyScore' => round($avgStrategyScore, 1),
            'totalToolsFound' => $totalToolsFound,
            'totalBacklinks' => $totalBacklinks,
            'totalKeywords' => $totalKeywords,
            'isDemo' => false
        ]);
    }

    public function analyze(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|string|url|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid URL provided.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please log in to perform competitor analysis.',
                'demo' => true
            ], 401);
        }

        try {
            $user = Auth::user();
            $url = $request->input('url');
            
            // Check if analysis already exists and is recent
            $domain = parse_url($url, PHP_URL_HOST);
            $existingAnalysis = CompetitorInsight::forUser($user->id)
                ->where('domain', $domain)
                ->where('last_scanned_at', '>=', now()->subHours(24))
                ->first();
            
            if ($existingAnalysis) {
                return response()->json([
                    'success' => true,
                    'message' => 'Using recent analysis for this domain.',
                    'data' => $existingAnalysis,
                    'redirect' => route('competitor-analysis.show', $existingAnalysis->id)
                ]);
            }
            
            // Perform analysis
            $analysis = $this->competitorAnalysisService->analyzeCompetitor($url, $user->id);
            
            if ($analysis->scan_status === 'completed') {
                return response()->json([
                    'success' => true,
                    'message' => 'Analysis completed successfully.',
                    'data' => $analysis,
                    'redirect' => route('competitor-analysis.show', $analysis->id)
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Analysis failed: ' . $analysis->scan_error,
                    'data' => $analysis
                ], 400);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during analysis: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(CompetitorInsight $competitorInsight)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            abort(401, 'Authentication required to view analysis.');
        }
        
        // Ensure user owns this analysis
        if ($competitorInsight->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this analysis.');
        }
        
        return view('tools.competitor-analysis-detail', [
            'analysis' => $competitorInsight
        ]);
    }

    public function history()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Please log in to view your analysis history.');
        }
        
        $user = Auth::user();
        
        $analyses = CompetitorInsight::forUser($user->id)
            ->orderBy('last_scanned_at', 'desc')
            ->paginate(20);
        
        return view('tools.competitor-analysis-history', [
            'analyses' => $analyses
        ]);
    }

    public function delete(CompetitorInsight $competitorInsight): JsonResponse
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please log in to delete analyses.'
            ], 401);
        }
        
        // Ensure user owns this analysis
        if ($competitorInsight->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this analysis.'
            ], 403);
        }
        
        try {
            $competitorInsight->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Analysis deleted successfully.'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete analysis: ' . $e->getMessage()
            ], 500);
        }
    }

    public function rescan(CompetitorInsight $competitorInsight): JsonResponse
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please log in to rescan analyses.'
            ], 401);
        }
        
        // Ensure user owns this analysis
        if ($competitorInsight->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this analysis.'
            ], 403);
        }
        
        try {
            $analysis = $this->competitorAnalysisService->analyzeCompetitor(
                $competitorInsight->url, 
                $competitorInsight->user_id
            );
            
            if ($analysis->scan_status === 'completed') {
                return response()->json([
                    'success' => true,
                    'message' => 'Analysis updated successfully.',
                    'data' => $analysis
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Analysis failed: ' . $analysis->scan_error,
                    'data' => $analysis
                ], 400);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during rescan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportAnalysis(CompetitorInsight $competitorInsight)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            abort(401, 'Please log in to export analyses.');
        }
        
        // Ensure user owns this analysis
        if ($competitorInsight->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this analysis.');
        }
        
        // Generate PDF using Laravel DomPDF
        $pdf = Pdf::loadView('pdfs.competitor-analysis', ['analysis' => $competitorInsight]);
        
        $filename = 'competitor_analysis_' . $competitorInsight->domain . '_' . now()->format('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }
}
