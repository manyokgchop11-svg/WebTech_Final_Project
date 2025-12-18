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
$order_id = $_GET['id'] ?? 0;

// Get order details
$order_query = "SELECT o.* FROM orders o WHERE o.id = ? AND o.user_id = ?";
$stmt = $conn->prepare($order_query);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    header('Location: orders.php');
    exit();
}

// Get order items
$items_query = "SELECT oi.*, mi.name, mi.image, mi.category 
                FROM order_items oi 
                JOIN menu_items mi ON oi.menu_item_id = mi.id 
                WHERE oi.order_id = ?";
$stmt = $conn->prepare($items_query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #<?php echo $order_id; ?> - QuickBite</title>
    <link rel="stylesheet" href="../assets/css/customer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header_new.php'; ?>

    <div class="customer-container">
        <div class="order-details-wrapper">
            <div class="order-details-header">
                <div>
                    <h1><i class="fas fa-receipt"></i> Order #<?php echo $order_id; ?></h1>
                    <p class="order-date">
                        Placed on <?php echo date('F d, Y - h:i A', strtotime($order['created_at'])); ?>
                    </p>
                </div>
                <span class="status-badge status-<?php echo $order['status']; ?>">
                    <?php echo ucfirst($order['status']); ?>
                </span>
            </div>

            <div class="order-details-grid">
                <!-- Order Items -->
                <div class="order-section">
                    <h2><i class="fas fa-utensils"></i> Order Items</h2>
                    <div class="order-items-list">
                        <?php foreach ($items as $item): ?>
                            <div class="order-item-card">
                                <img src="../assets/images/<?php echo htmlspecialchars($item['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>">
                                <div class="item-info">
                                    <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                    <p class="item-category"><?php echo htmlspecialchars($item['category']); ?></p>
                                    <p class="item-quantity">Quantity: <?php echo $item['quantity']; ?></p>
                                </div>
                                <div class="item-price">
                                    <p class="unit-price"><?php echo number_format($item['price']); ?> SSP each</p>
                                    <p class="total-price"><?php echo number_format($item['price'] * $item['quantity']); ?> SSP</p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Order Information -->
                <div class="order-section">
                    <h2><i class="fas fa-info-circle"></i> Order Information</h2>
                    
                    <div class="info-group">
                        <label><i class="fas fa-<?php echo $order['order_type'] === 'delivery' ? 'truck' : 'store'; ?>"></i> Order Type</label>
                        <p><?php echo ucfirst($order['order_type']); ?></p>
                    </div>

                    <div class="info-group">
                        <label><i class="fas fa-credit-card"></i> Payment Method</label>
                        <p><?php echo ucfirst($order['payment_method']); ?></p>
                    </div>

                    <?php if ($order['order_type'] === 'delivery' && $order['delivery_address']): ?>
                        <div class="info-group">
                            <label><i class="fas fa-map-marker-alt"></i> Delivery Address</label>
                            <p><?php echo htmlspecialchars($order['delivery_address']); ?></p>
                        </div>

                        <div class="info-group">
                            <label><i class="fas fa-phone"></i> Contact Phone</label>
                            <p><?php echo htmlspecialchars($order['delivery_phone']); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if ($order['special_instructions']): ?>
                        <div class="info-group">
                            <label><i class="fas fa-comment"></i> Special Instructions</label>
                            <p><?php echo htmlspecialchars($order['special_instructions']); ?></p>
                        </div>
                    <?php endif; ?>

                    <!-- Order Summary -->
                    <div class="order-summary-box">
                        <h3>Order Summary</h3>
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span><?php echo number_format($order['total_amount'] - $order['delivery_fee']); ?> SSP</span>
                        </div>
                        <div class="summary-row">
                            <span>Delivery Fee</span>
                            <span><?php echo number_format($order['delivery_fee']); ?> SSP</span>
                        </div>
                        <div class="summary-row total">
                            <span>Total</span>
                            <strong><?php echo number_format($order['total_amount']); ?> SSP</strong>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="order-actions">
                        <a href="orders.php" class="btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Orders
                        </a>
                        <?php if ($order['status'] === 'pending'): ?>
                            <button class="btn-danger" onclick="cancelOrder(<?php echo $order_id; ?>)">
                                <i class="fas fa-times"></i> Cancel Order
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
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
    </script>
</body>
</html>
