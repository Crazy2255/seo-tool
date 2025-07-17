<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompetitorAnalysisController;
use App\Http\Controllers\SeoAuditController;
use App\Http\Controllers\KeywordController;
use App\Http\Controllers\BacklinkController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LeadMagnetController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\DebugJsonController;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::get('/register-debug', function() { 
    return view('auth.register-debug'); 
})->name('register.debug');

// Test CSRF route
Route::post('/test-csrf', function(Illuminate\Http\Request $request) {
    return response()->json(['success' => true, 'message' => 'CSRF test passed', 'data' => $request->all()]);
})->name('test.csrf');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Debug route
Route::get('/debug-auth', function() {
    return view('debug_auth');
})->name('debug.auth');

// Password Reset Routes
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendPasswordResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.store');

// Profile Routes (Authenticated)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
});

// Dashboard Routes
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.home');

// SEO Tools Routes
Route::prefix('tools')->group(function () {
    Route::get('/site-audit', [App\Http\Controllers\SiteAuditController::class, 'index'])->name('tools.site-audit');
    Route::get('/keyword-tracker', [DashboardController::class, 'keywordTracker'])->name('tools.keyword-tracker');
    Route::get('/backlink-checker', [DashboardController::class, 'backlinkChecker'])->name('tools.backlink-checker');
    Route::get('/meta-analyzer', [App\Http\Controllers\MetaAnalyzerController::class, 'index'])->name('tools.meta-analyzer');
    Route::get('/page-speed', [App\Http\Controllers\PageSpeedController::class, 'index'])->name('tools.page-speed');
    Route::get('/serp-preview', [DashboardController::class, 'serpPreview'])->name('tools.serp-preview');
    Route::get('/image-alt-checker', [App\Http\Controllers\ImageAltController::class, 'index'])->name('tools.image-alt-checker');
    Route::get('/lead-magnet-builder', [LeadMagnetController::class, 'index'])->name('tools.lead-magnet-builder');
    Route::get('/sitemap-checker', [DashboardController::class, 'sitemapChecker'])->name('tools.sitemap-checker');
    Route::get('/reports', [DashboardController::class, 'reports'])->name('tools.reports');
});

// Page Speed Tool Routes
Route::prefix('tools/page-speed')->group(function () {
    Route::get('/history', [App\Http\Controllers\PageSpeedController::class, 'history'])->name('tools.page-speed.history')->middleware('auth');
    Route::get('/{id}', [App\Http\Controllers\PageSpeedController::class, 'show'])->name('tools.page-speed.show');
    Route::get('/{id}/pdf', [App\Http\Controllers\PageSpeedController::class, 'exportPdf'])->name('tools.page-speed.pdf');
});

// Image ALT Text Checker Routes
Route::prefix('tools/image-alt')->group(function () {
    Route::get('/history', [App\Http\Controllers\ImageAltController::class, 'history'])->name('tools.image-alt.history')->middleware('auth');
    Route::get('/{id}', [App\Http\Controllers\ImageAltController::class, 'show'])->name('tools.image-alt.show');
    Route::get('/{id}/pdf', [App\Http\Controllers\ImageAltController::class, 'exportPdf'])->name('tools.image-alt.pdf');
    Route::get('/{id}/csv', [App\Http\Controllers\ImageAltController::class, 'exportCsv'])->name('tools.image-alt.csv');
});

// Lead Magnet Builder Routes
Route::prefix('lead-magnets')->middleware('auth')->group(function () {
    Route::get('/', [LeadMagnetController::class, 'index'])->name('lead-magnets.index');
    Route::post('/', [LeadMagnetController::class, 'store'])->name('lead-magnets.store');
    Route::get('/analytics/dashboard', [LeadMagnetController::class, 'analytics'])->name('lead-magnets.analytics');
    Route::get('/export-all', [LeadMagnetController::class, 'exportAllLeads'])->name('lead-magnets.export-all');
    Route::get('/{leadMagnet}', [LeadMagnetController::class, 'show'])->name('lead-magnets.show');
    Route::put('/{leadMagnet}', [LeadMagnetController::class, 'update'])->name('lead-magnets.update');
    Route::delete('/{leadMagnet}', [LeadMagnetController::class, 'destroy'])->name('lead-magnets.destroy');
    Route::get('/{leadMagnet}/export', [LeadMagnetController::class, 'exportLeads'])->name('lead-magnets.export-leads');
    Route::post('/{leadMagnet}/bulk-invite', [LeadMagnetController::class, 'sendBulkInvites'])->name('lead-magnets.bulk-invite');
});

