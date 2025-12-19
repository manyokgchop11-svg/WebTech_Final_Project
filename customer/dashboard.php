<?php
session_start();
require_once '../config/database.php';
require_once '../auth/session_check.php';

// Security: Check authentication and role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: ../auth/login_fixed.php');
    exit();
}

// Security: Regenerate session ID to prevent session fixation
if (!isset($_SESSION['regenerated'])) {
    session_regenerate_id(true);
    $_SESSION['regenerated'] = true;
}

$conn = getDBConnection();
$user_id = (int)$_SESSION['user_id']; // Type casting for security

// Get user info with prepared statement
$query = "SELECT id, username, email, full_name, phone, created_at FROM users WHERE id = ? AND role = 'customer'";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Security: Double-check user exists and is customer
if (!$user) {
    session_destroy();
    header('Location: ../auth/login_fixed.php');
    exit();
}

// Get recent orders with security
$recent_orders = [];
$orders_query = "SELECT id, total_amount, status, created_at, delivery_address 
                 FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 5";
$stmt = $conn->prepare($orders_query);
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $recent_orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Get comprehensive order statistics
$stats = [
    'total_orders' => 0,
    'pending_orders' => 0,
    'preparing_orders' => 0,
    'ready_orders' => 0,
    'delivered_orders' => 0,
    'total_spent' => 0,
    'avg_order_value' => 0
];
$stats_query = "SELECT 
                COUNT(*) as total_orders,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
                SUM(CASE WHEN status = 'preparing' THEN 1 ELSE 0 END) as preparing_orders,
                SUM(CASE WHEN status = 'ready' THEN 1 ELSE 0 END) as ready_orders,
                SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered_orders,
                COALESCE(SUM(total_amount), 0) as total_spent,
                COALESCE(AVG(total_amount), 0) as avg_order_value
                FROM orders WHERE user_id = ?";
$stmt = $conn->prepare($stats_query);
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    if ($result) {
        $stats = $result;
    }
}

