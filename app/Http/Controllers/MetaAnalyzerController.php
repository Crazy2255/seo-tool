<?php

namespace App\Http\Controllers;

use App\Services\MetaAnalyzerService;
use App\Models\MetaTagAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class MetaAnalyzerController extends Controller
{
    protected MetaAnalyzerService $metaAnalyzerService;

    public function __construct(MetaAnalyzerService $metaAnalyzerService)
    {
        $this->middleware('auth')->except(['index']); // Allow guest access to index page
        $this->metaAnalyzerService = $metaAnalyzerService;
    }

    /**
     * Show the meta analyzer page
     */
    public function index()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return view('tools.meta-analyzer', [
                'recentAudits' => collect([]),
                'isDemo' => true
            ]);
        }
        
        $user = Auth::user();
        
        $recentAudits = MetaTagAudit::where('user_id', $user->id)
            ->orderBy('analyzed_at', 'desc')
            ->limit(5)
            ->get();

        return view('tools.meta-analyzer', compact('recentAudits'));
    }

    /**
     * Analyze meta tags for a given URL (API endpoint)
     */
    public function analyzeUrl(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|url|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid URL provided',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $url = $request->input('url');
            $userId = Auth::id(); // Always use authenticated user

            // Analyze the URL and save to database
            $analysis = $this->metaAnalyzerService->analyzeUrl($url, $userId);

            return response()->json([
                'success' => true,
                'data' => $analysis,
                'message' => 'Meta tags analyzed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get audit history for the authenticated user
     */
    public function getAuditHistory(Request $request)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please log in to view audit history.'
            ], 401);
        }
        
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        try {
            $limit = $request->input('limit', 10);
            $audits = $this->metaAnalyzerService->getAuditHistory($user->id, $limit);

            return response()->json([
                'success' => true,
                'data' => $audits
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific audit by ID
     */
    public function getAudit(Request $request, $id)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please log in to view audit details.'
            ], 401);
        }
        
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        try {
            $audit = MetaTagAudit::where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$audit) {
                return response()->json([
                    'success' => false,
                    'message' => 'Audit not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $audit
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an audit
     */
    public function deleteAudit(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        try {
            $deleted = $this->metaAnalyzerService->deleteAudit($id, $user->id);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Audit not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Audit deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate and download PDF report
     */
    public function generatePdfReport(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        try {
            $audit = MetaTagAudit::where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$audit) {
                return response()->json([
                    'success' => false,
                    'message' => 'Audit not found'
                ], 404);
            }

            // Generate PDF
            $pdf = Pdf::loadView('reports.meta-audit-pdf', compact('audit'));
            
            $filename = 'meta-audit-' . parse_url($audit->url, PHP_URL_HOST) . '-' . $audit->analyzed_at->format('Y-m-d') . '.pdf';
            
            return $pdf->download($filename);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate PDF report for the latest audit
     */
    public function generateLatestPdfReport()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        try {
            // Get the latest audit for this user
            $audit = MetaTagAudit::where('user_id', $user->id)
                ->orderBy('analyzed_at', 'desc')
                ->first();

            if (!$audit) {
                return redirect()->back()->with('error', 'No audit found to generate PDF report');
            }

            // Generate PDF
            $pdf = Pdf::loadView('reports.meta-audit-pdf', compact('audit'));
            
            return $pdf->download('meta-audit-report-latest.pdf');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to generate PDF report: ' . $e->getMessage());
        }
    }

    /**
     * Quick analyze without authentication (for demo purposes)
     */
    public function quickAnalyze(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|url|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid URL provided',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $url = $request->input('url');

            // Analyze without saving to database
            $analysis = $this->metaAnalyzerService->analyzeUrl($url);

            return response()->json([
                'success' => true,
                'data' => $analysis,
                'message' => 'Meta tags analyzed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show audit history page - only recent audits
     */
    public function auditHistory()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Show only recent audits (last 30 days or last 50 audits, whichever is smaller)
        $thirtyDaysAgo = now()->subDays(30);
        
        $audits = MetaTagAudit::where('user_id', $user->id)
            ->where('analyzed_at', '>=', $thirtyDaysAgo)
            ->orderBy('analyzed_at', 'desc')
            ->limit(50) // Limit to 50 most recent audits
            ->paginate(20);

        return view('tools.meta-audit-history', compact('audits'));
    }

    /**
     * Show detailed audit view
     */
    public function showAudit($id)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        $audit = MetaTagAudit::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$audit) {
            abort(404);
        }

        return view('tools.meta-audit-detail', compact('audit'));
    }
}
