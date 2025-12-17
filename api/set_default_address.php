<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

$conn = getDBConnection();
$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);
$address_id = $data['address_id'] ?? 0;

// Unset all defaults
$unset_query = "UPDATE customer_addresses SET is_default = 0 WHERE user_id = ?";
$stmt = $conn->prepare($unset_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();

// Set new default
$set_query = "UPDATE customer_addresses SET is_default = 1 WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($set_query);
$stmt->bind_param("ii", $address_id, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Default address updated']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update default address']);
}

$conn->close();
?>
