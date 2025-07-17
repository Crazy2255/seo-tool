<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadMagnet;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Mail\LeadMagnetDownload;

class LeadController extends Controller
{
    /**
     * Show the lead magnet landing page.
     */
    public function landing($slug)
    {
        $leadMagnet = LeadMagnet::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Increment page views
        $leadMagnet->incrementViews();

        return view('lead-magnets.landing', compact('leadMagnet'));
    }

    /**
     * Store a new lead and send download link.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'lead_magnet_id' => 'required|exists:lead_magnets,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $leadMagnet = LeadMagnet::findOrFail($request->lead_magnet_id);

        // Check if email already exists for this lead magnet
        $existingLead = Lead::where('email', $request->email)
            ->where('lead_magnet_id', $leadMagnet->id)
            ->first();

        if ($existingLead) {
            return response()->json([
                'success' => false,
                'message' => 'You have already signed up for this download. Check your email for the download link.'
            ], 422);
        }

        // Create new lead
        $lead = Lead::create([
            'name' => $request->name,
            'email' => $request->email,
            'lead_magnet_id' => $leadMagnet->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referrer' => $request->header('referer'),
        ]);

        // Generate download token
        $downloadToken = $this->generateDownloadToken($lead);
        $downloadUrl = route('lead.download', ['slug' => $leadMagnet->slug, 'token' => $downloadToken]);

        // Send email with download link
        try {
            Mail::to($lead->email)->send(new LeadMagnetDownload($lead, $leadMagnet, $downloadUrl));
            $lead->markEmailAsSent();
        } catch (\Exception $e) {
            \Log::error('Failed to send lead magnet email: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Success! Check your email for the download link.',
            'download_url' => $downloadUrl,
            'thank_you_url' => route('lead.thank-you', $leadMagnet->slug)
        ]);
    }

    /**
     * Handle form submission from landing page.
     */
    public function submitForm(Request $request, $slug): JsonResponse
    {
        $leadMagnet = LeadMagnet::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if email already exists for this lead magnet
        $existingLead = Lead::where('email', $request->email)
            ->where('lead_magnet_id', $leadMagnet->id)
            ->first();

        if ($existingLead) {
            // Resend the download link to existing users
            $downloadToken = $this->generateDownloadToken($existingLead);
            $downloadUrl = route('lead.download', ['slug' => $leadMagnet->slug, 'token' => $downloadToken]);

            try {
                Mail::to($existingLead->email)->send(new LeadMagnetDownload($existingLead, $leadMagnet, $downloadUrl));
                $existingLead->markEmailAsSent();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Download link has been resent to your email address!'
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to resend lead magnet email: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'There was an error sending the email. Please try again or contact support.'
                ], 500);
            }
        }

        // Create new lead
        $lead = Lead::create([
            'name' => $request->name,
            'email' => $request->email,
            'lead_magnet_id' => $leadMagnet->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referrer' => $request->header('referer'),
        ]);

        // Generate download token
        $downloadToken = $this->generateDownloadToken($lead);
        $downloadUrl = route('lead.download', ['slug' => $leadMagnet->slug, 'token' => $downloadToken]);

        // Send email with download link
        try {
            Mail::to($lead->email)->send(new LeadMagnetDownload($lead, $leadMagnet, $downloadUrl));
            $lead->markEmailAsSent();
        } catch (\Exception $e) {
            \Log::error('Failed to send lead magnet email: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Success! Check your email for the download link.',
            'redirect_url' => route('lead.thank-you', $leadMagnet->slug)
        ]);
    }

    /**
     * Handle file download.
     */
    public function download($slug, $token)
    {
        $leadMagnet = LeadMagnet::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Find the lead by token (we'll encode lead ID in token)
        $leadId = $this->getLeadIdFromToken($token);
        $lead = Lead::where('id', $leadId)
            ->where('lead_magnet_id', $leadMagnet->id)
            ->firstOrFail();

        // Verify download token
        if (!$this->verifyDownloadToken($lead, $token)) {
            abort(403, 'Invalid download token');
        }

        // Check if file exists
        if (!Storage::disk('public')->exists($leadMagnet->file_path)) {
            abort(404, 'File not found');
        }

        // Mark as downloaded
        $lead->markAsDownloaded();
        $leadMagnet->incrementDownloads();

        // Return file download
        return Storage::disk('public')->download(
            $leadMagnet->file_path,
            $leadMagnet->file_name
        );
    }

    /**
     * Show thank you page.
     */
    public function thankYou($slug)
    {
        $leadMagnet = LeadMagnet::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return view('lead-magnets.thank-you', compact('leadMagnet'));
    }

    /**
     * Generate download token for lead.
     */
    private function generateDownloadToken(Lead $lead): string
    {
        $data = $lead->id . '|' . $lead->email . '|' . $lead->created_at->timestamp;
        return base64_encode($data . '|' . hash('sha256', $data . config('app.key')));
    }

    /**
     * Verify download token.
     */
    private function verifyDownloadToken(Lead $lead, string $token): bool
    {
        try {
            $decoded = base64_decode($token);
            $parts = explode('|', $decoded);
            
            if (count($parts) !== 4) {
                return false;
            }
            
            $leadId = $parts[0];
            $email = $parts[1];
            $timestamp = $parts[2];
            $hash = $parts[3];
            
            $data = $leadId . '|' . $email . '|' . $timestamp;
            $expectedHash = hash('sha256', $data . config('app.key'));
            
            return $leadId == $lead->id && 
                   $email === $lead->email && 
                   $timestamp == $lead->created_at->timestamp && 
                   hash_equals($expectedHash, $hash);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Extract lead ID from token.
     */
    private function getLeadIdFromToken(string $token): ?int
    {
        try {
            $decoded = base64_decode($token);
            $parts = explode('|', $decoded);
            return isset($parts[0]) ? (int)$parts[0] : null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
