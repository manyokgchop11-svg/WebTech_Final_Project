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
    $table_number = trim($_POST['table_number']);
    $capacity = intval($_POST['capacity']);
    $location = $_POST['location'];
    $status = $_POST['status'];
    
    // Validate inputs
    if (empty($table_number) || $capacity <= 0 || empty($location) || empty($status)) {
        throw new Exception('Please fill in all required fields with valid values');
    }
    
    // Check if table number already exists
    $check_query = "SELECT id FROM tables WHERE table_number = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("s", $table_number);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        throw new Exception('A table with this number already exists');
    }
    
    // Add new table
    $query = "INSERT INTO tables (table_number, capacity, location, status, created_at) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("siss", $table_number, $capacity, $location, $status);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Table added successfully']);
    } else {
        throw new Exception('Failed to add table');
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>