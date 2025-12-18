<?php
/**
 * QuickBite Database Configuration
 
 */

// Database configuration 
// Manual override: 
$manual_override = null; 

// Auto-detect environment if no manual override
if ($manual_override === null) {
    $is_school_server = (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') === false) || 
                       (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] !== 'localhost');
} else {
    $is_school_server = $manual_override;
}

if ($is_school_server) {
    // ===== SCHOOL SERVER CONFIGURATION =====
    
    define('DB_HOST', 'localhost');
    define('DB_USER', 'manyok.deng');           
    define('DB_PASS', 'M@ny0k');                
    define('DB_NAME', 'webtech_2025A_manyok_deng'); 
    define('DB_PORT', 3306);                    
} else {
    // ===== LOCAL XAMPP CONFIGURATION =====
    // Default XAMPP settings 
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');                 
    define('DB_PASS', '');                      
    define('DB_NAME', 'quickbite_db');          
    define('DB_PORT', 3307);                    
}

// Create  automatic environment detection
function getDBConnection() {
    mysqli_report(MYSQLI_REPORT_OFF);

    // multiple ports for better compatibility
    $ports = [DB_PORT, 3306, 3307, 3308];
    $conn = null;
    $last_error = '';
    
    foreach ($ports as $port) {
        $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, $port);
        if (!$conn->connect_error) {
            $conn->set_charset("utf8mb4");
            return $conn;
        }
        $last_error = $conn->connect_error;
    }

    // Determine environment for better error messages
    global $is_school_server;
    $environment = $is_school_server ? 'School Server' : 'Local XAMPP';

    // If all ports failed, show environment-specific error
    $error = "<div style='font-family: Arial; padding: 20px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; margin: 20px;'>";
    $error .= "<h2 style='color: #dc3545; margin-bottom: 15px;'>🚨 Database Connection Failed!</h2>";
    $error .= "<p style='color: #6c757d; margin-bottom: 15px;'><strong>Environment:</strong> " . $environment . "</p>";
    $error .= "<p style='color: #6c757d; margin-bottom: 15px;'><strong>Error:</strong> " . $last_error . "</p>";
    $error .= "<p style='color: #6c757d; margin-bottom: 15px;'><strong>Database:</strong> " . DB_NAME . "</p>";
    $error .= "<p style='color: #6c757d; margin-bottom: 15px;'><strong>User:</strong> " . DB_USER . "</p>";
    
    if ($is_school_server) {
        $error .= "<h3 style='color: #495057; margin-bottom: 10px;'>School Server Troubleshooting:</h3>";
        $error .= "<ol style='color: #6c757d; line-height: 1.6;'>";
        $error .= "<li><strong>Check Database Name:</strong> Ensure '" . DB_NAME . "' exists in your phpMyAdmin</li>";
        $error .= "<li><strong>Check Credentials:</strong> Verify your school username and password</li>";
        $error .= "<li><strong>Import Database:</strong> Make sure you've imported setup.sql to your database</li>";
        $error .= "<li><strong>Contact IT Support:</strong> If issues persist, contact your school's IT department</li>";
        $error .= "</ol>";
    } else {
        $error .= "<h3 style='color: #495057; margin-bottom: 10px;'>XAMPP Troubleshooting:</h3>";
        $error .= "<ol style='color: #6c757d; line-height: 1.6;'>";
        $error .= "<li><strong>Start XAMPP:</strong> Open XAMPP Control Panel → Start Apache and MySQL</li>";
        $error .= "<li><strong>Create Database:</strong> Go to <a href='http://localhost/phpmyadmin' target='_blank'>phpMyAdmin</a> → Create 'quickbite_db'</li>";
        $error .= "<li><strong>Import Database:</strong> Import database/setup.sql file</li>";
        $error .= "<li><strong>Check Port:</strong> MySQL usually runs on port 3306 or 3307</li>";
        $error .= "</ol>";
    }
    
    $error .= "<div style='background: #e9ecef; padding: 15px; border-radius: 5px; margin-top: 15px;'>";
    $error .= "<p style='margin: 0; color: #495057;'><strong>💡 Tip:</strong> The system automatically detects your environment and uses appropriate settings</p>";
    $error .= "</div>";
    
    $error .= "<p style='margin-top: 15px;'><a href='javascript:history.back()' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>← Go Back</a></p>";
    $error .= "</div>";
    
    die($error);
}
?>