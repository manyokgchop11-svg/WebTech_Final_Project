<?php
require_once 'config/database.php';

echo "<!DOCTYPE html><html><head><title>Database Connection Test</title>";
echo "<style>body{font-family:Arial;padding:20px;background:#f8f9fa;} .container{max-width:800px;margin:0 auto;} .success{color:#28a745;background:#d4edda;padding:15px;border-radius:8px;margin:10px 0;} .error{color:#dc3545;background:#f8d7da;padding:15px;border-radius:8px;margin:10px 0;} .info{color:#0c5460;background:#d1ecf1;padding:15px;border-radius:8px;margin:10px 0;} .btn{background:#007bff;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;margin:5px;display:inline-block;}</style>";
echo "</head><body><div class='container'>";

echo "<h1>🔧 Database Connection Test</h1>";

echo "<h2>Environment Detection:</h2>";
global $is_school_server;
$environment = $is_school_server ? 'School Server' : 'Local XAMPP';
echo "<div class='info'>";
echo "<p><strong>Detected Environment:</strong> " . $environment . "</p>";
echo "<p><strong>Host:</strong> " . DB_HOST . "</p>";
echo "<p><strong>User:</strong> " . DB_USER . "</p>";
echo "<p><strong>Database:</strong> " . DB_NAME . "</p>";
echo "<p><strong>Port:</strong> " . DB_PORT . "</p>";
echo "</div>";

echo "<h2>Testing Connection:</h2>";

try {
    $conn = getDBConnection();
    echo "<div class='success'>✅ Database connection successful!</div>";
    
    // Test if users table exists
    $result = $conn->query("SHOW TABLES LIKE 'users'");
    if ($result && $result->num_rows > 0) {
        echo "<div class='success'>✅ Users table exists</div>";
        
        // Count users
        $count_result = $conn->query("SELECT COUNT(*) as count FROM users");
        if ($count_result) {
            $count = $count_result->fetch_assoc()['count'];
            echo "<div class='info'>ℹ️ Found $count users in database</div>";
        }
        
        // Count menu items
        $menu_result = $conn->query("SELECT COUNT(*) as count FROM menu_items");
        if ($menu_result) {
            $menu_count = $menu_result->fetch_assoc()['count'];
            echo "<div class='info'>ℹ️ Found $menu_count menu items</div>";
        }
        
        // Count orders
        $order_result = $conn->query("SELECT COUNT(*) as count FROM orders");
        if ($order_result) {
            $order_count = $order_result->fetch_assoc()['count'];
            echo "<div class='info'>ℹ️ Found $order_count orders</div>";
        }
        
        // Count tables
        $table_result = $conn->query("SELECT COUNT(*) as count FROM tables");
        if ($table_result) {
            $table_count = $table_result->fetch_assoc()['count'];
            echo "<div class='info'>ℹ️ Found $table_count restaurant tables</div>";
        }
        
    } else {
        echo "<div class='error'>❌ Users table missing - database may not be set up properly</div>";
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Connection failed: " . $e->getMessage() . "</div>";
}

echo "<h2>XAMPP Status Check:</h2>";
echo "<div class='info'>";
echo "<p><strong>What to check in XAMPP Control Panel:</strong></p>";
echo "<ol>";
echo "<li>Apache should be running (green)</li>";
echo "<li>MySQL should be running (green)</li>";
echo "<li>Note the MySQL port number (usually 3306 or 3307)</li>";
echo "</ol>";
echo "</div>";

echo "<h2>Next Steps:</h2>";
echo "<a href='setup_tables.php' class='btn'>🔧 Setup Database</a>";
echo "<a href='auth/login_fixed.php' class='btn'>🔑 Test Login</a>";
echo "<a href='index.php' class='btn'>🏠 Main Website</a>";

echo "<h2>Manual Steps if Issues Persist:</h2>";
echo "<div class='info'>";
echo "<ol>";
echo "<li>Open XAMPP Control Panel</li>";
echo "<li>Stop MySQL if running</li>";
echo "<li>Start MySQL again</li>";
echo "<li>Click 'Admin' next to MySQL to open phpMyAdmin</li>";
echo "<li>Use your assigned database (e.g., webtech_2025A_manyok_deng)</li>";
echo "<li>Run setup_tables.php to create tables</li>";
echo "</ol>";
echo "</div>";

echo "</div></body></html>";
?>