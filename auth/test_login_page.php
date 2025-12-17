<?php
// Simple test to check if PHP is working and what errors might exist
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "PHP is working!<br>";
echo "Current directory: " . __DIR__ . "<br>";

// Test database connection
require_once '../config/database.php';
echo "Database config loaded<br>";

try {
    $conn = getDBConnection();
    echo "Database connection successful!<br>";
    $conn->close();
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "<br>";
}

// Test if CSS files exist
$css_files = [
    '../assets/css/style.css',
    '../assets/css/auth.css'
];

foreach ($css_files as $file) {
    if (file_exists($file)) {
        echo "✓ Found: $file<br>";
    } else {
        echo "✗ Missing: $file<br>";
    }
}

// Test if logo exists
if (file_exists('../assets/images/logo1.png')) {
    echo "✓ Logo found<br>";
} else {
    echo "✗ Logo missing<br>";
}
?>
