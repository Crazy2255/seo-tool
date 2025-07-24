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

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

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
    Route::get('/', [App\Http\Controllers\ToolsController::class, 'dashboard'])->name('tools.dashboard');
    Route::get('/site-audit', [App\Http\Controllers\SiteAuditController::class, 'index'])->name('tools.site-audit');
    Route::get('/keyword-tracker', [DashboardController::class, 'keywordTracker'])->name('tools.keyword-tracker');
    Route::get('/backlink-checker', [DashboardController::class, 'backlinkChecker'])->name('tools.backlink-checker');
    Route::get('/meta-analyzer', [App\Http\Controllers\MetaAnalyzerController::class, 'index'])->name('tools.meta-analyzer');
    Route::get('/page-speed', [App\Http\Controllers\PageSpeedController::class, 'index'])->name('tools.page-speed');
    Route::get('/serp-preview', [DashboardController::class, 'serpPreview'])->name('tools.serp-preview');
    Route::get('/image-alt-checker', [App\Http\Controllers\ImageAltController::class, 'index'])->name('tools.image-alt-checker');
    Route::get('/lead-magnet-builder', [LeadMagnetController::class, 'index'])->name('tools.lead-magnet-builder');
    Route::get('/sitemap-checker', [DashboardController::class, 'sitemapChecker'])->name('tools.sitemap-checker');
    Route::get('/web-builder', [DashboardController::class, 'webBuilder'])->name('tools.web-builder');
    // Add a route with underscore for backward compatibility
    Route::get('/web_builder', function() { return view('tools.redirect'); })->name('tools.web_builder');
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

// Landing Page Builder Routes
Route::middleware(['auth'])->group(function () {
    // Quick Access for Testing
    Route::get('/quick-access', function() {
        return view('quick-access');
    })->name('quick-access');
    
    // Landing Pages CRUD
    Route::resource('landing-pages', App\Http\Controllers\LandingPageController::class);
    Route::post('landing-pages/{landingPage}/duplicate', [App\Http\Controllers\LandingPageController::class, 'duplicate'])->name('landing-pages.duplicate');
    Route::get('landing-pages/{landingPage}/preview', [App\Http\Controllers\LandingPageController::class, 'preview'])->name('landing-pages.preview');
    Route::get('landing-pages/{landingPage}/analytics', [App\Http\Controllers\LandingPageController::class, 'analytics'])->name('landing-pages.analytics');
    
    // Page Builder Routes
    Route::get('landing-pages/{landingPage}/builder', [App\Http\Controllers\LandingPageBuilderController::class, 'builder'])->name('landing-pages.builder');
    Route::get('landing-pages/{landingPage}/web-builder', [App\Http\Controllers\LandingPageBuilderController::class, 'webBuilder'])->name('landing-pages.web-builder')->middleware('auth');
    Route::post('landing-pages/{landingPage}/save-content', [App\Http\Controllers\LandingPageBuilderController::class, 'saveContent'])->name('landing-pages.save-content');
    Route::post('landing-pages/upload-image', [App\Http\Controllers\LandingPageBuilderController::class, 'uploadImage'])->name('landing-pages.upload-image');
    Route::get('templates/{template}', [App\Http\Controllers\LandingPageBuilderController::class, 'getTemplate'])->name('templates.get');
    Route::post('landing-pages/{landingPage}/apply-template', [App\Http\Controllers\LandingPageBuilderController::class, 'applyTemplate'])->name('landing-pages.apply-template');
    Route::get('landing-pages/{landingPage}/export-html', [App\Http\Controllers\LandingPageBuilderController::class, 'exportHtml'])->name('landing-pages.export-html');
    Route::post('landing-pages/{landingPage}/submit-form', [App\Http\Controllers\LandingPageBuilderController::class, 'submitForm'])->name('landing-pages.submit-form');
    
    // Business Promotions CRUD
    Route::resource('business-promotions', App\Http\Controllers\BusinessPromotionController::class);
    Route::post('business-promotions/{businessPromotion}/activate', [App\Http\Controllers\BusinessPromotionController::class, 'activate'])->name('business-promotions.activate');
    Route::post('business-promotions/{businessPromotion}/pause', [App\Http\Controllers\BusinessPromotionController::class, 'pause'])->name('business-promotions.pause');
    Route::get('business-promotions/{businessPromotion}/analytics', [App\Http\Controllers\BusinessPromotionController::class, 'analytics'])->name('business-promotions.analytics');
    Route::post('business-promotions/{businessPromotion}/update-metrics', [App\Http\Controllers\BusinessPromotionController::class, 'updateMetrics'])->name('business-promotions.update-metrics');
    
    // Campaign Routes
    Route::resource('campaigns', App\Http\Controllers\CampaignController::class);
    Route::post('campaigns/{campaign}/activate', [App\Http\Controllers\CampaignController::class, 'activate'])->name('campaigns.activate');
    Route::post('campaigns/{campaign}/pause', [App\Http\Controllers\CampaignController::class, 'pause'])->name('campaigns.pause');
    Route::get('campaigns/{campaign}/analytics', [App\Http\Controllers\CampaignController::class, 'analytics'])->name('campaigns.analytics');
    Route::post('campaigns/{campaign}/bulk-emails', [App\Http\Controllers\CampaignController::class, 'sendBulkEmails'])->name('campaigns.bulk-emails');
    
    // Public Campaign Routes
    Route::get('campaign/{shareLink}', [App\Http\Controllers\CampaignController::class, 'landing'])->name('campaigns.landing');
    Route::post('campaign/{shareLink}/lead', [App\Http\Controllers\CampaignController::class, 'storeLead'])->name('campaigns.store-lead');
    Route::get('campaign/{shareLink}/thank-you', [App\Http\Controllers\CampaignController::class, 'thankYou'])->name('campaigns.thank-you');
    Route::get('campaign/{shareLink}/track', [App\Http\Controllers\CampaignController::class, 'track'])->name('campaigns.track');
    
    // Analytics Routes
    Route::get('analytics/dashboard', [App\Http\Controllers\ClientTrackingController::class, 'dashboard'])->name('analytics.dashboard');
    Route::get('analytics/page/{landingPage}', [App\Http\Controllers\ClientTrackingController::class, 'pageAnalytics'])->name('analytics.page');
    Route::get('analytics/export', [App\Http\Controllers\ClientTrackingController::class, 'exportData'])->name('analytics.export');
    Route::get('analytics/real-time', [App\Http\Controllers\ClientTrackingController::class, 'realTimeStats'])->name('analytics.real-time');
});

