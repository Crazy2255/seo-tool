<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class TestController extends Controller
{
    /**
     * Test JSON response endpoint
     */
    public function testJson(): JsonResponse
    {
        // Clear any output buffer
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Test JSON response working correctly',
            'timestamp' => now()->toISOString()
        ]);
    }
    
    /**
     * Test bulk invite with sample data
     */
    public function testBulkInvite(Request $request): JsonResponse
    {
        // Clear any output buffer
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // If this is a POST request, test validation
        if ($request->isMethod('POST')) {
            $validator = Validator::make($request->all(), [
                'excel_file' => 'required|file|mimes:csv,txt,xlsx,xls|max:5120',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Test validation failed',
                    'errors' => $validator->errors()->all(),
                    'request_data' => [
                        'has_file' => $request->hasFile('excel_file'),
                        'file_info' => $request->hasFile('excel_file') ? [
                            'name' => $request->file('excel_file')->getClientOriginalName(),
                            'size' => $request->file('excel_file')->getSize(),
                            'mime' => $request->file('excel_file')->getMimeType(),
                            'extension' => $request->file('excel_file')->getClientOriginalExtension(),
                        ] : null
                    ]
                ], 422);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Test validation passed!',
                'file_info' => [
                    'name' => $request->file('excel_file')->getClientOriginalName(),
                    'size' => $request->file('excel_file')->getSize(),
                    'mime' => $request->file('excel_file')->getMimeType(),
                    'extension' => $request->file('excel_file')->getClientOriginalExtension(),
                ]
            ]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Test bulk invite response working correctly',
            'data' => [
                'total_emails_found' => 3,
                'emails_sent' => 3,
                'failed_emails' => 0,
                'duplicate_emails' => 0,
                'invalid_rows' => 0,
                'details' => [
                    'duplicates' => [],
                    'failed' => [],
                    'invalid' => []
                ]
            ]
        ]);
    }
    
    /**
     * Test bulk invite with actual lead magnet (no auth required for testing)
     */
    public function testActualBulkInvite(Request $request): JsonResponse
    {
        // Clear any output buffer
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // Get first available lead magnet for testing
        $leadMagnet = \App\Models\LeadMagnet::first();
        
        if (!$leadMagnet) {
            return response()->json([
                'success' => false,
                'message' => 'No lead magnets found. Create one first.',
            ]);
        }
        
        // Simulate the bulk invite logic
        if ($request->hasFile('excel_file')) {
            try {
                $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                    'excel_file' => 'required|file|mimes:csv,txt,xlsx,xls|max:5120',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'File validation failed',
                        'errors' => $validator->errors()->all()
                    ], 422);
                }

                $file = $request->file('excel_file');
                
                // Simple CSV processing for testing
                $emails = [];
                if (($handle = fopen($file->getPathname(), 'r')) !== FALSE) {
                    $headers = fgetcsv($handle);
                    while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                        foreach ($data as $cell) {
                            if (filter_var(trim($cell), FILTER_VALIDATE_EMAIL)) {
                                $emails[] = trim($cell);
                                break;
                            }
                        }
                    }
                    fclose($handle);
                }

                $savedLeads = [];
                $sentCount = 0;
                
                foreach ($emails as $email) {
                    // Check if lead already exists
                    $existingLead = \App\Models\Lead::where('email', $email)
                        ->where('lead_magnet_id', $leadMagnet->id)
                        ->exists();
                    
                    if (!$existingLead) {
                        // Create lead
                        $lead = \App\Models\Lead::create([
                            'email' => $email,
                            'lead_magnet_id' => $leadMagnet->id,
                            'source' => 'bulk_invite_test',
                            'name' => null,
                            'downloaded_at' => null
                        ]);
                        $savedLeads[] = $email;
                        
                        // Send email (this will use whatever mail driver is configured)
                        try {
                            \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\LeadMagnetInvite($leadMagnet, $email));
                            $sentCount++;
                        } catch (\Exception $e) {
                            \Log::error("Failed to send test invite to {$email}: " . $e->getMessage());
                        }
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => "Test bulk invite completed! {$sentCount} emails processed.",
                    'data' => [
                        'lead_magnet' => $leadMagnet->title,
                        'emails_found' => count($emails),
                        'leads_created' => count($savedLeads),
                        'emails_sent' => $sentCount,
                        'mail_driver' => config('mail.default'),
                        'saved_leads' => $savedLeads
                    ]
                ]);
                
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error processing file: ' . $e->getMessage()
                ], 500);
            }
        }
        
        return response()->json([
            'success' => false,
            'message' => 'No file uploaded',
            'lead_magnet_available' => $leadMagnet->title ?? 'None'
        ]);
    }
    
    /**
     * Simple email test endpoint
     */
    public function testEmailSending()
    {
        try {
            // Get first available lead magnet
            $leadMagnet = \App\Models\LeadMagnet::first();
            
            if (!$leadMagnet) {
                return response()->json([
                    'success' => false,
                    'message' => 'No lead magnets found. Create one first.'
                ]);
            }
            
            $testEmail = 'rramees60@gmail.com';
            
            // Try to send a test email
            \Illuminate\Support\Facades\Mail::to($testEmail)->send(new \App\Mail\LeadMagnetInvite($leadMagnet, $testEmail));
            
            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully!',
                'data' => [
                    'test_email' => $testEmail,
                    'lead_magnet' => $leadMagnet->title,
                    'mail_driver' => config('mail.default'),
                    'mail_host' => config('mail.mailers.smtp.host'),
                    'note' => 'Check your Mailtrap inbox to see the email.'
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test email: ' . $e->getMessage()
            ]);
        }
    }
}
