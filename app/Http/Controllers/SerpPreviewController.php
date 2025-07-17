<?php

namespace App\Http\Controllers;

use App\Models\SerpPreview;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class SerpPreviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index']);
    }

    /**
     * Show SERP preview tool page
     */
    public function index()
    {
        $recentPreviews = [];
        
        if (Auth::check()) {
            $recentPreviews = SerpPreview::byUser(Auth::id())
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }

        return view('tools.serp-preview', compact('recentPreviews'));
    }

    /**
     * Save SERP preview
     */
    public function save(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'page_title' => 'required|string|max:255',
            'target_url' => 'required|url|max:2048',
            'meta_description' => 'required|string|max:500',
            'preview_name' => 'nullable|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $serpPreview = SerpPreview::create([
                'user_id' => Auth::id(),
                'page_title' => $request->page_title,
                'target_url' => $request->target_url,
                'meta_description' => $request->meta_description,
                'preview_name' => $request->preview_name ?: 'Preview ' . now()->format('M d, Y H:i')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'SERP preview saved successfully',
                'data' => $serpPreview
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving preview: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's saved previews
     */
    public function getUserPreviews(): JsonResponse
    {
        try {
            $previews = SerpPreview::byUser(Auth::id())
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $previews
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading previews: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a saved preview
     */
    public function delete(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'preview_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $preview = SerpPreview::byUser(Auth::id())
                ->where('id', $request->preview_id)
                ->firstOrFail();

            $preview->delete();

            return response()->json([
                'success' => true,
                'message' => 'Preview deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting preview: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export preview as PDF
     */
    public function exportPdf(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page_title' => 'required|string|max:255',
            'target_url' => 'required|url|max:2048',
            'meta_description' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = [
                'page_title' => $request->page_title,
                'target_url' => $request->target_url,
                'meta_description' => $request->meta_description,
                'generated_at' => now()->format('F d, Y \a\t H:i'),
                'title_length' => strlen($request->page_title),
                'description_length' => strlen($request->meta_description),
                'formatted_url' => $this->formatUrl($request->target_url)
            ];

            $pdf = Pdf::loadView('pdfs.serp-preview', $data);
            
            return $pdf->download('serp-preview-' . now()->format('Y-m-d-H-i-s') . '.pdf');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format URL for display
     */
    private function formatUrl(string $url): string
    {
        $url = preg_replace('#^https?://#', '', $url);
        $url = preg_replace('#^www\.#', '', $url);
        return strlen($url) > 50 ? substr($url, 0, 50) . '...' : $url;
    }
}