// Public Lead Magnet Landing Pages (no auth required)
Route::prefix('magnet')->group(function () {
    Route::get('/{slug}', [LeadController::class, 'landing'])->name('lead.landing');
    Route::post('/{slug}/submit', [LeadController::class, 'submitForm'])->name('lead.submit');
    Route::get('/{slug}/download/{token}', [LeadController::class, 'download'])->name('lead.download');
    Route::get('/{slug}/thank-you', [LeadController::class, 'thankYou'])->name('lead.thank-you');
});

// Site Audit Routes
Route::prefix('site-audit')->middleware('auth')->group(function () {
    Route::get('/history', [App\Http\Controllers\SiteAuditController::class, 'history'])->name('site-audit.history');
    Route::get('/{id}', [App\Http\Controllers\SiteAuditController::class, 'show'])->name('site-audit.show');
});

// Meta Analyzer Routes
Route::prefix('meta-analyzer')->group(function () {
    Route::get('/', [App\Http\Controllers\MetaAnalyzerController::class, 'index'])->name('meta-analyzer.index');
    Route::get('/history', [App\Http\Controllers\MetaAnalyzerController::class, 'auditHistory'])->name('meta-analyzer.history')->middleware('auth');
    Route::get('/audit/{id}', [App\Http\Controllers\MetaAnalyzerController::class, 'showAudit'])->name('meta-analyzer.show')->middleware('auth');
    Route::get('/audit/{id}/pdf', [App\Http\Controllers\MetaAnalyzerController::class, 'generatePdfReport'])->name('meta-analyzer.pdf')->middleware('auth');
    Route::get('/audit/latest/pdf', [App\Http\Controllers\MetaAnalyzerController::class, 'generateLatestPdfReport'])->name('meta-analyzer.latest-pdf')->middleware('auth');
});

// Competitor Analysis Routes
Route::prefix('tools')->group(function () {
    Route::get('/competitor-analysis', [CompetitorAnalysisController::class, 'index'])->name('competitor-analysis.index');
    Route::middleware('auth')->group(function () {
        Route::get('/competitor-analysis/history', [CompetitorAnalysisController::class, 'history'])->name('competitor-analysis.history');
        Route::get('/competitor-analysis/{competitorInsight}', [CompetitorAnalysisController::class, 'show'])->name('competitor-analysis.show');
        Route::get('/competitor-analysis/{competitorInsight}/export', [CompetitorAnalysisController::class, 'exportAnalysis'])->name('competitor-analysis.export');
    });
});

// Competitor Analysis API Routes - Protected
Route::middleware('auth')->group(function () {
    Route::post('/api/competitor-analysis/analyze', [CompetitorAnalysisController::class, 'analyze'])->name('competitor-analysis.analyze');
    Route::delete('/api/competitor-analysis/{competitorInsight}', [CompetitorAnalysisController::class, 'delete'])->name('competitor-analysis.delete');
    Route::post('/api/competitor-analysis/{competitorInsight}/rescan', [CompetitorAnalysisController::class, 'rescan'])->name('competitor-analysis.rescan');
});

// Additional web routes
Route::get('/download-report/{id}', [SeoAuditController::class, 'downloadReport'])->name('audit.download');

// Site Audit API routes - using web routes for proper session authentication
Route::middleware('auth')->group(function () {
    Route::post('/api/site-audit', [App\Http\Controllers\SiteAuditController::class, 'runAudit']);
    Route::delete('/api/site-audit/{id}', [App\Http\Controllers\SiteAuditController::class, 'destroy']);
    
    // Meta Analyzer API routes
    Route::post('/api/meta-analyzer', [App\Http\Controllers\MetaAnalyzerController::class, 'analyzeUrl']);
    Route::delete('/api/meta-analyzer/audit/{id}', [App\Http\Controllers\MetaAnalyzerController::class, 'deleteAudit']);
    
    // Page Speed API routes
    Route::post('/api/page-speed/analyze', [App\Http\Controllers\PageSpeedController::class, 'analyze']);
    Route::delete('/api/page-speed/{id}', [App\Http\Controllers\PageSpeedController::class, 'delete']);
    
    // Image ALT Text Checker API routes
    Route::post('/api/image-alt/analyze', [App\Http\Controllers\ImageAltController::class, 'analyze']);
    Route::delete('/api/image-alt/{id}', [App\Http\Controllers\ImageAltController::class, 'delete']);
    
    // Lead Magnet API routes
    Route::post('/api/lead-magnets', [LeadMagnetController::class, 'store']);
    Route::delete('/api/lead-magnets/{leadMagnet}', [LeadMagnetController::class, 'destroy']);
    Route::put('/api/lead-magnets/{leadMagnet}', [LeadMagnetController::class, 'update']);
});

