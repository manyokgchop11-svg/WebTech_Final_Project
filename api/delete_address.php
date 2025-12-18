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

$query = "DELETE FROM customer_addresses WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $address_id, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Address deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete address']);
}

$conn->close();
?>
