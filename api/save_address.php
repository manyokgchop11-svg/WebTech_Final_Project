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

$address_id = $data['id'] ?? null;
$label = $data['label'] ?? '';
$address_line = $data['address_line'] ?? '';
$city = $data['city'] ?? '';
$state = $data['state'] ?? '';
$phone = $data['phone'] ?? '';
$is_default = $data['is_default'] ?? 0;

// Validate
if (empty($label) || empty($address_line) || empty($city) || empty($phone)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit();
}

// If setting as default, unset other defaults
if ($is_default) {
    $unset_query = "UPDATE customer_addresses SET is_default = 0 WHERE user_id = ?";
    $stmt = $conn->prepare($unset_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
}

if ($address_id) {
    // Update existing address
    $query = "UPDATE customer_addresses SET label = ?, address_line = ?, city = ?, state = ?, phone = ?, is_default = ? 
              WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssiii", $label, $address_line, $city, $state, $phone, $is_default, $address_id, $user_id);
} else {
    // Insert new address
    $query = "INSERT INTO customer_addresses (user_id, label, address_line, city, state, phone, is_default) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isssssi", $user_id, $label, $address_line, $city, $state, $phone, $is_default);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Address saved successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save address']);
}

$conn->close();
?>
