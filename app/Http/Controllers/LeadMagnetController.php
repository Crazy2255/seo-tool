<?php

namespace App\Http\Controllers;

use App\Models\LeadMagnet;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\LeadMagnetInvite;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class LeadMagnetController extends Controller
{
    /**
     * Display the lead magnet builder tool.
     */
    public function index()
    {
        $user = Auth::user();
        
        $leadMagnets = collect([]);
        $totalMagnets = 0;
        $totalLeads = 0;
        $totalDownloads = 0;
        $avgConversionRate = 0;

        if ($user) {
            $leadMagnets = LeadMagnet::forUser($user->id)
                ->with(['leads'])
                ->orderBy('created_at', 'desc')
                ->get();

            $totalMagnets = $leadMagnets->count();
            $totalLeads = $leadMagnets->sum(function($magnet) {
                return $magnet->leads->count();
            });
            $totalDownloads = $leadMagnets->sum('download_count');
            $avgConversionRate = $leadMagnets->avg('conversion_rate');
        }

        return view('tools.lead-magnet-builder', [
            'leadMagnets' => $leadMagnets,
            'totalMagnets' => $totalMagnets,
            'totalLeads' => $totalLeads,
            'totalDownloads' => $totalDownloads,
            'avgConversionRate' => round($avgConversionRate, 1),
            'isDemo' => !$user
        ]);
    }

    /**
     * Store a newly created lead magnet.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,zip|max:10240', // 10MB max
            'form_title' => 'nullable|string|max:255',
            'form_description' => 'nullable|string',
            'form_button_text' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $file = $request->file('file');
        
        // Generate unique filename
        $filename = time() . '_' . Str::slug($request->title) . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('lead-magnets', $filename, 'public');

        $leadMagnet = LeadMagnet::create([
            'title' => $request->title,
            'description' => $request->description,
            'slug' => LeadMagnet::generateSlug($request->title),
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'form_title' => $request->form_title ?: 'Get Your Free Download',
            'form_description' => $request->form_description,
            'form_button_text' => $request->form_button_text ?: 'Download Now',
            'user_id' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lead magnet created successfully!',
            'data' => [
                'id' => $leadMagnet->id,
                'title' => $leadMagnet->title,
                'landing_url' => $leadMagnet->landing_url,
                'formatted_file_size' => $leadMagnet->formatted_file_size,
                'is_active' => $leadMagnet->is_active
            ]
        ]);
    }

    /**
     * Display the specified lead magnet analytics.
     */
    public function show(LeadMagnet $leadMagnet)
    {
        if ($leadMagnet->user_id !== Auth::id()) {
            abort(403);
        }

        $leadMagnet->load('leads');
        
        $analytics = [
            'total_views' => $leadMagnet->view_count,
            'total_leads' => $leadMagnet->leads->count(),
            'total_downloads' => $leadMagnet->download_count,
            'conversion_rate' => $leadMagnet->conversion_rate,
            'recent_leads' => $leadMagnet->leads()->orderBy('created_at', 'desc')->take(10)->get(),
        ];

        return view('tools.lead-magnet-analytics', compact('leadMagnet', 'analytics'));
    }

    /**
     * Update the specified lead magnet.
     */
    public function update(Request $request, LeadMagnet $leadMagnet): JsonResponse
    {
        if ($leadMagnet->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'form_title' => 'nullable|string|max:255',
            'form_description' => 'nullable|string',
            'form_button_text' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = [
            'title' => $request->title,
            'description' => $request->description,
            'form_title' => $request->form_title ?: 'Get Your Free Download',
            'form_description' => $request->form_description,
            'form_button_text' => $request->form_button_text ?: 'Download Now',
            'is_active' => $request->boolean('is_active', true),
        ];

        // Update slug if title changed
        if ($leadMagnet->title !== $request->title) {
            $updateData['slug'] = LeadMagnet::generateSlug($request->title);
        }

        $leadMagnet->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Lead magnet updated successfully!',
            'data' => $leadMagnet->fresh()
        ]);
    }

    /**
     * Remove the specified lead magnet.
     */
    public function destroy(LeadMagnet $leadMagnet): JsonResponse
    {
        if ($leadMagnet->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Delete the file
        if ($leadMagnet->file_path && Storage::disk('public')->exists($leadMagnet->file_path)) {
            Storage::disk('public')->delete($leadMagnet->file_path);
        }

        $leadMagnet->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lead magnet deleted successfully!'
        ]);
    }

    /**
     * Export leads as CSV.
     */
    public function exportLeads(LeadMagnet $leadMagnet)
    {
        if ($leadMagnet->user_id !== Auth::id()) {
            abort(403);
        }

        $leads = $leadMagnet->leads()->orderBy('created_at', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . Str::slug($leadMagnet->title) . '-leads.csv"',
        ];

        $callback = function() use ($leads) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, ['Name', 'Email', 'Signup Date', 'Download Date', 'IP Address', 'Referrer']);
            
            // Add data rows
            foreach ($leads as $lead) {
                fputcsv($file, [
                    $lead->name,
                    $lead->email,
                    $lead->created_at->format('Y-m-d H:i:s'),
                    $lead->downloaded_at ? $lead->downloaded_at->format('Y-m-d H:i:s') : 'Not downloaded',
                    $lead->ip_address,
                    $lead->referrer,
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export all leads for the authenticated user.
     */
    public function exportAllLeads()
    {
        $user = Auth::user();
        
        $leads = Lead::whereHas('leadMagnet', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with('leadMagnet')->orderBy('created_at', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="all-leads-' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($leads) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, ['Name', 'Email', 'Lead Magnet', 'Signup Date', 'Download Date', 'IP Address', 'Referrer']);
            
            // Add data rows
            foreach ($leads as $lead) {
                fputcsv($file, [
                    $lead->name,
                    $lead->email,
                    $lead->leadMagnet->title,
                    $lead->created_at->format('Y-m-d H:i:s'),
                    $lead->downloaded_at ? $lead->downloaded_at->format('Y-m-d H:i:s') : 'Not downloaded',
                    $lead->ip_address,
                    $lead->referrer,
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Send bulk invites from Excel file.
     */
    public function sendBulkInvites(Request $request, LeadMagnet $leadMagnet): JsonResponse
    {
        // Aggressive output cleaning - disable all output immediately
        ini_set('display_errors', '0');
        error_reporting(0);
        
        // Clean all existing output buffers
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        try {
            
            if ($leadMagnet->user_id !== Auth::id()) {
                header('Content-Type: application/json', true);
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit;
            }

            $validator = Validator::make($request->all(), [
                'excel_file' => 'required|file|mimes:csv,txt,xlsx,xls|max:5120', // 5MB max, support CSV and Excel
            ]);

            if ($validator->fails()) {
                header('Content-Type: application/json', true);
                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'message' => 'File validation failed. Please upload a CSV or Excel file (max 5MB).',
                    'errors' => $validator->errors()->all()
                ]);
                exit;
            }

            $file = $request->file('excel_file');
            
            // Process the CSV file
            $data = $this->processCsvFile($file);
            
            if (empty($data)) {
                header('Content-Type: application/json', true);
                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'message' => 'The uploaded file is empty or invalid.'
                ]);
                exit;
            }

            $emails = collect();
            $invalidEmails = collect();
            $duplicateEmails = collect();
            
            // Process each row to extract emails
            foreach ($data as $index => $row) {
                // Skip header row
                if ($index === 0) {
                    continue;
                }
                
                $email = null;
                
                // Try to find email in any column
                if (is_array($row)) {
                    foreach ($row as $cell) {
                        $cellValue = trim((string)$cell);
                        if (filter_var($cellValue, FILTER_VALIDATE_EMAIL)) {
                            $email = strtolower($cellValue);
                            break;
                        }
                    }
                }
                
                if ($email) {
                    // Check if email already exists in our leads for this magnet
                    $existingLead = Lead::where('email', $email)
                        ->where('lead_magnet_id', $leadMagnet->id)
                        ->exists();
                    
                    if ($existingLead) {
                        $duplicateEmails->push($email);
                    } elseif (!$emails->contains($email)) {
                        $emails->push($email);
                    }
                } else {
                    $invalidEmails->push("Row " . ($index + 1));
                }
            }

            if ($emails->isEmpty()) {
                header('Content-Type: application/json', true);
                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'message' => 'No valid emails found in the uploaded file.',
                    'details' => [
                        'invalid_emails' => $invalidEmails->take(5)->toArray(),
                        'total_invalid' => $invalidEmails->count()
                    ]
                ]);
                exit;
            }

            $sentCount = 0;
            $failedEmails = collect();
            $savedLeads = collect();

            // Send emails and save leads
            foreach ($emails as $email) {
                try {
                    // Create lead record first
                    $lead = Lead::create([
                        'email' => $email,
                        'lead_magnet_id' => $leadMagnet->id,
                        'source' => 'bulk_invite',
                        'name' => null, // Will be filled when they actually download
                        'downloaded_at' => null
                    ]);
                    $savedLeads->push($email);
                    
                    // Send invite email
                    Mail::to($email)->send(new LeadMagnetInvite($leadMagnet, $email));
                    $sentCount++;
                    
                } catch (\Exception $e) {
                    $failedEmails->push($email);
                    \Log::error("Failed to send invite email to {$email}: " . $e->getMessage());
                }
            }

            // Clear output buffer before returning JSON
            while (ob_get_level()) {
                ob_end_clean();
            }
            
            header('Content-Type: application/json', true);
            echo json_encode([
                'success' => true,
                'message' => "Bulk invites processed successfully! " . 
                    ($sentCount > 0 ? "{$sentCount} emails sent." : "") . 
                    " Check your mail logs or configure SMTP for actual delivery.",
                'data' => [
                    'total_emails_found' => $emails->count(),
                    'leads_created' => $savedLeads->count(),
                    'emails_sent' => $sentCount,
                    'failed_emails' => $failedEmails->count(),
                    'duplicate_emails' => $duplicateEmails->count(),
                    'invalid_rows' => $invalidEmails->count(),
                    'mail_driver' => config('mail.default'),
                    'note' => config('mail.default') === 'log' ? 
                        'Emails are being logged to storage/logs/laravel.log. Configure SMTP for actual email delivery.' : 
                        'Emails are being sent via ' . config('mail.default'),
                    'details' => [
                        'duplicates' => $duplicateEmails->take(5)->toArray(),
                        'failed' => $failedEmails->take(5)->toArray(),
                        'invalid' => $invalidEmails->take(5)->toArray(),
                        'saved_leads' => $savedLeads->take(5)->toArray()
                    ]
                ]
            ]);
            exit;

        } catch (\Exception $e) {
            // Clear any output buffer
            while (ob_get_level()) {
                ob_end_clean();
            }
            
            \Log::error("Error processing bulk invite: " . $e->getMessage());
            
            header('Content-Type: application/json', true);
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error processing the uploaded file. Please ensure it\'s a valid CSV file with email addresses.'
            ]);
            exit;
        }
    }

    /**
     * Process CSV or Excel file and return data array
     */
    private function processCsvFile($file)
    {
        $data = [];
        
        try {
            $extension = strtolower($file->getClientOriginalExtension());
            
            if (in_array($extension, ['xlsx', 'xls'])) {
                // Handle Excel files
                $collection = Excel::toCollection(null, $file);
                
                if ($collection->isNotEmpty()) {
                    $rows = $collection->first();
                    foreach ($rows as $row) {
                        // Skip completely empty rows
                        $rowArray = $row->toArray();
                        if (!empty(array_filter($rowArray, function($cell) {
                            return !empty(trim($cell));
                        }))) {
                            $data[] = $rowArray;
                        }
                    }
                }
            } else {
                // Handle CSV files
                $handle = fopen($file->getRealPath(), 'r');
                
                if ($handle === false) {
                    throw new \Exception('Cannot open uploaded file');
                }
                
                // Set locale for proper character handling
                setlocale(LC_ALL, 'en_US.UTF-8');
                
                while (($row = fgetcsv($handle, 1000, ",")) !== false) {
                    // Skip completely empty rows
                    if (!empty(array_filter($row, function($cell) {
                        return !empty(trim($cell));
                    }))) {
                        $data[] = $row;
                    }
                }
                
                fclose($handle);
            }
            
        } catch (\Exception $e) {
            \Log::error("Error processing file: " . $e->getMessage());
            throw $e;
        }
        
        return $data;
    }
}