// Test login route (remove in production)
Route::get('/test-login', function () {
    $user = App\Models\User::first();
    if ($user) {
        Auth::login($user);
        return redirect('/landing-pages/maison/builder');
    }
    return 'No users found';
});

// Debug Routes
Route::get('/debug/landing-pages', [App\Http\Controllers\DashboardController::class, 'landingPagesDebug'])->name('debug.landing-pages');
Route::get('/debug/web-builder', [App\Http\Controllers\DashboardController::class, 'webBuilderDebug'])->name('debug.web-builder');
Route::get('/debug/route-test/{landingPage}', function(App\Models\LandingPage $landingPage) {
    return view('debug.route-test', compact('landingPage'));
})->name('debug.route-test');

// Simple Web Builder Routes
Route::get('/landing-pages/{landingPage}/simple-builder', [App\Http\Controllers\SimpleWebBuilderController::class, 'edit'])->name('simple.builder.edit');
Route::post('/landing-pages/{landingPage}/simple-builder', [App\Http\Controllers\SimpleWebBuilderController::class, 'save'])->name('simple.builder.save');
Route::get('/landing-pages/{landingPage}/simple-preview', [App\Http\Controllers\SimpleWebBuilderController::class, 'preview'])->name('simple.builder.preview');

// Drag & Drop Web Builder Routes 
// Using full namespaces to avoid any issues with class resolution
Route::get('/tools/drag-drop-builder', ['uses' => 'App\Http\Controllers\WebBuilderController@index'])->name('tools.drag-drop-builder');
Route::get('/landing-pages/{landingPage}/drag-drop-builder', ['uses' => 'App\Http\Controllers\WebBuilderController@edit'])->name('landing-pages.drag-drop-builder');
Route::get('/landing-pages/{landingPage}/builder/edit', ['uses' => 'App\Http\Controllers\WebBuilderController@edit'])->name('landing-pages.builder.edit');
// Alternative save endpoint to bypass potential routing issues
Route::post('/save-landing-page/{id}', [App\Http\Controllers\LandingPageSaveController::class, 'save'])->name('landing-pages.save-alternative');

// Public Landing Page Routes (no auth required)
Route::get('lp/{slug}', [App\Http\Controllers\LandingPageController::class, 'publicView'])->name('landing-page.show');
Route::post('lp/{slug}/submit', [App\Http\Controllers\LandingPageController::class, 'submitForm'])->name('landing-page.submit');

// Tracking API (no auth required)
Route::post('api/track', [App\Http\Controllers\ClientTrackingController::class, 'track'])->name('api.track');

// Image ALT Text Checker API routes (public for demo mode)
Route::post('/api/image-alt/analyze', [App\Http\Controllers\ImageAltController::class, 'analyze']);

// Route testing
Route::get('/test-routes', [App\Http\Controllers\RouteTestController::class, 'testRoutes'])->name('test.routes');

// CSRF Testing Routes
Route::get('/csrf-test', [App\Http\Controllers\CsrfTestController::class, 'showTest'])->name('csrf.test');
Route::post('/test-csrf', [App\Http\Controllers\CsrfTestController::class, 'processTest']);

// Debug Routes
Route::get('/debug/routes', [App\Http\Controllers\DebugController::class, 'checkRoutes'])->name('debug.routes');
Route::post('/debug/test-save/{id?}', [App\Http\Controllers\DebugController::class, 'testSave'])->name('debug.test-save');

// Landing Page Debug Routes
Route::post('/debug/create-landing-page', [App\Http\Controllers\LandingPageDebugController::class, 'debugCreate'])->name('debug.landing-page.create');
