<?php
session_start();
require_once '../config/database.php';
require_once '../auth/session_check.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login_fixed.php');
    exit();
}

$conn = getDBConnection();

// Get statistics
$stats = [];

// Total orders
$result = $conn->query("SELECT COUNT(*) as count, SUM(total_amount) as revenue FROM orders");
$stats['orders'] = $result->fetch_assoc();

// Today's orders
$result = $conn->query("SELECT COUNT(*) as count FROM orders WHERE DATE(created_at) = CURDATE()");
$stats['today_orders'] = $result->fetch_assoc()['count'];

// Pending orders
$result = $conn->query("SELECT COUNT(*) as count FROM orders WHERE status = 'pending'");
$stats['pending_orders'] = $result->fetch_assoc()['count'];

// Total customers
$result = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'customer'");
$stats['customers'] = $result->fetch_assoc()['count'];

// Reservations
$result = $conn->query("SELECT COUNT(*) as count FROM reservations WHERE status = 'pending'");
$stats['pending_reservations'] = $result->fetch_assoc()['count'];

// Messages - Get all message counts
$result = $conn->query("SELECT 
    COUNT(*) as total_messages,
    SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) as new_messages,
    SUM(CASE WHEN status = 'read' THEN 1 ELSE 0 END) as read_messages,
    SUM(CASE WHEN status = 'replied' THEN 1 ELSE 0 END) as replied_messages
    FROM contact_messages");
$message_stats = $result->fetch_assoc();
$stats['new_messages'] = $message_stats['new_messages'];
$stats['total_messages'] = $message_stats['total_messages'];

// Recent orders
$recent_orders = $conn->query("SELECT o.*, u.full_name FROM orders o 
                               LEFT JOIN users u ON o.user_id = u.id 
                               ORDER BY o.created_at DESC LIMIT 10");

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - QuickBite</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body class="dashboard-page">
    
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'includes/topbar.php'; ?>
        
        <div class="dashboard-container">
            <div class="page-header">
                <h1 class="headline-1">Dashboard Overview</h1>
                <p class="body-2">Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</p>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card stat-primary">
                    <div class="stat-icon">
                        <ion-icon name="cart-outline"></ion-icon>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-value"><?php echo number_format($stats['orders']['count']); ?></h3>
                        <p class="stat-label">Total Orders</p>
                        <p class="stat-sublabel">SSP <?php echo number_format($stats['orders']['revenue'], 2); ?> Revenue</p>
                    </div>
                </div>

                <div class="stat-card stat-success">
                    <div class="stat-icon">
                        <ion-icon name="today-outline"></ion-icon>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-value"><?php echo $stats['today_orders']; ?></h3>
                        <p class="stat-label">Today's Orders</p>
                        <p class="stat-sublabel">Active orders</p>
                    </div>
                </div>

                <div class="stat-card stat-warning">
                    <div class="stat-icon">
                        <ion-icon name="time-outline"></ion-icon>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-value"><?php echo $stats['pending_orders']; ?></h3>
                        <p class="stat-label">Pending Orders</p>
                        <p class="stat-sublabel">Needs attention</p>
                    </div>
                </div>

                <div class="stat-card stat-info">
                    <div class="stat-icon">
                        <ion-icon name="people-outline"></ion-icon>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-value"><?php echo $stats['customers']; ?></h3>
                        <p class="stat-label">Total Customers</p>
                        <p class="stat-sublabel">Registered users</p>
                    </div>
                </div>

                <div class="stat-card stat-danger">
                    <div class="stat-icon">
                        <ion-icon name="calendar-outline"></ion-icon>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-value"><?php echo $stats['pending_reservations']; ?></h3>
                        <p class="stat-label">Pending Reservations</p>
                        <p class="stat-sublabel">Table bookings</p>
                    </div>
                </div>

                <div class="stat-card stat-purple">
                    <div class="stat-icon">
                        <ion-icon name="mail-outline"></ion-icon>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-value"><?php echo $stats['total_messages']; ?></h3>
                        <p class="stat-label">Total Messages</p>
                        <p class="stat-sublabel"><?php echo $stats['new_messages']; ?> new inquiries</p>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="dashboard-section">
                <div class="section-header">
                    <h2 class="headline-2">Recent Orders</h2>
                    <a href="orders.php" class="btn btn-primary btn-sm">View All</a>
                </div>

                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($order = $recent_orders->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($order['order_number']); ?></strong></td>
                                <td><?php echo htmlspecialchars($order['full_name'] ?? 'Guest'); ?></td>
                                <td><span class="badge badge-info"><?php echo ucfirst($order['order_type']); ?></span></td>
                                <td>SSP <?php echo number_format($order['total_amount'], 2); ?></td>
                                <td><span class="badge badge-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></td>
                                <td><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn-icon" title="View">
                                        <ion-icon name="eye-outline"></ion-icon>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <h2 class="headline-2">Quick Actions</h2>
                <div class="action-grid">
                    <a href="orders.php" class="action-card">
                        <ion-icon name="cart-outline"></ion-icon>
                        <span>Manage Orders</span>
                    </a>
                    <a href="menu_management.php" class="action-card">
                        <ion-icon name="restaurant-outline"></ion-icon>
                        <span>Manage Menu</span>
                    </a>
                    <a href="index.php" class="action-card">
                        <ion-icon name="calendar-outline"></ion-icon>
                        <span>Reservations</span>
                    </a>
                    <a href="messages.php" class="action-card">
                        <ion-icon name="mail-outline"></ion-icon>
                        <span>Messages</span>
                    </a>
                    <a href="customers.php" class="action-card">
                        <ion-icon name="people-outline"></ion-icon>
                        <span>Customers</span>
                    </a>
                    <a href="tables_management.php" class="action-card">
                        <ion-icon name="grid-outline"></ion-icon>
                        <span>Tables</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script src="../assets/js/dashboard.js"></script>
</body>
</html>
