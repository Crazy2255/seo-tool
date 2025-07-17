<?php

namespace App\Http\Controllers;

use App\Models\SeoAudit;
use App\Services\SeoAuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SiteAuditController extends Controller
{
    protected SeoAuditService $seoAuditService;

    public function __construct(SeoAuditService $seoAuditService)
    {
        $this->middleware('auth');
        $this->seoAuditService = $seoAuditService;
    }

    /**
     * Display the site audit tool
     */
    public function index()
    {
        $recentAudits = SeoAudit::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('tools.site-audit', compact('recentAudits'));
    }

    /**
     * Display audit history
     */
    public function history()
    {
        $audits = SeoAudit::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('tools.site-audit-history', compact('audits'));
    }

    /**
     * Show specific audit details
     */
    public function show($id)
    {
        $audit = SeoAudit::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('tools.site-audit-detail', compact('audit'));
    }

    /**
     * Run a new site audit
     */
    public function runAudit(Request $request)
    {
        $request->validate([
            'url' => 'required|url'
        ]);

        try {
            $auditData = $this->seoAuditService->performAudit($request->url);
            
            // Ensure arrays are properly formatted and safely cast
            $auditData['h1_tags'] = is_array($auditData['h1_tags']) ? $auditData['h1_tags'] : [];
            $auditData['h2_tags'] = is_array($auditData['h2_tags']) ? $auditData['h2_tags'] : [];
            $auditData['schema_markup'] = is_array($auditData['schema_markup']) ? $auditData['schema_markup'] : [];
            $auditData['social_meta_tags'] = is_array($auditData['social_meta_tags']) ? $auditData['social_meta_tags'] : [];
            $auditData['broken_links'] = is_array($auditData['broken_links']) ? $auditData['broken_links'] : [];
            $auditData['recommendations'] = is_array($auditData['recommendations']) ? $auditData['recommendations'] : [];
            
            // Convert boolean values to proper format
            $auditData['ssl_certificate'] = (bool) ($auditData['ssl_certificate'] ?? false);
            $auditData['mobile_friendly'] = (bool) ($auditData['mobile_friendly'] ?? false);
            
            // Save to database
            $audit = SeoAudit::create([
                'user_id' => Auth::id(),
                'url' => $auditData['url'],
                'title' => $auditData['title'],
                'meta_description' => $auditData['meta_description'],
                'h1_tags' => $auditData['h1_tags'],
                'h2_tags' => $auditData['h2_tags'],
                'status_code' => $auditData['status_code'],
                'page_load_speed' => $auditData['page_load_speed'],
                'internal_links_count' => $auditData['internal_links_count'],
                'external_links_count' => $auditData['external_links_count'],
                'images_without_alt' => $auditData['images_without_alt'],
                'images_count' => $auditData['images_count'],
                'word_count' => $auditData['word_count'],
                'meta_keywords' => $auditData['meta_keywords'],
                'canonical_url' => $auditData['canonical_url'],
                'robots_meta' => $auditData['robots_meta'],
                'sitemap_url' => $auditData['sitemap_url'],
                'robots_txt_status' => $auditData['robots_txt_status'],
                'ssl_certificate' => $auditData['ssl_certificate'],
                'mobile_friendly' => $auditData['mobile_friendly'],
                'schema_markup' => $auditData['schema_markup'],
                'social_meta_tags' => $auditData['social_meta_tags'],
                'broken_links' => $auditData['broken_links'],
                'audit_score' => $auditData['audit_score'],
                'recommendations' => $auditData['recommendations'],
                'audit_date' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Audit completed successfully',
                'data' => $auditData,
                'audit_id' => $audit->id
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
    public function destroy($id)
    {
        $audit = SeoAudit::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $audit->delete();

        return response()->json([
            'success' => true,
            'message' => 'Audit deleted successfully'
        ]);
    }
}
