<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SeoAuditController;
use App\Http\Controllers\KeywordController;
use App\Http\Controllers\BacklinkController;
use App\Http\Controllers\MetaAnalyzerController;
use App\Http\Controllers\SerpPreviewController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Test API endpoint
Route::get('/test', function() {
    return response()->json(['message' => 'API is working!', 'time' => now()]);
});

// Test audit endpoint
Route::get('/test-audit', function() {
    try {
        $service = new \App\Services\SeoAuditService();
        $result = $service->performAudit('https://example.com');
        return response()->json(['success' => true, 'data' => $result]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
});

// Public SEO Tools (allow demo access)
Route::prefix('audit')->group(function () {
    // Individual SEO Tools - allow demo access
    Route::post('/meta-analyzer', [SeoAuditController::class, 'metaAnalyzer']);
    Route::post('/page-speed', [SeoAuditController::class, 'pageSpeedCheck']);
    Route::post('/broken-links', [SeoAuditController::class, 'brokenLinkCheck']);
});

// Authenticated Site Audit API - moved to web routes for proper session handling
// Route::middleware('auth:web')->group(function () {
//     Route::post('/site-audit', [App\Http\Controllers\SiteAuditController::class, 'runAudit']);
//     Route::delete('/site-audit/{id}', [App\Http\Controllers\SiteAuditController::class, 'destroy']);
// });

// Public Keyword Tools (allow demo access) 
Route::prefix('keywords')->group(function () {
    Route::get('/', [KeywordController::class, 'index']);
    Route::post('/', [KeywordController::class, 'store']); // Allow both demo and authenticated users
    Route::post('/suggestions', [KeywordController::class, 'suggestions']);
    Route::post('/check-ranking', [KeywordController::class, 'checkRanking']);
});

// Public Backlink Tools (allow demo access)
Route::prefix('backlinks')->group(function () {
    Route::post('/check', [BacklinkController::class, 'checkBacklinks']);
    Route::post('/analyze', [BacklinkController::class, 'analyze']);
    Route::get('/export', [BacklinkController::class, 'export']);
    Route::post('/competitors', [BacklinkController::class, 'findCompetitors']);
});

// Protected API routes - require web authentication (session-based)
Route::middleware('auth:web')->group(function () {
    
    // SEO Audit API - Protected
    Route::prefix('audit')->group(function () {
        Route::post('/save', [SeoAuditController::class, 'store']);
        Route::get('/user-audits', [SeoAuditController::class, 'getUserAudits']);
        Route::get('/{id}', [SeoAuditController::class, 'show']);
        Route::delete('/{id}', [SeoAuditController::class, 'destroy']);
        Route::get('/{id}/report', [SeoAuditController::class, 'generateReport']);
    });

    // Keyword Tracking API - Protected (authenticated actions only)
    Route::prefix('keywords')->group(function () {
        Route::put('/{id}', [KeywordController::class, 'update']);
        Route::delete('/{id}', [KeywordController::class, 'destroy']);
        Route::post('/bulk-update', [KeywordController::class, 'bulkUpdate']);
        Route::get('/analytics', [KeywordController::class, 'analytics']);
    });

    // Backlink Analysis API - Protected
    Route::prefix('backlinks')->group(function () {
        Route::get('/', [BacklinkController::class, 'index']);
        Route::get('/domain/{domain}', [BacklinkController::class, 'getDomainBacklinks']);
    });
    
    // Meta Analyzer - Protected Routes
    Route::prefix('meta-analyzer')->group(function () {
        Route::post('/', [MetaAnalyzerController::class, 'analyzeUrl']);
        Route::post('/quick', [MetaAnalyzerController::class, 'quickAnalyze']);
        Route::get('/history', [MetaAnalyzerController::class, 'getAuditHistory']);
        Route::get('/audit/{id}', [MetaAnalyzerController::class, 'getAudit']);
        Route::delete('/audit/{id}', [MetaAnalyzerController::class, 'deleteAudit']);
        Route::get('/audit/{id}/pdf', [MetaAnalyzerController::class, 'generatePdfReport']);
    });

    // SERP Preview routes
    Route::prefix('serp-preview')->group(function () {
        Route::post('/save', [SerpPreviewController::class, 'save']);
        Route::get('/previews', [SerpPreviewController::class, 'getUserPreviews']);
        Route::delete('/delete', [SerpPreviewController::class, 'delete']);
        Route::post('/export-pdf', [SerpPreviewController::class, 'exportPdf']);
    });

    // User profile routes
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
