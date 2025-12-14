<?php
// Debug script to test API access

echo "=== API Debug Info ===\n\n";

// Show the current path
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "\n";
echo "PHP_SELF: " . $_SERVER['PHP_SELF'] . "\n";
echo "BASE_PATH: " . dirname($_SERVER['SCRIPT_NAME']) . "\n\n";

// Try loading the actual API
echo "=== Attempting to load REST API ===\n";
try {
    require_once __DIR__ . '/rest/index.php';
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

?>
