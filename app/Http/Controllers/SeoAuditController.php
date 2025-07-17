<?php

namespace App\Http\Controllers;

use App\Models\SeoAudit;
use App\Models\Keyword;
use App\Models\Backlink;
use App\Models\AuditReport;
use App\Services\SeoAuditService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class SeoAuditController extends Controller
{
    protected SeoAuditService $seoAuditService;

    public function __construct(SeoAuditService $seoAuditService)
    {
        $this->seoAuditService = $seoAuditService;
    }

    /**
     * Display the main dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $recentAudits = SeoAudit::byUser($user->id)->recent()->take(5)->get();
        $totalAudits = SeoAudit::byUser($user->id)->count();
        $totalKeywords = Keyword::byUser($user->id)->count();
        $totalBacklinks = Backlink::byUser($user->id)->count();

        return view('dashboard', compact('recentAudits', 'totalAudits', 'totalKeywords', 'totalBacklinks'));
    }

    /**
     * Store a new SEO audit
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|url|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $url = $request->input('url');
            
            // Perform SEO audit
            $auditData = $this->seoAuditService->performAudit($url);
            
            // Add user_id only if user is authenticated
            if (Auth::check()) {
                $auditData['user_id'] = Auth::id();
            }
            $auditData['audit_date'] = now();

            $audit = SeoAudit::create($auditData);

            return response()->json([
                'success' => true,
                'message' => 'SEO audit completed successfully',
                'data' => $audit
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error performing SEO audit: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all audits for the authenticated user
     */
    public function getUserAudits(): JsonResponse
    {
        $user = Auth::user();
        $audits = SeoAudit::byUser($user->id)
            ->recent()
            ->with(['keywords', 'backlinks'])
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $audits
        ]);
    }

    /**
     * Get a specific audit by ID
     */
    public function show($id): JsonResponse
    {
        $user = Auth::user();
        $audit = SeoAudit::byUser($user->id)
            ->with(['keywords', 'backlinks', 'auditReports'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $audit
        ]);
    }

    /**
     * Delete an audit
     */
    public function destroy($id): JsonResponse
    {
        $user = Auth::user();
        $audit = SeoAudit::byUser($user->id)->findOrFail($id);
        $audit->delete();

        return response()->json([
            'success' => true,
            'message' => 'Audit deleted successfully'
        ]);
    }

    /**
     * Generate PDF report for an audit
     */
    public function generateReport($id, Request $request)
    {
        $user = Auth::user();
        $audit = SeoAudit::byUser($user->id)->with(['keywords', 'backlinks'])->findOrFail($id);

        $pdf = PDF::loadView('reports.audit-pdf', compact('audit'));
        
        $fileName = 'seo-audit-' . $audit->id . '-' . date('Y-m-d') . '.pdf';
        $filePath = storage_path('app/public/reports/' . $fileName);
        
        // Create directory if it doesn't exist
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        $pdf->save($filePath);

        // Save report record
        AuditReport::create([
            'seo_audit_id' => $audit->id,
            'user_id' => $user->id,
            'report_type' => 'pdf',
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_size' => filesize($filePath),
            'generated_date' => now(),
            'download_count' => 0
        ]);

        return response()->download($filePath, $fileName);
    }

    /**
     * Site audit tool - comprehensive SEO analysis
     */
    public function siteAudit(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|url'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $results = $this->seoAuditService->performSiteAudit($request->url);
            
            return response()->json([
                'success' => true,
                'data' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Meta tag analyzer
     */
    public function metaAnalyzer(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|url'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $results = $this->seoAuditService->analyzeMetaTags($request->url);
            
            return response()->json([
                'success' => true,
                'data' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Page speed checker
     */
    public function pageSpeedCheck(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|url'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $results = $this->seoAuditService->checkPageSpeed($request->url);
            
            return response()->json([
                'success' => true,
                'data' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Broken link checker
     */
    public function brokenLinkCheck(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|url'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $results = $this->seoAuditService->checkBrokenLinks($request->url);
            
            return response()->json([
                'success' => true,
                'data' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
