<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login_fixed.php');
    exit();
}

$conn = getDBConnection();

// Get basic statistics
$stats = [];

// Total orders
$result = $conn->query("SELECT COUNT(*) as count FROM orders");
$stats['total_orders'] = $result->fetch_assoc()['count'];

// Total revenue
$result = $conn->query("SELECT SUM(total_amount) as revenue FROM orders WHERE status = 'delivered'");
$stats['total_revenue'] = $result->fetch_assoc()['revenue'] ?? 0;

// Total customers
$result = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'customer'");
$stats['total_customers'] = $result->fetch_assoc()['count'];

// Total messages
$result = $conn->query("SELECT COUNT(*) as count FROM contact_messages");
$stats['total_messages'] = $result->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - QuickBite Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body class="admin-page">
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="admin-content">
        <?php include 'includes/topbar.php'; ?>
        
        <div class="content-wrapper">
            <div class="page-header">
                <h1 class="headline-1">Reports & Analytics</h1>
                <p class="body-2">Business insights and performance metrics</p>
            </div>

            <div class="stats-row">
                <div class="stat-box stat-primary">
                    <ion-icon name="receipt-outline"></ion-icon>
                    <div>
                        <h3><?php echo $stats['total_orders']; ?></h3>
                        <p>Total Orders</p>
                    </div>
                </div>
                
                <div class="stat-box stat-success">
                    <ion-icon name="cash-outline"></ion-icon>
                    <div>
                        <h3>$<?php echo number_format($stats['total_revenue'], 2); ?></h3>
                        <p>Total Revenue</p>
                    </div>
                </div>
                
                <div class="stat-box stat-warning">
                    <ion-icon name="people-outline"></ion-icon>
                    <div>
                        <h3><?php echo $stats['total_customers']; ?></h3>
                        <p>Total Customers</p>
                    </div>
                </div>
                
                <div class="stat-box stat-primary">
                    <ion-icon name="mail-outline"></ion-icon>
                    <div>
                        <h3><?php echo $stats['total_messages']; ?></h3>
                        <p>Total Messages</p>
                    </div>
                </div>
            </div>

            <div class="reports-section">
                <h2 class="headline-2">Available Reports</h2>
                <div class="reports-grid">
                    <div class="report-card">
                        <h3>Sales Report</h3>
                        <p>Daily, weekly, and monthly sales analysis</p>
                        <button class="btn btn-primary">Generate</button>
                    </div>
                    
                    <div class="report-card">
                        <h3>Customer Report</h3>
                        <p>Customer registration and activity trends</p>
                        <button class="btn btn-primary">Generate</button>
                    </div>
                    
                    <div class="report-card">
                        <h3>Menu Performance</h3>
                        <p>Most popular items and category analysis</p>
                        <button class="btn btn-primary">Generate</button>
                    </div>
                    
                    <div class="report-card">
                        <h3>Revenue Analysis</h3>
                        <p>Revenue trends and financial insights</p>
                        <button class="btn btn-primary">Generate</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>

<?php $conn->close(); ?>