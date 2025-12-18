<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit();
}

$conn = getDBConnection();
$user_id = $_SESSION['user_id'];
$address_id = $_GET['id'] ?? 0;

$query = "SELECT * FROM customer_addresses WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $address_id, $user_id);
$stmt->execute();
$address = $stmt->get_result()->fetch_assoc();

if ($address) {
    echo json_encode(['success' => true, 'address' => $address]);
} else {
    echo json_encode(['success' => false, 'message' => 'Address not found']);
}

$conn->close();
?>
