<?php

// Simple CSV test
$file = 'public/sample-emails.csv';
echo "Testing CSV processing...\n";

if (file_exists($file)) {
    $data = [];
    $handle = fopen($file, 'r');
    
    if ($handle !== false) {
        while (($row = fgetcsv($handle)) !== false) {
            $data[] = $row;
        }
        fclose($handle);
    }
    
    echo "Total rows found: " . count($data) . "\n";
    
    foreach ($data as $index => $row) {
        echo "Row $index: " . implode(', ', $row) . "\n";
        
        // Look for email in each column
        foreach ($row as $cell) {
            if (filter_var(trim($cell), FILTER_VALIDATE_EMAIL)) {
                echo "  -> Found email: " . trim($cell) . "\n";
            }
        }
    }
} else {
    echo "File not found: $file\n";
}
