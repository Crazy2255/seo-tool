<?php

namespace App\Http\Controllers;

use App\Models\SeoAudit;
use App\Models\Keyword;
use App\Models\Backlink;
use App\Models\AuditReport;
use App\Models\MetaTagAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the main dashboard
     */
    public function index()
    {
        // Require authentication for real data
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Get dashboard statistics
        $stats = $this->getDashboardStats($user->id);
        
        return view('dashboard.index', compact('user', 'stats'));
    }

    /**
     * Keyword Tracker Page
     */
    public function keywordTracker()
    {
        return view('tools.keyword-tracker');
    }

    /**
     * Backlink Checker Page
     */
    public function backlinkChecker()
    {
        return view('tools.backlink-checker');
    }

    /**
     * Page Speed Checker Page - redirect to dedicated controller
     */
    public function pageSpeed()
    {
        return redirect()->route('tools.page-speed.index');
    }

    /**
     * SERP Preview Tool Page
     */
    public function serpPreview()
    {
        return view('tools.serp-preview');
    }

    /**
     * Image Alt Checker Page
     */
    public function imageAltChecker()
    {
        return view('tools.image-alt-checker');
    }

    /**
     * Sitemap Checker Page
     */
    public function sitemapChecker()
    {
        return view('tools.sitemap-checker');
    }

    /**
     * Reports Page
     */
    public function reports()
    {
        return view('tools.reports');
    }

    /**
     * Broken Links Checker Page
     */
    public function brokenLinks()
    {
        return view('tools.broken-links');
    }

    /**
     * Get dashboard statistics based on real user data
     */
    private function getDashboardStats($userId)
    {
        // Get total counts
        $totalAudits = SeoAudit::where('user_id', $userId)->count();
        $totalMetaAudits = MetaTagAudit::where('user_id', $userId)->count();
        $totalKeywords = Keyword::where('user_id', $userId)->count();
        $totalBacklinks = Backlink::where('user_id', $userId)->count();
        
        // Calculate average audit score from both SeoAudit and MetaTagAudit
        $avgSeoScore = SeoAudit::where('user_id', $userId)
            ->whereNotNull('audit_score')
            ->avg('audit_score');
        
        $avgMetaScore = MetaTagAudit::where('user_id', $userId)
            ->whereNotNull('audit_score')
            ->avg('audit_score');
            
        // Combine scores if both exist, otherwise use what's available
        $avgAuditScore = 0;
        if ($avgSeoScore && $avgMetaScore) {
            $avgAuditScore = ($avgSeoScore + $avgMetaScore) / 2;
        } elseif ($avgSeoScore) {
            $avgAuditScore = $avgSeoScore;
        } elseif ($avgMetaScore) {
            $avgAuditScore = $avgMetaScore;
        }
        
        // Get recent audits from both tables
        $recentSeoAudits = SeoAudit::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get(['id', 'url', 'audit_score', 'created_at'])
            ->map(function ($audit) {
                return [
                    'id' => $audit->id,
                    'url' => $audit->url,
                    'audit_score' => $audit->audit_score ?? 0,
                    'created_at' => $audit->created_at->format('Y-m-d H:i:s'),
                    'type' => 'seo'
                ];
            });
            
        $recentMetaAudits = MetaTagAudit::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get(['id', 'url', 'audit_score', 'created_at'])
            ->map(function ($audit) {
                return [
                    'id' => $audit->id,
                    'url' => $audit->url,
                    'audit_score' => $audit->audit_score ?? 0,
                    'created_at' => $audit->created_at->format('Y-m-d H:i:s'),
                    'type' => 'meta'
                ];
            });
            
        // Combine and sort recent audits
        $recentAudits = collect($recentSeoAudits)
            ->concat($recentMetaAudits)
            ->sortByDesc('created_at')
            ->take(5)
            ->values()
            ->toArray();
        
        // Get top keywords with position changes
        $topKeywords = Keyword::where('user_id', $userId)
            ->whereNotNull('current_position')
            ->orderBy('current_position', 'asc')
            ->take(3)
            ->get(['keyword', 'current_position', 'previous_position'])
            ->map(function ($keyword) {
                $change = 0;
                if ($keyword->previous_position && $keyword->current_position) {
                    $change = $keyword->previous_position - $keyword->current_position;
                }
                return [
                    'keyword' => $keyword->keyword,
                    'position' => $keyword->current_position,
                    'change' => $change
                ];
            })
            ->toArray();
            
        // Get recent backlinks
        $recentBacklinks = Backlink::where('user_id', $userId)
            ->orderBy('discovered_date', 'desc')
            ->take(3)
            ->get(['domain', 'domain_authority', 'discovered_date'])
            ->map(function ($backlink) {
                return [
                    'source_domain' => $backlink->domain,
                    'domain_authority' => $backlink->domain_authority ?? 0,
                    'discovered_date' => $backlink->discovered_date ? $backlink->discovered_date->format('Y-m-d') : now()->format('Y-m-d')
                ];
            })
            ->toArray();
        
        return [
            'total_audits' => $totalAudits + $totalMetaAudits,
            'total_keywords' => $totalKeywords,
            'total_backlinks' => $totalBacklinks,
            'avg_audit_score' => round($avgAuditScore, 1),
            'recent_audits' => $recentAudits,
            'top_keywords' => $topKeywords,
            'recent_backlinks' => $recentBacklinks
        ];
    }
}
