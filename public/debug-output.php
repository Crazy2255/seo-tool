<?php
// Clean start - debug page to check for extra output
ob_start();

// Capture any existing output
$existingOutput = ob_get_clean();

if (!empty($existingOutput)) {
    echo "Found extra output: " . bin2hex($existingOutput) . "<br>";
    echo "Characters: " . strlen($existingOutput) . "<br>";
    echo "Raw: " . htmlspecialchars($existingOutput) . "<br>";
} else {
    echo "No extra output detected.<br>";
}

echo "This is a clean test page.<br>";
echo "If you see any characters above this line, there's an output issue.<br>";
?>
