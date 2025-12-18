<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$conn = getDBConnection();

try {
    $item_id = intval($_POST['item_id']);
    $name = trim($_POST['name']);
    $category = $_POST['category'];
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']);
    $is_available = intval($_POST['is_available']);
    
    // Validate inputs
    if (empty($name) || empty($category) || $price <= 0) {
        throw new Exception('Please fill in all required fields with valid values');
    }
    
    // Update menu item
    $query = "UPDATE menu_items SET name = ?, category = ?, price = ?, description = ?, is_available = ?, updated_at = NOW() WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssdsii", $name, $category, $price, $description, $is_available, $item_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Menu item updated successfully']);
    } else {
        throw new Exception('Failed to update menu item');
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>