// Get cart count from localStorage 
$cart_count = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - QuickBite</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/customer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="csrf-token" content="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
    <style>
        /* Dashboard Styles */
        .dashboard-page {
            background: var(--smoky-black-1);
            min-height: 100vh;
        }

        /* Header Styles */
        .customer-header {
            background: var(--eerie-black-2);
            border-bottom: 1px solid var(--white-alpha-10);
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }
        
        .header-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 15px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .logo img {
            max-width: 120px;
            height: auto;
            border-radius: 12px;
        }
        
        .customer-nav {
            display: flex;
            gap: 10px;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            color: var(--quick-silver);
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s ease;
            font-size: 1.4rem;
            font-weight: 500;
            position: relative;
        }
        
        .nav-link:hover,
        .nav-link.active {
            background: var(--smoky-black-1);
            color: var(--gold-crayola);
            transform: translateY(-2px);
        }
        
        .nav-link ion-icon {
            font-size: 2rem;
        }
        
        .cart-count, .notification-count {
            position: absolute;
            top: 5px;
            right: 5px;
            background: var(--gold-crayola);
            color: var(--smoky-black-1);
            width: 22px;
            height: 22px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            font-weight: bold;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .header-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .logout-btn {
            background: var(--red-crayola, #dc3545) !important;
            color: white !important;
            border: 2px solid var(--red-crayola, #dc3545);
        }

        .logout-btn:hover {
            background: transparent !important;
            color: var(--red-crayola, #dc3545) !important;
            border-color: var(--red-crayola, #dc3545);
        }
        
        .btn-text {
            display: flex;
            align-items: center;
            gap: 6px;
            color: var(--quick-silver);
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 1.4rem;
        }
        
        .btn-text:hover {
            background: var(--smoky-black-1);
            color: var(--gold-crayola);
        }

        /* Dashboard Content */
        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 30px;
        }
        
        .welcome-section {
            background: linear-gradient(135deg, var(--eerie-black-2), var(--smoky-black-1));
            border: 1px solid var(--white-alpha-10);
            border-radius: 20px;
            padding: 50px;
            margin-bottom: 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .welcome-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--gold-crayola), var(--white));
        }
        
        .welcome-section h1 {
            color: var(--gold-crayola);
            font-size: 3.5rem;
            margin-bottom: 15px;
            font-weight: 700;
        }
        
        .welcome-section p {
            color: var(--quick-silver);
            font-size: 1.8rem;
            margin-bottom: 25px;
        }

        .quick-actions {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .quick-action-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 25px;
            background: var(--gold-crayola);
            color: var(--smoky-black-1);
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .quick-action-btn:hover {
            background: var(--white);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(138, 180, 70, 0.3);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: var(--eerie-black-2);
            border: 1px solid var(--white-alpha-10);
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--gold-crayola);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        .stat-card:hover::before {
            transform: scaleX(1);
        }

        .stat-icon {
            font-size: 3.5rem;
            color: var(--gold-crayola);
            margin-bottom: 15px;
        }

        .stat-value {
            font-size: 2.8rem;
            font-weight: 700;
            color: var(--white);
            margin-bottom: 8px;
        }

        .stat-label {
            color: var(--quick-silver);
            font-size: 1.4rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Recent Orders Section */
        .recent-orders {
            background: var(--eerie-black-2);
            border: 1px solid var(--white-alpha-10);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 40px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--white-alpha-10);
        }

        .section-title {
            color: var(--gold-crayola);
            font-size: 2.4rem;
            font-weight: 600;
        }

        .view-all-btn {
            color: var(--quick-silver);
            text-decoration: none;
            font-size: 1.4rem;
            transition: color 0.3s ease;
        }

        .view-all-btn:hover {
            color: var(--gold-crayola);
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background: var(--smoky-black-1);
            border-radius: 12px;
            margin-bottom: 15px;
            border: 1px solid var(--white-alpha-10);
            transition: all 0.3s ease;
        }

        .order-item:hover {
            transform: translateX(5px);
            border-color: var(--gold-crayola);
        }

        .order-info h4 {
            color: var(--gold-crayola);
            font-size: 1.6rem;
            margin-bottom: 5px;
        }

        .order-info p {
            color: var(--quick-silver);
            font-size: 1.3rem;
        }

        .order-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 1.2rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending { background: #ffc107; color: #000; }
        .status-preparing { background: #17a2b8; color: #fff; }
        .status-ready { background: #28a745; color: #fff; }
        .status-delivered { background: #6f42c1; color: #fff; }
        .status-cancelled { background: #dc3545; color: #fff; }

        /* Responsive Design */
        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                gap: 20px;
            }
            
            .customer-nav {
                flex-wrap: wrap;
                justify-content: center;
            }

            .welcome-section {
                padding: 30px 20px;
            }

            .welcome-section h1 {
                font-size: 2.5rem;
            }

            .quick-actions {
                flex-direction: column;
                align-items: center;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .order-item {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            color: var(--gold-crayola);
            margin-bottom: 10px;
        }
        
        .stat-label {
            color: var(--quick-silver);
            font-size: 1.6rem;
        }
        
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .action-card {
            background: var(--eerie-black-2);
            border: 1px solid var(--white-alpha-10);
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
        }
        
        .action-icon {
            font-size: 4rem;
            color: var(--gold-crayola);
            margin-bottom: 20px;
        }
        
        .action-title {
            color: var(--white);
            font-size: 2rem;
            margin-bottom: 15px;
        }
        
        .action-description {
            color: var(--quick-silver);
            font-size: 1.4rem;
            margin-bottom: 25px;
        }
        
        .action-btn {
            background: var(--gold-crayola);
            color: var(--smoky-black-1);
            padding: 12px 25px;
            border: none;
            border-radius: 25px;
            font-size: 1.4rem;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .action-btn:hover {
            background: var(--white);
            transform: translateY(-2px);
        }
        
        .recent-orders {
            background: var(--eerie-black-2);
            border: 1px solid var(--white-alpha-10);
            border-radius: 20px;
            padding: 30px;
        }
        
        .section-title {
            color: var(--gold-crayola);
            font-size: 2.4rem;
            margin-bottom: 25px;
        }
        
        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
            border-bottom: 1px solid var(--white-alpha-10);
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .order-info h4 {
            color: var(--white);
            font-size: 1.6rem;
            margin-bottom: 5px;
        }
        
        .order-info p {
            color: var(--quick-silver);
            font-size: 1.3rem;
        }
        
        .order-status {
            padding: 8px 15px;
            border-radius: 15px;
            font-size: 1.2rem;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-pending {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
        }
        
        .status-delivered {
            background: rgba(39, 174, 96, 0.2);
            color: #27ae60;
        }
        
        .status-cancelled {
            background: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
        }
        
        .empty-orders {
            text-align: center;
            padding: 40px;
            color: var(--quick-silver);
        }
        
        .empty-orders h4 {
            color: var(--gold-crayola);
            margin-bottom: 15px;
        }
        
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 20px 15px;
            }
            
            .welcome-section h1 {
                font-size: 2.4rem;
            }
            
            .stats-grid,
            .quick-actions {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="dashboard-page">
    <!-- Customer Header with Navigation -->
    <header class="customer-header">
        <div class="header-container">
            <a href="../index.php" class="logo">
                <img src="../assets/images/logo1.png" alt="QuickBite">
            </a>

            <nav class="customer-nav">
                <a href="dashboard.php" class="nav-link active">
                    <ion-icon name="home-outline"></ion-icon>
                    <span>Dashboard</span>
                </a>
                <a href="menu.php" class="nav-link">
                    <ion-icon name="restaurant-outline"></ion-icon>
                    <span>Menu</span>
                </a>
                <a href="orders.php?tab=cart" class="nav-link">
                    <ion-icon name="cart-outline"></ion-icon>
                    <span>Cart</span>
                    <span class="cart-count" id="cartCount">0</span>
                </a>
                <a href="orders.php?tab=orders" class="nav-link">
                    <ion-icon name="list-outline"></ion-icon>
                    <span>Orders</span>
                </a>
                <a href="profile.php" class="nav-link">
                    <ion-icon name="person-outline"></ion-icon>
                    <span>Profile</span>
                </a>
                <a href="#" class="nav-link" onclick="showNotifications()">
                    <ion-icon name="notifications-outline"></ion-icon>
                    <span>Notifications</span>
                    <span class="notification-count" id="notificationCount">0</span>
                </a>
            </nav>

            <div class="header-actions">
                <a href="../index.php" class="nav-link">
                    <ion-icon name="home-outline"></ion-icon>
                    <span>Website</span>
                </a>
                <a href="../auth/logout.php" class="nav-link logout-btn">
                    <ion-icon name="log-out-outline"></ion-icon>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Dashboard Content -->
    <div class="dashboard-container">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <h1>Welcome back, <?php echo htmlspecialchars($user['full_name'] ?? $user['username']); ?>!</h1>
            <p>Ready to order something delicious?</p>
            
            <div class="quick-actions">
                <a href="../index.php#menu" class="quick-action-btn">
                    <ion-icon name="restaurant-outline"></ion-icon>
                    <span>Browse Menu</span>
                </a>
                <a href="orders.php?tab=cart" class="quick-action-btn">
                    <ion-icon name="cart-outline"></ion-icon>
                    <span>View Cart</span>
                </a>
                <a href="orders.php?tab=orders" class="quick-action-btn">
                    <ion-icon name="time-outline"></ion-icon>
                    <span>Track Orders</span>
                </a>
                <a href="profile.php" class="quick-action-btn">
                    <ion-icon name="person-outline"></ion-icon>
                    <span>My Profile</span>
                </a>
            </div>
        </div>

        <!-- Statistics Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <div class="stat-value"><?php echo number_format($stats['total_orders']); ?></div>
                <div class="stat-label">Total Orders</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-value"><?php echo number_format($stats['pending_orders'] + $stats['preparing_orders']); ?></div>
                <div class="stat-label">Active Orders</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-value"><?php echo number_format($stats['delivered_orders']); ?></div>
                <div class="stat-label">Completed</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-value">SSP <?php echo number_format($stats['total_spent']); ?></div>
                <div class="stat-label">Total Spent</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-value" id="dashboardCartCount">0</div>
                <div class="stat-label">Items in Cart</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-value">SSP <?php echo number_format($stats['avg_order_value']); ?></div>
                <div class="stat-label">Avg Order Value</div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="recent-orders">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-history"></i> Recent Orders
                </h2>
                <a href="orders.php?tab=orders" class="view-all-btn">View All Orders →</a>
            </div>

            <?php if (count($recent_orders) > 0): ?>
                <?php foreach ($recent_orders as $order): ?>
                    <div class="order-item">
                        <div class="order-info">
                            <h4>Order #<?php echo $order['id']; ?></h4>
                            <p>
                                <i class="fas fa-calendar"></i>
                                <?php echo date('M j, Y g:i A', strtotime($order['created_at'])); ?>
                            </p>
                            <?php if ($order['delivery_address']): ?>
                                <p>
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo htmlspecialchars($order['delivery_address']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="order-details">
                            <div class="order-amount">
                                <strong>SSP <?php echo number_format($order['total_amount']); ?></strong>
                            </div>
                            <div class="order-status status-<?php echo $order['status']; ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-shopping-bag" style="font-size: 4rem; color: var(--white-alpha-20); margin-bottom: 20px;"></i>
                    <h3 style="color: var(--gold-crayola); margin-bottom: 10px;">No orders yet</h3>
                    <p style="color: var(--quick-silver); margin-bottom: 25px;">Start by browsing our delicious menu!</p>
                    <a href="../index.php#menu" class="quick-action-btn">
                        <ion-icon name="restaurant-outline"></ion-icon>
                        <span>Browse Menu</span>
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Quick Features Grid -->
        <div class="features-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px; margin-top: 40px;">
            <div class="feature-card" style="background: var(--eerie-black-2); border: 1px solid var(--white-alpha-10); border-radius: 15px; padding: 25px; text-align: center;">
                <i class="fas fa-search" style="font-size: 3rem; color: var(--gold-crayola); margin-bottom: 15px;"></i>
                <h3 style="color: var(--gold-crayola); margin-bottom: 10px;">Search & Browse</h3>
                <p style="color: var(--quick-silver); margin-bottom: 20px;">Find your favorite dishes easily with our search and category filters</p>
                <a href="menu.php" class="btn btn-secondary">Browse Menu</a>
            </div>

            <div class="feature-card" style="background: var(--eerie-black-2); border: 1px solid var(--white-alpha-10); border-radius: 15px; padding: 25px; text-align: center;">
                <i class="fas fa-truck" style="font-size: 3rem; color: var(--gold-crayola); margin-bottom: 15px;"></i>
                <h3 style="color: var(--gold-crayola); margin-bottom: 10px;">Order Tracking</h3>
                <p style="color: var(--quick-silver); margin-bottom: 20px;">Track your orders in real-time from preparation to delivery</p>
                <a href="orders.php?tab=orders" class="btn btn-secondary">Track Orders</a>
            </div>

            <div class="feature-card" style="background: var(--eerie-black-2); border: 1px solid var(--white-alpha-10); border-radius: 15px; padding: 25px; text-align: center;">
                <i class="fas fa-headset" style="font-size: 3rem; color: var(--gold-crayola); margin-bottom: 15px;"></i>
                <h3 style="color: var(--gold-crayola); margin-bottom: 10px;">Customer Support</h3>
                <p style="color: var(--quick-silver); margin-bottom: 20px;">Need help? Our support team is here to assist you</p>
                <a href="#" onclick="showSupport()" class="btn btn-secondary">Get Help</a>
            </div>
        </div>
    </div>

    <!-- Notification Modal -->
    <div id="notificationModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
        <div class="modal-content" style="background: var(--eerie-black-2); margin: 5% auto; padding: 30px; border-radius: 15px; width: 90%; max-width: 600px; border: 1px solid var(--white-alpha-10);">
            <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid var(--white-alpha-10);">
                <h3 style="color: var(--gold-crayola); margin: 0;"><i class="fas fa-bell"></i> Notifications</h3>
                <span class="close" onclick="closeNotifications()" style="color: var(--quick-silver); font-size: 2rem; cursor: pointer;">&times;</span>
            </div>
            <div class="modal-body" id="notificationsList">
                <div class="notification-item" style="display: flex; gap: 15px; padding: 15px; background: var(--smoky-black-1); border-radius: 10px; margin-bottom: 15px;">
                    <i class="fas fa-info-circle" style="color: var(--gold-crayola); font-size: 2rem;"></i>
                    <div>
                        <h4 style="color: var(--gold-crayola); margin: 0 0 5px 0;">Welcome to QuickBite!</h4>
                        <p style="color: var(--quick-silver); margin: 0 0 5px 0;">Thank you for joining us. Enjoy browsing our delicious menu!</p>
                        <small style="color: var(--white-alpha-20);">Just now</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Support Modal -->
    <div id="supportModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
        <div class="modal-content" style="background: var(--eerie-black-2); margin: 5% auto; padding: 30px; border-radius: 15px; width: 90%; max-width: 600px; border: 1px solid var(--white-alpha-10);">
            <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid var(--white-alpha-10);">
                <h3 style="color: var(--gold-crayola); margin: 0;"><i class="fas fa-headset"></i> Customer Support</h3>
                <span class="close" onclick="closeSupport()" style="color: var(--quick-silver); font-size: 2rem; cursor: pointer;">&times;</span>
            </div>
            <div class="modal-body">
                <div class="support-options" style="display: grid; gap: 20px; margin-bottom: 25px;">
                    <div class="support-option" style="display: flex; gap: 15px; padding: 15px; background: var(--smoky-black-1); border-radius: 10px;">
                        <i class="fas fa-phone" style="color: var(--gold-crayola); font-size: 2rem;"></i>
                        <div>
                            <h4 style="color: var(--gold-crayola); margin: 0 0 5px 0;">Call Us</h4>
                            <p style="color: var(--quick-silver); margin: 0;">+211 9224 888 68</p>
                        </div>
                    </div>
                    <div class="support-option" style="display: flex; gap: 15px; padding: 15px; background: var(--smoky-black-1); border-radius: 10px;">
                        <i class="fas fa-envelope" style="color: var(--gold-crayola); font-size: 2rem;"></i>
                        <div>
                            <h4 style="color: var(--gold-crayola); margin: 0 0 5px 0;">Email Support</h4>
                            <p style="color: var(--quick-silver); margin: 0;">support@quickbite.com</p>
                        </div>
                    </div>
                    <div class="support-option" style="display: flex; gap: 15px; padding: 15px; background: var(--smoky-black-1); border-radius: 10px;">
                        <i class="fas fa-comments" style="color: var(--gold-crayola); font-size: 2rem;"></i>
                        <div>
                            <h4 style="color: var(--gold-crayola); margin: 0 0 5px 0;">Live Chat</h4>
                            <p style="color: var(--quick-silver); margin: 0;">Available 24/7</p>
                        </div>
                    </div>
                </div>
                
                <form class="support-form" style="margin-top: 25px;">
                    <h4 style="color: var(--gold-crayola); margin-bottom: 15px;">Send us a message</h4>
                    <textarea placeholder="Describe your issue..." rows="4" style="width: 100%; margin-bottom: 15px; padding: 10px; border-radius: 8px; border: 1px solid var(--white-alpha-10); background: var(--smoky-black-1); color: var(--white);"></textarea>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script src="../assets/js/cart-optimized.js"></script>
    
    <script>
        // Security: CSRF Token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Cart Management
        let cart = JSON.parse(localStorage.getItem('quickbite_cart')) || [];
        
        function updateCartCounts() {
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            const cartElements = document.querySelectorAll('#cartCount, #dashboardCartCount');
            cartElements.forEach(element => {
                if (element) element.textContent = totalItems;
            });
        }

        // Notification System
        function showNotifications() {
            document.getElementById('notificationModal').style.display = 'block';
            // Mark notifications as read
            document.getElementById('notificationCount').textContent = '0';
        }

        function closeNotifications() {
            document.getElementById('notificationModal').style.display = 'none';
        }

        // Support System
        function showSupport() {
            document.getElementById('supportModal').style.display = 'block';
        }

        function closeSupport() {
            document.getElementById('supportModal').style.display = 'none';
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const notificationModal = document.getElementById('notificationModal');
            const supportModal = document.getElementById('supportModal');
            
            if (event.target === notificationModal) {
                notificationModal.style.display = 'none';
            }
            if (event.target === supportModal) {
                supportModal.style.display = 'none';
            }
        }

        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            updateCartCounts();
            
            // Simulate notifications
            setTimeout(() => {
                document.getElementById('notificationCount').textContent = '1';
            }, 2000);
        });

        // Auto-refresh cart count every 30 seconds
        setInterval(updateCartCounts, 30000);
    </script>
</body>
</html>