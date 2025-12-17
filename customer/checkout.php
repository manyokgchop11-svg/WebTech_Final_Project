<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: ../auth/login_fixed.php');
    exit();
}

$conn = getDBConnection();
$user_id = $_SESSION['user_id'];

// Get user info
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$success_message = '';
$error_message = '';

// Handle order submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_type = $_POST['order_type'];
    $payment_method = $_POST['payment_method'];
    $delivery_address = $_POST['delivery_address'] ?? '';
    $special_instructions = $_POST['special_instructions'] ?? '';
    $cart_data = json_decode($_POST['cart_data'], true);
    
    if (empty($cart_data)) {
        $error_message = 'Your cart is empty!';
    } else {
        try {
            $conn->begin_transaction();
            
            // Calculate totals
            $subtotal = 0;
            foreach ($cart_data as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }
            $tax = $subtotal * 0.1;
            $total = $subtotal + $tax;
            
            // Insert order
            $order_query = "INSERT INTO orders (user_id, order_type, payment_method, delivery_address, special_instructions, subtotal, tax_amount, total_amount, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";
            $stmt = $conn->prepare($order_query);
            $stmt->bind_param("issssdds", $user_id, $order_type, $payment_method, $delivery_address, $special_instructions, $subtotal, $tax, $total);
            $stmt->execute();
            
            $order_id = $conn->insert_id;
            
            // Insert order items
            $item_query = "INSERT INTO order_items (order_id, menu_item_id, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($item_query);
            
            foreach ($cart_data as $item) {
                $item_subtotal = $item['price'] * $item['quantity'];
                $stmt->bind_param("iiidd", $order_id, $item['id'], $item['quantity'], $item['price'], $item_subtotal);
                $stmt->execute();
            }
            
            $conn->commit();
            $success_message = "Order placed successfully! Order ID: #$order_id";
            
        } catch (Exception $e) {
            $conn->rollback();
            $error_message = 'Failed to place order. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - QuickBite</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/customer.css">
    <style>
        .checkout-page {
            background: var(--smoky-black-1);
            min-height: 100vh;
            padding-top: 100px;
        }
        
        .checkout-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 30px;
        }
        
        .page-title {
            color: var(--gold-crayola);
            font-size: 3rem;
            margin-bottom: 40px;
            text-align: center;
        }
        
        .checkout-layout {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 40px;
        }
        
        .checkout-form {
            background: var(--eerie-black-2);
            border: 1px solid var(--white-alpha-10);
            border-radius: 20px;
            padding: 30px;
        }
        
        .form-section {
            margin-bottom: 35px;
        }
        
        .form-section h2 {
            color: var(--gold-crayola);
            margin-bottom: 20px;
            font-size: 2rem;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--gold-crayola);
            font-weight: bold;
            font-size: 1.4rem;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            background: var(--smoky-black-1);
            border: 1px solid var(--white-alpha-10);
            border-radius: 8px;
            color: var(--white);
            font-size: 1.6rem;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--gold-crayola);
        }
        
        .radio-group {
            display: flex;
            gap: 25px;
            margin-top: 10px;
        }
        
        .radio-option {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .radio-option input[type="radio"] {
            width: auto;
            margin: 0;
        }
        
        .radio-option label {
            margin: 0;
            color: var(--white);
            font-size: 1.6rem;
        }
        
        .order-summary {
            background: var(--eerie-black-2);
            border: 1px solid var(--white-alpha-10);
            border-radius: 20px;
            padding: 30px;
            height: fit-content;
        }
        
        .order-summary h2 {
            color: var(--gold-crayola);
            margin-bottom: 25px;
            font-size: 2.4rem;
        }
        
        .order-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--white-alpha-10);
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .item-details {
            flex: 1;
        }
        
        .item-name {
            color: var(--gold-crayola);
            font-weight: bold;
            font-size: 1.6rem;
        }
        
        .item-quantity {
            color: var(--quick-silver);
            font-size: 1.3rem;
        }
        
        .item-price {
            color: var(--white);
            font-weight: bold;
            font-size: 1.6rem;
        }
        
        .summary-totals {
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid var(--white-alpha-10);
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            color: var(--quick-silver);
            font-size: 1.6rem;
        }
        
        .summary-row.total {
            font-size: 2rem;
            font-weight: bold;
            color: var(--gold-crayola);
            border-top: 1px solid var(--white-alpha-10);
            padding-top: 15px;
            margin-top: 20px;
        }
        
        .place-order-btn {
            width: 100%;
            background: var(--gold-crayola);
            color: var(--smoky-black-1);
            border: none;
            padding: 18px;
            border-radius: 12px;
            font-size: 1.8rem;
            font-weight: bold;
            cursor: pointer;
            margin-top: 25px;
            transition: all 0.3s ease;
        }
        
        .place-order-btn:hover {
            background: var(--white);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }
        
        .alert {
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-size: 1.6rem;
        }
        
        .alert-success {
            background: rgba(39, 174, 96, 0.2);
            border: 1px solid #27ae60;
            color: #27ae60;
        }
        
        .alert-error {
            background: rgba(231, 76, 60, 0.2);
            border: 1px solid #e74c3c;
            color: #e74c3c;
        }
        
        .alert a {
            color: inherit;
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .checkout-layout {
                grid-template-columns: 1fr;
            }
            
            .checkout-container {
                padding: 20px 15px;
            }
            
            .radio-group {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>
<body class="customer-page">
    <?php include 'includes/header_new.php'; ?>

    <main class="checkout-page">
        <div class="checkout-container">
            <h1 class="page-title">Checkout</h1>

            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <?php echo $success_message; ?>
                    <br><br>
                    <a href="orders.php">View My Orders</a> | 
                    <a href="dashboard.php">Back to Dashboard</a>
                </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="alert alert-error"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <div class="checkout-layout">
                <div class="checkout-form">
                    <form method="POST" id="checkoutForm">
                        <input type="hidden" name="cart_data" id="cartData">
                        
                        <div class="form-section">
                            <h2>Order Type</h2>
                            <div class="radio-group">
                                <div class="radio-option">
                                    <input type="radio" name="order_type" value="pickup" id="pickup" checked onchange="toggleDeliveryAddress()">
                                    <label for="pickup">Pickup</label>
                                </div>
                                <div class="radio-option">
                                    <input type="radio" name="order_type" value="delivery" id="delivery" onchange="toggleDeliveryAddress()">
                                    <label for="delivery">Delivery</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-section" id="deliverySection" style="display: none;">
                            <h2>Delivery Address</h2>
                            <div class="form-group">
                                <label for="delivery_address">Address</label>
                                <textarea name="delivery_address" id="delivery_address" rows="3" placeholder="Enter your delivery address"></textarea>
                            </div>
                        </div>

                        <div class="form-section">
                            <h2>Payment Method</h2>
                            <div class="form-group">
                                <select name="payment_method" required>
                                    <option value="">Select Payment Method</option>
                                    <option value="cash">Cash</option>
                                    <option value="card">Credit/Debit Card</option>
                                    <option value="mobile">Mobile Payment</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-section">
                            <h2>Special Instructions</h2>
                            <div class="form-group">
                                <textarea name="special_instructions" rows="3" placeholder="Any special requests or instructions..."></textarea>
                            </div>
                        </div>

                        <button type="submit" class="place-order-btn">Place Order</button>
                    </form>
                </div>

                <div class="order-summary">
                    <h2>Order Summary</h2>
                    <div id="orderItems">
                        <!-- Order items will be loaded here -->
                    </div>
                    
                    <div class="summary-totals">
                        <div class="summary-row">
                            <span>Subtotal:</span>
                            <span id="subtotal">0 SSP</span>
                        </div>
                        <div class="summary-row">
                            <span>Tax (10%):</span>
                            <span id="tax">0 SSP</span>
                        </div>
                        <div class="summary-row total">
                            <span>Total:</span>
                            <span id="total">0 SSP</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    
    <script>
        function toggleDeliveryAddress() {
            const deliverySection = document.getElementById('deliverySection');
            const deliveryRadio = document.getElementById('delivery');
            
            if (deliveryRadio.checked) {
                deliverySection.style.display = 'block';
                document.getElementById('delivery_address').required = true;
            } else {
                deliverySection.style.display = 'none';
                document.getElementById('delivery_address').required = false;
            }
        }
        
        function loadOrderSummary() {
            const cart = JSON.parse(localStorage.getItem('checkoutCart')) || [];
            
            if (cart.length === 0) {
                alert('No items in cart!');
                window.location.href = 'cart.php';
                return;
            }
            
            // Set cart data for form submission
            document.getElementById('cartData').value = JSON.stringify(cart);
            
            let html = '';
            let subtotal = 0;
            
            cart.forEach(item => {
                const itemTotal = item.price * item.quantity;
                subtotal += itemTotal;
                
                html += `
                    <div class="order-item">
                        <div class="item-details">
                            <div class="item-name">${item.name}</div>
                            <div class="item-quantity">Qty: ${item.quantity} × ${item.price.toFixed(0)} SSP</div>
                        </div>
                        <div class="item-price">${itemTotal.toFixed(0)} SSP</div>
                    </div>
                `;
            });
            
            document.getElementById('orderItems').innerHTML = html;
            
            const tax = subtotal * 0.1;
            const total = subtotal + tax;
            
            document.getElementById('subtotal').textContent = subtotal.toFixed(0) + ' SSP';
            document.getElementById('tax').textContent = tax.toFixed(0) + ' SSP';
            document.getElementById('total').textContent = total.toFixed(0) + ' SSP';
        }
        
        // Load order summary on page load
        loadOrderSummary();
        
        // Clear cart after successful order
        <?php if ($success_message): ?>
            localStorage.removeItem('cart');
            localStorage.removeItem('checkoutCart');
        <?php endif; ?>
    </script>
</body>
</html>