// Image ALT Text Checker API routes (public for demo mode)
Route::post('/api/image-alt/analyze', [App\Http\Controllers\ImageAltController::class, 'analyze']);

// Test JSON response routes (for debugging)
Route::get('/test/json', [App\Http\Controllers\TestController::class, 'testJson']);
Route::match(['GET', 'POST'], '/test/bulk-invite', [App\Http\Controllers\TestController::class, 'testBulkInvite']);
Route::post('/test/actual-bulk-invite', [App\Http\Controllers\TestController::class, 'testActualBulkInvite']);
Route::get('/test/upload', function () {
    return view('test-upload');
});
Route::get('/test-bulk-invite-page', function () {
    return view('test-bulk-invite');
});
Route::get('/bulk-invite-fixed', function () {
    return view('bulk-invite-test-fixed');
});
Route::get('/test/email', [App\Http\Controllers\TestController::class, 'testEmailSending']);
Route::get('/working-bulk-test', function () {
    return view('working-bulk-test');
});

// Debug JSON routes
Route::prefix('debug-json')->group(function () {
    Route::get('/competitor-analysis', [DebugJsonController::class, 'competitorAnalysis'])->name('debug.json.competitor-analysis');
    Route::get('/site-audit', [DebugJsonController::class, 'siteAudit'])->name('debug.json.site-audit');
    Route::get('/meta-analyzer', [DebugJsonController::class, 'metaAnalyzer'])->name('debug.json.meta-analyzer');
    Route::get('/page-speed', [DebugJsonController::class, 'pageSpeed'])->name('debug.json.page-speed');
    Route::get('/image-alt', [DebugJsonController::class, 'imageAlt'])->name('debug.json.image-alt');
    Route::get('/lead-magnets', [DebugJsonController::class, 'leadMagnets'])->name('debug.json.lead-magnets');
});

// Debug JSON clean output routes
Route::get('/debug/clean-json', [DebugJsonController::class, 'testCleanJson']);
Route::get('/debug/dirty-json', [DebugJsonController::class, 'testWithOutput']);
Route::get('/debug/raw-json', [DebugJsonController::class, 'testRawJson']);
Route::match(['GET', 'POST'], '/debug/bulk-simulation', [DebugJsonController::class, 'testBulkInviteSimulation']);

// Test email sending (simple version)
Route::get('/test/email', function () {
    try {
        // Test basic mail configuration
        Mail::raw('This is a test email from your Laravel application.', function ($message) {
            $message->to('test@example.com')
                    ->subject('Test Email from Laravel');
        });
        
        return response()->json(['success' => 'Test email sent successfully!']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
})->name('test.email');

// Test download functionality
Route::get('/test/download', function () {
    try {
        $leadMagnets = App\Models\LeadMagnet::all();
        $leads = App\Models\Lead::all();
        
        if ($leadMagnets->isEmpty()) {
            return response()->json(['error' => 'No lead magnets found']);
        }
        
        $leadMagnet = $leadMagnets->first();
        
        if ($leads->isEmpty()) {
            return response()->json(['error' => 'No leads found']);
        }
        
        $lead = $leads->first();
        
        // Generate download token
        $data = $lead->id . '|' . $lead->email . '|' . $lead->created_at->timestamp;
        $token = base64_encode($data . '|' . hash('sha256', $data . config('app.key')));
        
        $downloadUrl = route('lead.download', ['slug' => $leadMagnet->slug, 'token' => $token]);
        
        return response()->json([
            'lead_magnet' => $leadMagnet->only(['id', 'title', 'slug', 'file_path', 'file_name']),
            'lead' => $lead->only(['id', 'email']),
            'download_url' => $downloadUrl,
            'file_exists' => Storage::disk('public')->exists($leadMagnet->file_path)
        ]);
        
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
})->name('test.download');
