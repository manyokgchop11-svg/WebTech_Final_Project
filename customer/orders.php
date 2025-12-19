<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is a customer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: ../auth/login_fixed.php');
    exit();
}

$conn = getDBConnection();
$user_id = $_SESSION['user_id'];

// Get current tab (cart or orders)
$current_tab = $_GET['tab'] ?? 'cart';

// Get filter for orders
$status_filter = $_GET['status'] ?? 'all';

// Get orders
$query = "SELECT o.*, COUNT(oi.id) as item_count 
          FROM orders o 
          LEFT JOIN order_items oi ON o.id = oi.order_id 
          WHERE o.user_id = ?";

if ($status_filter !== 'all') {
    $query .= " AND o.status = ?";
}

$query .= " GROUP BY o.id ORDER BY o.created_at DESC";

$stmt = $conn->prepare($query);
if ($status_filter !== 'all') {
    $stmt->bind_param("is", $user_id, $status_filter);
} else {
    $stmt->bind_param("i", $user_id);
}
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get order statistics
$stats_query = "SELECT 
                COUNT(*) as total_orders,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
                SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as completed_orders,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders
                FROM orders WHERE user_id = ?";
$stmt = $conn->prepare($stats_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();

// Ensure stats have default values
$stats = $stats ?: ['total_orders' => 0, 'pending_orders' => 0, 'completed_orders' => 0, 'cancelled_orders' => 0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - QuickBite</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/customer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Header Styles */
        .customer-header {
            background: var(--eerie-black-2);
            border-bottom: 1px solid var(--white-alpha-10);
            position: sticky;
            top: 0;
            z-index: 100;
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
            padding: 10px 18px;
            color: var(--quick-silver);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 1.4rem;
            position: relative;
        }
        
        .nav-link:hover,
        .nav-link.active {
            background: var(--smoky-black-1);
            color: var(--gold-crayola);
        }
        
        .nav-link ion-icon {
            font-size: 2rem;
        }
        
        .cart-count {
            position: absolute;
            top: 5px;
            right: 5px;
            background: var(--gold-crayola);
            color: var(--smoky-black-1);
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            font-weight: bold;
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
            padding: 8px 15px;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 1.4rem;
        }
        
        .btn-text:hover {
            background: var(--smoky-black-1);
            color: var(--gold-crayola);
        }
        
        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                gap: 20px;
            }
            
            .customer-nav {
                flex-wrap: wrap;
                justify-content: center;
            }
        }

        /* Main Tabs Styles */
        .main-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px 10px 0 0;
            padding: 10px;
        }

        .main-tab {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px 25px;
            color: #e0e0e0;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 1.6rem;
            font-weight: 600;
            position: relative;
            background: rgba(255, 255, 255, 0.05);
        }

        .main-tab:hover,
        .main-tab.active {
            background: #ffd700;
            color: #1a1a1a;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 215, 0, 0.3);
        }

        .main-tab .cart-count {
            background: #ff4757;
            color: #ffffff;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 1.2rem;
            font-weight: bold;
            min-width: 20px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(255, 71, 87, 0.4);
        }

        .main-tab.active .cart-count {
            background: #1a1a1a;
            color: #ffd700;
        }

        /* Ensure cart count badges are always visible when they have content */
        .cart-count {
            display: flex !important;
            align-items: center;
            justify-content: center;
            min-width: 20px;
            height: 20px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 50%;
        }
        
        .cart-count:empty {
            display: none !important;
        }

        /* Cart Section Styles */
        .cart-section {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 15px;
            padding: 30px;
            border: 1px solid rgba(255, 215, 0, 0.3);
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .cart-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .cart-header h2 {
            color: #ffd700;
            font-size: 2.8rem;
            margin-bottom: 10px;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        }

        .cart-header p {
            color: #e0e0e0;
            font-size: 1.6rem;
            font-weight: 500;
        }

        .cart-layout {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-top: 20px;
        }

        /* Make cart more visible */
        .cart-section {
            background: rgba(255, 255, 255, 0.02);
            border-radius: 15px;
            padding: 30px;
            border: 2px solid var(--gold-crayola);
            box-shadow: 0 8px 32px rgba(255, 215, 0, 0.1);
        }

        .cart-header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: linear-gradient(135deg, var(--gold-crayola), #ffed4e);
            border-radius: 12px;
            color: var(--smoky-black-1);
        }

        .cart-header h2 {
            font-size: 2.8rem;
            font-weight: bold;
            margin-bottom: 10px;
            text-shadow: none;
        }

        .cart-header p {
            font-size: 1.6rem;
            opacity: 0.8;
        }

        .cart-items {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 25px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
        }

        .cart-items-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--white-alpha-10);
        }

        .cart-items-header h3 {
            color: #ffd700;
            font-size: 2rem;
            font-weight: 600;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
        }

        .clear-cart-btn {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 8px 15px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1.4rem;
            font-weight: 500;
        }

        .clear-cart-btn:hover {
            background: #dc3545;
            color: white;
            border-color: #dc3545;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }

        .empty-cart {
            text-align: center;
            padding: 60px 20px;
            color: #e0e0e0;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 12px;
            border: 2px dashed rgba(255, 215, 0, 0.3);
        }

        .empty-cart i {
            font-size: 6rem;
            color: #ffd700;
            margin-bottom: 20px;
            opacity: 0.7;
        }

        .empty-cart h3 {
            font-size: 2.4rem;
            margin-bottom: 10px;
            color: #ffd700;
            font-weight: 600;
        }

        .empty-cart p {
            font-size: 1.6rem;
            margin-bottom: 25px;
            color: #e0e0e0;
        }

        .cart-item {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            margin-bottom: 15px;
            border: 1px solid rgba(255, 215, 0, 0.2);
            backdrop-filter: blur(5px);
            transition: all 0.3s ease;
        }

        .cart-item img {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            object-fit: cover;
        }

        .item-details {
            flex: 1;
        }

        .item-name {
            color: #ffd700;
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 5px;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
        }

        .item-price {
            color: #e0e0e0;
            font-size: 1.4rem;
            font-weight: 500;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .qty-btn {
            background: #ffd700;
            color: #1a1a1a;
            border: none;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1.6rem;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(255, 215, 0, 0.3);
        }

        .qty-btn:hover {
            background: #ffffff;
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(255, 255, 255, 0.3);
        }

        .qty-display {
            color: #ffffff;
            font-size: 1.6rem;
            font-weight: 600;
            min-width: 30px;
            text-align: center;
            background: rgba(255, 255, 255, 0.1);
            padding: 5px 10px;
            border-radius: 8px;
        }

        .remove-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }

        .remove-btn:hover {
            background: #c82333;
            transform: scale(1.05);
        }

        .order-summary {
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.15), rgba(255, 215, 0, 0.05));
            border-radius: 15px;
            padding: 30px;
            border: 2px solid var(--gold-crayola);
            height: fit-content;
            position: sticky;
            top: 100px;
            backdrop-filter: blur(15px);
            box-shadow: 0 15px 40px rgba(255, 215, 0, 0.2);
            animation: glow 2s ease-in-out infinite alternate;
        }

        @keyframes glow {
            from { box-shadow: 0 15px 40px rgba(255, 215, 0, 0.2); }
            to { box-shadow: 0 15px 40px rgba(255, 215, 0, 0.4); }
        }

        /* Floating Cart Summary */
        .floating-cart-summary {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--gold-crayola);
            color: var(--smoky-black-1);
            padding: 15px 20px;
            border-radius: 50px;
            box-shadow: 0 8px 25px rgba(255, 215, 0, 0.4);
            z-index: 1000;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: bold;
            display: none;
        }

        .floating-cart-summary:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 35px rgba(255, 215, 0, 0.6);
        }

        .floating-cart-summary.show {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Quick scroll to top button */
        .scroll-to-top {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background: var(--smoky-black-1);
            color: var(--gold-crayola);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 999;
            border: 2px solid var(--gold-crayola);
        }

        .scroll-to-top:hover {
            background: var(--gold-crayola);
            color: var(--smoky-black-1);
        }

        .order-summary h3 {
            color: #ffd700;
            font-size: 2rem;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 600;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
        }

        .summary-details {
            margin-bottom: 25px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            color: #e0e0e0;
            font-size: 1.5rem;
            font-weight: 500;
        }

        .summary-row.total {
            border-bottom: none;
            font-size: 1.8rem;
            font-weight: 700;
            color: #ffd700;
            margin-top: 10px;
            padding-top: 15px;
            border-top: 2px solid #ffd700;
            background: rgba(255, 215, 0, 0.1);
            padding: 15px;
            border-radius: 8px;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
        }

        .checkout-btn {
            width: 100%;
            background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
            color: #1a1a1a;
            border: none;
            padding: 15px 20px;
            border-radius: 8px;
            font-size: 1.6rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .checkout-btn:hover:not(:disabled) {
            background: linear-gradient(135deg, #ffffff 0%, #f0f0f0 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 215, 0, 0.4);
        }

        .checkout-btn:disabled {
            background: rgba(255, 255, 255, 0.2);
            color: rgba(255, 255, 255, 0.5);
            cursor: not-allowed;
            box-shadow: none;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .cart-layout {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            
            .order-summary {
                position: static;
            }
        }

        @media (max-width: 768px) {
            .main-tabs {
                flex-direction: column;
            }
            
            .cart-item {
                flex-direction: column;
                text-align: center;
            }
            
            .quantity-controls {
                justify-content: center;
            }
        }
    </style>
</head>
<body class="customer-page">
    <header class="customer-header">
        <div class="header-container">
            <a href="../index.php" class="logo">
                <img src="../assets/images/logo1.png" alt="QuickBite">
            </a>

            <nav class="customer-nav">
                <a href="dashboard.php" class="nav-link">
                    <ion-icon name="home-outline"></ion-icon>
                    <span>Dashboard</span>
                </a>
                <a href="cart.php" class="nav-link">
                    <ion-icon name="cart-outline"></ion-icon>
                    <span>Cart</span>
                    <span class="cart-count" id="cartCount">0</span>
                </a>
                <a href="orders.php" class="nav-link active">
                    <ion-icon name="list-outline"></ion-icon>
                    <span>My Orders</span>
                </a>
                <a href="profile.php" class="nav-link">
                    <ion-icon name="person-outline"></ion-icon>
                    <span>Profile</span>
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

    <div class="customer-container">
        <div class="orders-wrapper">
            <h1><i class="fas fa-shopping-cart"></i> Orders & Cart</h1>

            <!-- Main Tabs -->
            <div class="main-tabs">
                <a href="?tab=cart" class="main-tab <?php echo $current_tab === 'cart' ? 'active' : ''; ?>" onclick="forceCartRefresh()">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Current Cart</span>
                    <span class="cart-count" id="mainCartCount">0</span>
                </a>
                <a href="?tab=orders" class="main-tab <?php echo $current_tab === 'orders' ? 'active' : ''; ?>">
                    <i class="fas fa-history"></i>
                    <span>Order History</span>
                </a>
            </div>

            <?php if ($current_tab === 'cart'): ?>
            <!-- CART SECTION -->
            <div class="cart-section">
                <div class="cart-header">
                    <h2>Shopping Cart</h2>
                    <p>Review your order before checkout</p>
                    <div id="cartDebugInfo" style="background: rgba(255,255,255,0.1); padding: 10px; border-radius: 8px; margin: 10px 0; font-size: 12px; color: #ccc;">
                        <strong>Debug Info:</strong> <span id="debugText">Loading...</span>
                    </div>
                </div>

                <div class="cart-layout">
                    <div class="cart-items">
                        <div class="cart-items-header">
                            <h3>Cart Items</h3>
                            <div style="display: flex; gap: 10px;">
                                <button class="clear-cart-btn" onclick="refreshCart()" style="background: #28a745; border-color: #28a745;">
                                    <i class="fas fa-sync"></i> Refresh
                                </button>
                                <button class="clear-cart-btn" onclick="debugCart()" style="background: #17a2b8; border-color: #17a2b8;">
                                    <i class="fas fa-bug"></i> Debug
                                </button>
                                <button class="clear-cart-btn" onclick="addTestItem()" style="background: #6f42c1; border-color: #6f42c1;">
                                    <i class="fas fa-plus"></i> Test Item
                                </button>
                                <button class="clear-cart-btn" onclick="createTestOrder()" style="background: #fd7e14; border-color: #fd7e14;">
                                    <i class="fas fa-receipt"></i> Test Order
                                </button>
                                <button class="clear-cart-btn" onclick="clearCart()">
                                    <i class="fas fa-trash"></i> Clear Cart
                                </button>
                            </div>
                        </div>
                        <div id="cartItemsList">
                            <div class="empty-cart">
                                <i class="fas fa-shopping-cart"></i>
                                <h3>Your cart is empty</h3>
                                <p>Add some delicious items from our menu!</p>
                                <a href="../index.php#menu" class="btn btn-primary">
                                    <span>Browse Menu</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="order-summary">
                        <h3>Order Summary</h3>
                        <div class="summary-details">
                            <div class="summary-row">
                                <span>Subtotal:</span>
                                <span id="cartSubtotal">SSP 0.00</span>
                            </div>
                            <div class="summary-row">
                                <span>Tax (10%):</span>
                                <span id="cartTax">SSP 0.00</span>
                            </div>
                            <div class="summary-row total">
                                <span>Total:</span>
                                <span id="cartTotal">SSP 0.00</span>
                            </div>
                        </div>
                        <button class="checkout-btn" id="checkoutBtn" onclick="proceedToCheckout()" disabled>
                            <i class="fas fa-credit-card"></i>
                            Proceed to Checkout
                        </button>
                    </div>
                </div>
            </div>

            <?php else: ?>
            <!-- ORDER HISTORY SECTION -->
            <div class="orders-section">
                <!-- Order Stats -->
                <div class="order-stats">
                    <div class="stat-card">
                        <i class="fas fa-shopping-bag"></i>
                        <div>
                            <h3><?php echo $stats['total_orders']; ?></h3>
                            <p>Total Orders</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-clock"></i>
                        <div>
                            <h3><?php echo $stats['pending_orders']; ?></h3>
                            <p>Pending</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <h3><?php echo $stats['completed_orders']; ?></h3>
                            <p>Completed</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-times-circle"></i>
                        <div>
                            <h3><?php echo $stats['cancelled_orders']; ?></h3>
                            <p>Cancelled</p>
                        </div>
                    </div>
                </div>

                <!-- Filter Tabs -->
                <div class="order-filters">
                    <a href="?tab=orders&status=all" class="filter-tab <?php echo $status_filter === 'all' ? 'active' : ''; ?>">
                        All Orders
                    </a>
                    <a href="?tab=orders&status=pending" class="filter-tab <?php echo $status_filter === 'pending' ? 'active' : ''; ?>">
                    Pending
                </a>
                <a href="?status=confirmed" class="filter-tab <?php echo $status_filter === 'confirmed' ? 'active' : ''; ?>">
                    Confirmed
                </a>
                <a href="?status=preparing" class="filter-tab <?php echo $status_filter === 'preparing' ? 'active' : ''; ?>">
                    Preparing
                </a>
                <a href="?status=ready" class="filter-tab <?php echo $status_filter === 'ready' ? 'active' : ''; ?>">
                    Ready
                </a>
                <a href="?status=completed" class="filter-tab <?php echo $status_filter === 'completed' ? 'active' : ''; ?>">
                    Completed
                </a>
                <a href="?status=cancelled" class="filter-tab <?php echo $status_filter === 'cancelled' ? 'active' : ''; ?>">
                    Cancelled
                </a>
            </div>

            <!-- Orders List -->
            <div class="orders-list">
                <?php if (count($orders) > 0): ?>
                    <?php foreach ($orders as $order): ?>
                        <div class="order-card">
                            <div class="order-header">
                                <div class="order-info">
                                    <h3>Order #<?php echo $order['id']; ?></h3>
                                    <p class="order-date">
                                        <i class="fas fa-calendar"></i>
                                        <?php echo date('M d, Y - h:i A', strtotime($order['created_at'])); ?>
                                    </p>
                                </div>
                                <div class="order-status">
                                    <span class="status-badge status-<?php echo $order['status']; ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </div>
                            </div>

                            <div class="order-details">
                                <div class="detail-item">
                                    <i class="fas fa-utensils"></i>
                                    <span><?php echo $order['item_count']; ?> items</span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-<?php echo $order['order_type'] === 'delivery' ? 'truck' : 'store'; ?>"></i>
                                    <span><?php echo ucfirst($order['order_type']); ?></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-credit-card"></i>
                                    <span><?php echo ucfirst($order['payment_method']); ?></span>
                                </div>
                                <div class="detail-item total">
                                    <i class="fas fa-money-bill-wave"></i>
                                    <strong><?php echo number_format($order['total_amount']); ?> SSP</strong>
                                </div>
                            </div>

                            <?php if ($order['order_type'] === 'delivery' && $order['delivery_address']): ?>
                                <div class="delivery-info">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?php echo htmlspecialchars($order['delivery_address']); ?></span>
                                </div>
                            <?php endif; ?>

                            <div class="order-actions">
                                <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn-primary">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                                <?php if ($order['status'] === 'pending'): ?>
                                    <button class="btn-danger" onclick="cancelOrder(<?php echo $order['id']; ?>)">
                                        <i class="fas fa-times"></i> Cancel
                                    </button>
                                <?php endif; ?>
                                <?php if ($order['status'] === 'completed'): ?>
                                    <button class="btn-secondary" onclick="reorder(<?php echo $order['id']; ?>)">
                                        <i class="fas fa-redo"></i> Reorder
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-shopping-bag"></i>
                        <h3>No orders found</h3>
                        <p>You haven't placed any orders yet.</p>
                        <a href="../menu.php" class="btn-primary">
                            <i class="fas fa-utensils"></i> Browse Menu
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    
    <!-- Immediate cart loading script -->
    <script>
        // Load cart immediately when page starts loading
        (function() {
            const cart = JSON.parse(localStorage.getItem('quickbite_cart')) || [];
            console.log('Immediate cart check:', cart);
            
            // Update cart counts immediately
            const totalItems = cart.reduce((sum, item) => sum + Number(item.quantity || 0), 0);
            
            // Update all cart count elements
            setTimeout(() => {
                const cartElements = document.querySelectorAll('.cart-count, #cartCount, #mainCartCount');
                cartElements.forEach(el => {
                    if (el) {
                        el.textContent = totalItems;
                        el.style.display = totalItems > 0 ? 'flex' : 'none';
                    }
                });
            }, 100);
        })();
    </script>
    
    <script>
        // Cart Management Functions
        let cart = JSON.parse(localStorage.getItem('quickbite_cart')) || [];

        // Initialize cart display on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM Content Loaded - initializing cart...');
            
            // Force reload cart from localStorage multiple times to ensure it loads
            setTimeout(() => {
                cart = JSON.parse(localStorage.getItem('quickbite_cart')) || [];
                console.log('Cart loaded on page load (attempt 1):', cart);
                updateCartDisplay();
                updateCartCounts();
            }, 100);
            
            setTimeout(() => {
                cart = JSON.parse(localStorage.getItem('quickbite_cart')) || [];
                console.log('Cart loaded on page load (attempt 2):', cart);
                updateCartDisplay();
                updateCartCounts();
            }, 500);
            
            setTimeout(() => {
                cart = JSON.parse(localStorage.getItem('quickbite_cart')) || [];
                console.log('Cart loaded on page load (attempt 3):', cart);
                updateCartDisplay();
                updateCartCounts();
            }, 1000);
            
            // Check if user just logged in after adding items
            const urlParams = new URLSearchParams(window.location.search);
            const loginSuccess = urlParams.get('login_success');
            const pendingRedirect = localStorage.getItem('quickbite_pending_redirect');
            const lastAddedItem = localStorage.getItem('quickbite_last_added_item');
            
            if ((pendingRedirect === 'true' || loginSuccess === '1') && cart.length > 0) {
                console.log('User logged in after adding items, showing notification');
                // Show welcome message with cart info
                setTimeout(() => {
                    showCartWelcomeMessage(lastAddedItem, cart.length);
                }, 1500);
                
                // Clear the redirect flag
                localStorage.removeItem('quickbite_pending_redirect');
                localStorage.removeItem('quickbite_last_added_item');
                
                // Clean up URL
                if (loginSuccess === '1') {
                    const newUrl = window.location.pathname + window.location.search.replace(/[?&]login_success=1/, '');
                    window.history.replaceState({}, '', newUrl);
                }
            }
        });

        function showCartWelcomeMessage(lastItem, totalItems) {
            // Create and show a welcome notification
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 100px;
                right: 20px;
                background: linear-gradient(135deg, #28a745, #20c997);
                color: white;
                padding: 20px;
                border-radius: 12px;
                box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
                z-index: 10000;
                max-width: 350px;
                font-family: Arial, sans-serif;
                animation: slideInRight 0.5s ease-out;
            `;
            
            notification.innerHTML = `
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                    <i class="fas fa-check-circle" style="font-size: 24px;"></i>
                    <strong style="font-size: 16px;">Welcome back!</strong>
                </div>
                <p style="margin: 0; font-size: 14px; line-height: 1.4;">
                    Your cart has <strong>${totalItems} item${totalItems > 1 ? 's' : ''}</strong> ready for checkout.
                    ${lastItem ? `Last added: "${lastItem}"` : ''}
                </p>
            `;
            
            document.body.appendChild(notification);
            
            // Add animation styles
            const style = document.createElement('style');
            style.textContent = `
                @keyframes slideInRight {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
            `;
            document.head.appendChild(style);
            
            // Remove notification after 4 seconds
            setTimeout(() => {
                notification.style.animation = 'slideInRight 0.3s ease-out reverse';
                setTimeout(() => notification.remove(), 300);
            }, 4000);
        }

        function updateDebugInfo() {
            const debugText = document.getElementById('debugText');
            if (debugText) {
                const cartData = localStorage.getItem('quickbite_cart');
                debugText.innerHTML = `
                    Cart items: ${cart.length} | 
                    LocalStorage: ${cartData ? 'Found' : 'Empty'} | 
                    Raw data: ${cartData ? cartData.substring(0, 50) + '...' : 'None'}
                `;
            }
        }

        function updateCartDisplay() {
            const cartItemsList = document.getElementById('cartItemsList');
            const checkoutBtn = document.getElementById('checkoutBtn');
            
            if (!cartItemsList) return; // Only run on cart tab

            console.log('Updating cart display, cart length:', cart.length); // Debug log
            updateDebugInfo(); // Update debug info

            if (cart.length === 0) {
                cartItemsList.innerHTML = `
                    <div class="empty-cart">
                        <i class="fas fa-shopping-cart"></i>
                        <h3>Your cart is empty</h3>
                        <p>Add some delicious items from our menu!</p>
                        <a href="../index.php#menu" class="btn btn-primary">
                            <span>Browse Menu</span>
                        </a>
                    </div>
                `;
                if (checkoutBtn) checkoutBtn.disabled = true;
            } else {
                cartItemsList.innerHTML = cart.map(item => `
                    <div class="cart-item">
                        <img src="${item.image}" alt="${item.name}" onerror="this.src='../assets/images/default-food.jpg'" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                        <div class="item-details">
                            <div class="item-name">${item.name}</div>
                            <div class="item-price">SSP ${Number(item.price).toLocaleString()} each</div>
                        </div>
                        <div class="quantity-controls">
                            <button class="qty-btn" onclick="updateQuantity('${item.name}', -1)">-</button>
                            <span class="qty-display">${item.quantity}</span>
                            <button class="qty-btn" onclick="updateQuantity('${item.name}', 1)">+</button>
                        </div>
                        <div class="quantity-controls">
                            <button class="qty-btn" onclick="updateQuantity('${item.name}', -1)">-</button>
                            <span class="qty-display">${item.quantity}</span>
                            <button class="qty-btn" onclick="updateQuantity('${item.name}', 1)">+</button>
                        </div>
                        <button class="remove-btn" onclick="removeFromCart('${item.name}')">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                    </div>
                `).join('');
                if (checkoutBtn) checkoutBtn.disabled = false;
            }

            updateOrderSummary();
        }

        function updateOrderSummary() {
            const subtotal = cart.reduce((sum, item) => sum + (Number(item.price) * Number(item.quantity)), 0);
            const tax = Math.round(subtotal * 0.1); // 10% tax, rounded
            const total = subtotal + tax;

            const subtotalEl = document.getElementById('cartSubtotal');
            const taxEl = document.getElementById('cartTax');
            const totalEl = document.getElementById('cartTotal');

            if (subtotalEl) subtotalEl.textContent = `SSP ${subtotal.toLocaleString()}`;
            if (taxEl) taxEl.textContent = `SSP ${tax.toLocaleString()}`;
            if (totalEl) totalEl.textContent = `SSP ${total.toLocaleString()}`;

            console.log('Order summary updated:', { subtotal, tax, total }); // Debug log
        }

        function updateCartCounts() {
            const totalItems = cart.reduce((sum, item) => sum + Number(item.quantity), 0);
            const cartCounts = document.querySelectorAll('.cart-count, #cartCount, #mainCartCount');
            cartCounts.forEach(element => {
                if (element) {
                    element.textContent = totalItems;
                    // Show/hide cart count badge
                    if (totalItems > 0) {
                        element.style.display = 'flex';
                        element.style.visibility = 'visible';
                        element.style.opacity = '1';
                    } else {
                        element.style.display = 'none';
                    }
                }
            });
            
            // Also update any cart navigation badges
            const navCartBadges = document.querySelectorAll('nav .cart-count, .nav-link .cart-count');
            navCartBadges.forEach(badge => {
                if (badge) {
                    badge.textContent = totalItems;
                    badge.style.display = totalItems > 0 ? 'flex' : 'none';
                }
            });
            
            console.log('Cart counts updated:', totalItems); // Debug log
        }

        function updateQuantity(itemName, change) {
            const item = cart.find(cartItem => cartItem.name === itemName);
            if (item) {
                item.quantity += change;
                if (item.quantity <= 0) {
                    removeFromCart(itemName);
                } else {
                    localStorage.setItem('quickbite_cart', JSON.stringify(cart));
                    updateCartDisplay();
                    updateCartCounts();
                }
            }
        }

        function removeFromCart(itemName) {
            cart = cart.filter(item => item.name !== itemName);
            localStorage.setItem('quickbite_cart', JSON.stringify(cart));
            updateCartDisplay();
            updateCartCounts();
        }

        function clearCart() {
            if (confirm('Are you sure you want to clear your cart?')) {
                cart = [];
                localStorage.removeItem('quickbite_cart');
                updateCartDisplay();
                updateCartCounts();
            }
        }

        function proceedToCheckout() {
            if (cart.length === 0) {
                alert('Your cart is empty!');
                return;
            }
            
            // Redirect to checkout page
            window.location.href = 'checkout.php';
        }

        // Manual refresh function for testing
        function refreshCart() {
            cart = JSON.parse(localStorage.getItem('quickbite_cart')) || [];
            updateCartDisplay();
            updateCartCounts();
            console.log('Cart manually refreshed:', cart);
            
            // Show success message
            if (cart.length > 0) {
                const notification = document.createElement('div');
                notification.style.cssText = `
                    position: fixed;
                    top: 100px;
                    right: 20px;
                    background: #28a745;
                    color: white;
                    padding: 15px 20px;
                    border-radius: 8px;
                    z-index: 10000;
                    font-size: 14px;
                `;
                notification.innerHTML = `✓ Cart refreshed! Found ${cart.length} item${cart.length > 1 ? 's' : ''}`;
                document.body.appendChild(notification);
                setTimeout(() => notification.remove(), 2000);
            }
        }

        // Debug function to check cart state
        function debugCart() {
            const cartData = localStorage.getItem('quickbite_cart');
            const pendingRedirect = localStorage.getItem('quickbite_pending_redirect');
            const lastAdded = localStorage.getItem('quickbite_last_added_item');
            
            console.log('=== CART DEBUG INFO ===');
            console.log('Raw localStorage data:', cartData);
            console.log('Parsed cart:', cart);
            console.log('Cart length:', cart.length);
            console.log('Pending redirect:', pendingRedirect);
            console.log('Last added item:', lastAdded);
            console.log('Current URL:', window.location.href);
            console.log('========================');
            
            alert(`Cart Debug Info:
            
Items in cart: ${cart.length}
Raw data: ${cartData ? 'Found' : 'Not found'}
Pending redirect: ${pendingRedirect || 'None'}
Last added: ${lastAdded || 'None'}

Check console for detailed info.`);
        }
        
        // Test function to add sample items 
        function addTestItem() {
            const testItem = {
                name: 'Test Kibab Salad',
                price: 5500,
                image: '../assets/images/menu-1.png',
                quantity: 1
            };
            
            // Force reload cart from localStorage first
            cart = JSON.parse(localStorage.getItem('quickbite_cart')) || [];
            
            const existingItemIndex = cart.findIndex(item => item.name === testItem.name);
            if (existingItemIndex !== -1) {
                cart[existingItemIndex].quantity += 1;
            } else {
                cart.push(testItem);
            }
            
            localStorage.setItem('quickbite_cart', JSON.stringify(cart));
            console.log('Test item added, cart now:', cart);
            
            updateCartDisplay();
            updateCartCounts();
            
            alert(`Test item added! Cart now has ${cart.length} items.`);
        }
        
        // Force cart refresh function
        function forceCartRefresh() {
            setTimeout(() => {
                cart = JSON.parse(localStorage.getItem('quickbite_cart')) || [];
                console.log('Force cart refresh triggered:', cart);
                updateCartDisplay();
                updateCartCounts();
            }, 200);
        }

        // Test function to create a sample order
        async function createTestOrder() {
            if (cart.length === 0) {
                alert('Please add items to cart first!');
                return;
            }
            
            try {
                const orderData = {
                    order_type: 'pickup',
                    payment_method: 'cash',
                    delivery_address: '',
                    special_instructions: 'Test order from cart',
                    cart_items: cart
                };
                
                const response = await fetch('../api/place_order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(orderData)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert(`Test order created successfully! Order ID: ${result.order_id}`);
                    // Clear cart after successful order
                    cart = [];
                    localStorage.removeItem('quickbite_cart');
                    updateCartDisplay();
                    updateCartCounts();
                    // Refresh page to show new order in history
                    setTimeout(() => location.reload(), 1000);
                } else {
                    alert('Error creating test order: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to create test order');
            }
        }

        // Make test function available globally
        window.addTestItem = addTestItem;
        window.forceCartRefresh = forceCartRefresh;
        window.createTestOrder = createTestOrder;

        // Add refresh button for testing 
        window.refreshCart = refreshCart;
        window.debugCart = debugCart;
        
        // Force cart update on window focus 
        window.addEventListener('focus', function() {
            setTimeout(() => {
                cart = JSON.parse(localStorage.getItem('quickbite_cart')) || [];
                updateCartDisplay();
                updateCartCounts();
                console.log('Cart updated on window focus:', cart);
            }, 500);
        });

        // Order Management Functions
        async function cancelOrder(orderId) {
            if (!confirm('Are you sure you want to cancel this order?')) {
                return;
            }

            try {
                const response = await fetch('../api/cancel_order.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ order_id: orderId })
                });

                const result = await response.json();

                if (result.success) {
                    alert('Order cancelled successfully');
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to cancel order');
            }
        }

        async function reorder(orderId) {
            if (!confirm('Add all items from this order to your cart?')) {
                return;
            }

            try {
                const response = await fetch('../api/get_order_items.php?order_id=' + orderId);
                const result = await response.json();

                if (result.success) {
                    result.items.forEach(item => {
                        const existingItem = cart.find(cartItem => cartItem.name === item.name);
                        if (existingItem) {
                            existingItem.quantity += item.quantity;
                        } else {
                            cart.push({
                                name: item.name,
                                price: item.price,
                                image: item.image || '../assets/images/default-food.jpg',
                                quantity: item.quantity
                            });
                        }
                    });
                    
                    localStorage.setItem('quickbite_cart', JSON.stringify(cart));
                    alert('Items added to cart!');
                    
                    // Switch to cart tab
                    window.location.href = 'orders.php?tab=cart';
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to reorder');
            }
        }
    </script>

    <!-- Floating Cart Summary -->
    <div class="floating-cart-summary" id="floatingCartSummary" onclick="scrollToCart()">
        <i class="fas fa-shopping-cart"></i>
        <span id="floatingCartText">0 items - SSP 0</span>
    </div>

    <!-- Scroll to Top Button -->
    <div class="scroll-to-top" onclick="scrollToTop()">
        <i class="fas fa-arrow-up"></i>
    </div>

    <script>
        // Update floating cart summary
        function updateFloatingCart() {
            const floatingCart = document.getElementById('floatingCartSummary');
            const floatingText = document.getElementById('floatingCartText');
            
            if (cart.length > 0) {
                const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                floatingText.textContent = `${cart.length} items - SSP ${total.toLocaleString()}`;
                floatingCart.classList.add('show');
            } else {
                floatingCart.classList.remove('show');
            }
        }

        function scrollToCart() {
            document.querySelector('.cart-section').scrollIntoView({ 
                behavior: 'smooth',
                block: 'start'
            });
        }

        function scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Update floating cart when cart changes
        const originalUpdateCartDisplay = updateCartDisplay;
        updateCartDisplay = function() {
            originalUpdateCartDisplay();
            updateFloatingCart();
        };

        // Initialize floating cart on page load
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(updateFloatingCart, 500);
        });
    </script>
</body>
</html>
