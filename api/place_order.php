<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Please login to place an order']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

try {
    $conn = getDBConnection();
    $user_id = $_SESSION['user_id'];
    
    // Get POST data
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid JSON data');
    }
    
    $order_type = $input['order_type'] ?? 'pickup';
    $payment_method = $input['payment_method'] ?? 'cash';
    $delivery_address = $input['delivery_address'] ?? '';
    $special_instructions = $input['special_instructions'] ?? '';
    $cart_items = $input['cart_items'] ?? [];
    
    if (empty($cart_items)) {
        throw new Exception('Cart is empty');
    }
    
    // Start transaction
    $conn->begin_transaction();
    
    // Calculate totals
    $subtotal = 0;
    foreach ($cart_items as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }
    
    $tax_rate = 0.10; // 10% tax
    $tax_amount = $subtotal * $tax_rate;
    $total_amount = $subtotal + $tax_amount;
    
    // Generate order number
    $order_number = 'QB' . date('Ymd') . sprintf('%04d', rand(1, 9999));
    
    // Insert order
    $order_query = "INSERT INTO orders (user_id, order_number, order_type, status, total_amount, delivery_address, special_instructions, payment_method, created_at) VALUES (?, ?, ?, 'pending', ?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($order_query);
    $stmt->bind_param("issdsss", $user_id, $order_number, $order_type, $total_amount, $delivery_address, $special_instructions, $payment_method);
    $stmt->execute();
    
    $order_id = $conn->insert_id;
    
    // Insert order items
    $item_query = "INSERT INTO order_items (order_id, menu_item_id, quantity, unit_price, subtotal, special_requests) VALUES (?, ?, ?, ?, ?, ?)";
    $item_stmt = $conn->prepare($item_query);
    
    foreach ($cart_items as $item) {
        // For now, we'll use 0 as menu_item_id since these are from the static menu
        // In a full implementation, you'd match these to actual menu_item records
        $menu_item_id = 0;
        $quantity = $item['quantity'];
        $unit_price = $item['price'];
        $item_subtotal = $unit_price * $quantity;
        $special_requests = $item['special_requests'] ?? '';
        
        $item_stmt->bind_param("iiidds", $order_id, $menu_item_id, $quantity, $unit_price, $item_subtotal, $special_requests);
        $item_stmt->execute();
    }
    
    // Commit transaction
    $conn->commit();
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Order placed successfully!',
        'order_id' => $order_id,
        'order_number' => $order_number,
        'total_amount' => $total_amount
    ]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($conn)) {
        $conn->rollback();
    }
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to place order: ' . $e->getMessage()
    ]);
}

if (isset($conn)) {
    $conn->close();
}
?>