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
    $name = trim($_POST['name']);
    $category = $_POST['category'];
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']);
    $is_available = intval($_POST['is_available']);
    
    // Validate inputs
    if (empty($name) || empty($category) || $price <= 0) {
        throw new Exception('Please fill in all required fields with valid values');
    }
    
    // Check if item already exists
    $check_query = "SELECT id FROM menu_items WHERE name = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("s", $name);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        throw new Exception('A menu item with this name already exists');
    }
    
    // Add new menu item
    $query = "INSERT INTO menu_items (name, category, price, description, is_available, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssdsi", $name, $category, $price, $description, $is_available);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Menu item added successfully']);
    } else {
        throw new Exception('Failed to add menu item');
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>