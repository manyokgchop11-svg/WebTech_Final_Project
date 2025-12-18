<?php
require_once 'config/database.php';

echo "<h2>Setting up Database Tables...</h2>";

try {
    $conn = getDBConnection();
    echo "✅ Connected to database<br><br>";
    
    // Read setup.sql file
    $sql_content = file_get_contents('database/setup.sql');
    
    if (!$sql_content) {
        die("❌ Could not read database/setup.sql file");
    }
    
    // Split SQL statements
    $statements = array_filter(array_map('trim', explode(';', $sql_content)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            if ($conn->query($statement)) {
                echo "✅ Executed: " . substr($statement, 0, 50) . "...<br>";
            } else {
                echo "❌ Error: " . $conn->error . "<br>";
                echo "Statement: " . substr($statement, 0, 100) . "...<br><br>";
            }
        }
    }
    
    // Create admin user
    $admin_email = 'admin@quickbite.com';
    $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT IGNORE INTO users (username, email, password, full_name, role, status) VALUES (?, ?, ?, ?, ?, ?)");
    $username = 'admin';
    $full_name = 'System Administrator';
    $role = 'admin';
    $status = 'active';
    
    $stmt->bind_param("ssssss", $username, $admin_email, $admin_password, $full_name, $role, $status);
    
    if ($stmt->execute()) {
        echo "<br>✅ Admin user created: admin@quickbite.com / admin123<br>";
    } else {
        echo "<br>ℹ️ Admin user already exists<br>";
    }
    
    echo "<br><h3>✅ Database setup complete!</h3>";
    echo "<p><a href='auth/login_fixed.php'>Try Login Page</a></p>";
    
    $conn->close();
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>

<a href="index.html">← Back to Website</a>