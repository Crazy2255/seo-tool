<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DebugJsonController extends Controller
{
    public function testCleanJson(): JsonResponse
    {
        // Clear any output
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'This is a clean JSON response'
        ]);
    }
    
    public function testWithOutput(): JsonResponse
    {
        // Intentionally output something before JSON
        echo " ";
        
        return response()->json([
            'success' => true,
            'message' => 'This has output before JSON'
        ]);
    }
    
    public function testRawJson()
    {
        // Disable all output
        ini_set('display_errors', '0');
        error_reporting(0);
        
        // Clear any output
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        header('Content-Type: application/json', true);
        echo json_encode([
            'success' => true,
            'message' => 'This is raw JSON output',
            'method' => 'direct echo'
        ]);
        exit;
    }
    
    public function testBulkInviteSimulation(Request $request)
    {
        // Simulate the bulk invite process with the same exact approach
        ini_set('display_errors', '0');
        error_reporting(0);
        
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // Simulate file validation (but don't actually require a file for this test)
        if ($request->hasFile('excel_file')) {
            $file = $request->file('excel_file');
            $message = "File uploaded: " . $file->getClientOriginalName();
        } else {
            $message = "No file uploaded - this is just a simulation";
        }
        
        header('Content-Type: application/json', true);
        echo json_encode([
            'success' => true,
            'message' => $message,
            'simulation' => true,
            'data' => [
                'total_emails_found' => 3,
                'emails_sent' => 3,
                'failed_emails' => 0,
                'duplicate_emails' => 0,
                'invalid_rows' => 0
            ]
        ]);
        exit;
    }
}
