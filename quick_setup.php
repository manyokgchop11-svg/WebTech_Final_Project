<?php
echo "<!DOCTYPE html><html><head><title>Quick Setup</title>";
echo "<style>body{font-family:Arial;padding:20px;background:#1a1a1a;color:white;} .container{max-width:600px;margin:0 auto;} h1{color:#ffd700;} .btn{background:#ffd700;color:#000;padding:10px 20px;text-decoration:none;border-radius:5px;margin:10px 5px;display:inline-block;} .success{color:#27ae60;} .error{color:#e74c3c;}</style>";
echo "</head><body><div class='container'>";

echo "<h1>🚀 QuickBite Quick Setup</h1>";

// Test database connection
echo "<h2>Step 1: Testing Database Connection</h2>";
try {
    require_once 'config/database.php';
    $conn = getDBConnection();
    echo "<p class='success'>✅ Database connection successful!</p>";
    
    // Check if users table exists
    $result = $conn->query("SHOW TABLES LIKE 'users'");
    if ($result->num_rows > 0) {
        echo "<p class='success'>✅ Users table exists</p>";
        
        // Check if admin user exists
        $admin_check = $conn->query("SELECT * FROM users WHERE email = 'admin@quickbite.com'");
        if ($admin_check->num_rows > 0) {
            echo "<p class='success'>✅ Admin user exists</p>";
        } else {
            echo "<p class='error'>❌ Admin user missing</p>";
        }
    } else {
        echo "<p class='error'>❌ Users table missing - need to run setup</p>";
    }
    
    // Check if orders table exists
    $result = $conn->query("SHOW TABLES LIKE 'orders'");
    if ($result->num_rows > 0) {
        echo "<p class='success'>✅ Orders table exists</p>";
    } else {
        echo "<p class='error'>❌ Orders table missing - need to run setup</p>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Database error: " . $e->getMessage() . "</p>";
}

echo "<h2>Step 2: Actions</h2>";
echo "<a href='setup_tables.php' class='btn'>🔧 Run Database Setup</a>";
echo "<a href='auth/login_fixed.php' class='btn'>🔑 Test Login</a>";
echo "<a href='index.php' class='btn'>🏠 Main Website</a>";

echo "<h2>Step 3: Test Credentials</h2>";
echo "<p><strong>Admin:</strong> admin@quickbite.com / admin123</p>";
echo "<p><strong>Customer:</strong> customer@test.com / customer123</p>";

echo "</div></body></html>";
?>