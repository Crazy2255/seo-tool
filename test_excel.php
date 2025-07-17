<?php

use App\Models\LeadMagnet;
use App\Mail\LeadMagnetInvite;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

// Test the Excel import functionality
$testFile = public_path('sample-emails.csv');

if (file_exists($testFile)) {
    echo "Testing Excel file processing...\n\n";
    
    // Read the CSV file
    $data = Excel::toCollection(null, $testFile)->first();
    
    echo "Total rows: " . $data->count() . "\n";
    
    $emails = collect();
    
    // Process each row to extract emails
    foreach ($data as $index => $row) {
        // Skip header row
        if ($index === 0) {
            echo "Header row: " . implode(", ", $row->toArray()) . "\n";
            continue;
        }
        
        $email = null;
        
        // Try to find email in any column
        foreach ($row as $cell) {
            $cellValue = trim($cell);
            if (filter_var($cellValue, FILTER_VALIDATE_EMAIL)) {
                $email = strtolower($cellValue);
                break;
            }
        }
        
        if ($email) {
            $emails->push($email);
            echo "Found email: $email\n";
        }
    }
    
    echo "\nTotal valid emails found: " . $emails->count() . "\n";
    echo "Emails: " . $emails->implode(', ') . "\n";
} else {
    echo "Test file not found: $testFile\n";
}
