<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login_fixed.php');
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: menu.php?error=Invalid item ID');
    exit();
}

$conn = getDBConnection();

try {
    $item_id = intval($_GET['id']);
    
    // Check if item exists and get name for confirmation
    $check_query = "SELECT name FROM menu_items WHERE id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("i", $item_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Menu item not found');
    }
    
    $item = $result->fetch_assoc();
    
    // Check if item is used in any orders
    $order_check = "SELECT COUNT(*) as count FROM order_items WHERE menu_item_id = ?";
    $order_stmt = $conn->prepare($order_check);
    $order_stmt->bind_param("i", $item_id);
    $order_stmt->execute();
    $order_result = $order_stmt->get_result();
    $order_count = $order_result->fetch_assoc()['count'];
    
    if ($order_count > 0) {
        // Don't delete, just mark as unavailable
        $update_query = "UPDATE menu_items SET is_available = 0, updated_at = NOW() WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("i", $item_id);
        
        if ($update_stmt->execute()) {
            header('Location: menu.php?success=Menu item "' . urlencode($item['name']) . '" has been marked as unavailable (cannot delete due to existing orders)');
        } else {
            throw new Exception('Failed to update menu item');
        }
    } else {
        // Safe to delete
        $delete_query = "DELETE FROM menu_items WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bind_param("i", $item_id);
        
        if ($delete_stmt->execute()) {
            header('Location: menu.php?success=Menu item "' . urlencode($item['name']) . '" has been deleted successfully');
        } else {
            throw new Exception('Failed to delete menu item');
        }
    }
    
} catch (Exception $e) {
    header('Location: menu.php?error=' . urlencode($e->getMessage()));
}

$conn->close();
?>