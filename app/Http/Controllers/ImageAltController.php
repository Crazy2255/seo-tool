<?php

namespace App\Http\Controllers;

use App\Models\ImageAltAudit;
use App\Services\ImageAltService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class ImageAltController extends Controller
{
    protected $imageAltService;

    public function __construct(ImageAltService $imageAltService)
    {
        $this->imageAltService = $imageAltService;
    }

    /**
     * Show the image alt text checker tool.
     */
    public function index()
    {
        $user = Auth::user();
        $recentAudits = collect([]);
        $totalAudits = 0;
        $avgAccessibilityScore = 0;

        if ($user) {
            $recentAudits = ImageAltAudit::forUser($user->id)
                ->orderBy('analyzed_at', 'desc')
                ->take(5)
                ->get();
                
            $totalAudits = ImageAltAudit::forUser($user->id)->count();
            $avgAccessibilityScore = ImageAltAudit::forUser($user->id)
                ->whereRaw('total_images > 0')
                ->get()
                ->avg('accessibility_score') ?? 0;
        }

        return view('tools.image-alt-checker', [
            'recentAudits' => $recentAudits,
            'totalAudits' => $totalAudits,
            'avgAccessibilityScore' => round($avgAccessibilityScore, 1),
            'isDemo' => !$user
        ]);
    }

    /**
     * Analyze URL for image alt text accessibility.
     */
    public function analyze(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|string|url|max:2048',
            'multi_page' => 'boolean',
            'max_pages' => 'integer|min:1|max:10',
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
        $multiPage = $request->boolean('multi_page', false);
        $maxPages = $request->input('max_pages', 5);

        // For demo mode, return mock data
        if (!$user) {
            return response()->json($this->imageAltService->generateDemoData());
        }

        try {
            $result = $this->imageAltService->analyzeImages($url, $user->id, $multiPage, $maxPages);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => $result['data'],
                    'processing_time' => $result['processing_time']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Analysis failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show user's audit history.
     */
    public function history()
    {
        $user = Auth::user();
        
        $audits = ImageAltAudit::forUser($user->id)
            ->orderBy('analyzed_at', 'desc')
            ->paginate(20);

        return view('tools.image-alt-history', [
            'audits' => $audits
        ]);
    }

    /**
     * Show detailed audit report.
     */
    public function show($id)
    {
        $audit = ImageAltAudit::findOrFail($id);
        
        // Check if user owns this audit (or allow public access for demo)
        if (Auth::check() && $audit->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to audit report.');
        }

        return view('tools.image-alt-report', [
            'audit' => $audit
        ]);
    }

    /**
     * Export audit report as PDF.
     */
    public function exportPdf($id)
    {
        $audit = ImageAltAudit::findOrFail($id);
        
        // Check if user owns this audit
        if (Auth::check() && $audit->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to audit report.');
        }

        $pdf = Pdf::loadView('reports.image-alt-pdf', [
            'audit' => $audit
        ]);

        $filename = 'image-alt-audit-' . $audit->id . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Export audit report as CSV.
     */
    public function exportCsv($id)
    {
        $audit = ImageAltAudit::findOrFail($id);
        
        // Check if user owns this audit
        if (Auth::check() && $audit->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to audit report.');
        }

        $filename = 'image-alt-audit-' . $audit->id . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($audit) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Image URL',
                'Alt Text',
                'Status',
                'Score',
                'Issues',
                'Recommendations',
                'File Name',
                'Dimensions',
                'Parent Tag',
                'Page URL'
            ]);

            // CSV data
            foreach ($audit->images_data as $image) {
                fputcsv($file, [
                    $image['src'],
                    $image['alt'] ?? '',
                    ucfirst($image['analysis']['status']),
                    $image['analysis']['score'],
                    implode('; ', $image['analysis']['issues']),
                    implode('; ', $image['analysis']['recommendations']),
                    $image['file_name'],
                    ($image['width'] && $image['height']) ? $image['width'] . 'x' . $image['height'] : 'Unknown',
                    $image['context']['parent_tag'] ?? '',
                    $image['page_url']
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Delete an audit.
     */
    public function delete($id): JsonResponse
    {
        try {
            $audit = ImageAltAudit::findOrFail($id);
            
            // Check if user owns this audit
            if ($audit->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to audit.'
                ], 403);
            }

            $audit->delete();

            return response()->json([
                'success' => true,
                'message' => 'Audit deleted successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete audit: ' . $e->getMessage()
            ], 500);
        }
    }
}
