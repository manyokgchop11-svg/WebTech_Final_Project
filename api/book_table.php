<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    require_once '../config/database.php';
    $conn = getDBConnection();
    
    // Get form data
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $person = $_POST['person'] ?? '';
    $reservation_date = $_POST['reservation-date'] ?? '';
    $time = $_POST['time'] ?? '';
    $message = $_POST['message'] ?? '';
    
    // Debug: Log received data
    error_log("Reservation data received: " . json_encode($_POST));
    
    // Validate required fields
    if (empty($name) || empty($phone) || empty($person) || empty($reservation_date) || empty($time)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
        exit;
    }
    
    // Convert person to integer
    $number_of_people = intval($person);
    
    // Insert reservation
    $stmt = $conn->prepare("INSERT INTO reservations (name, phone, number_of_people, reservation_date, reservation_time, message, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
    $stmt->bind_param("ssisss", $name, $phone, $number_of_people, $reservation_date, $time, $message);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Reservation request submitted successfully! We will contact you soon.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to submit reservation. Please try again.']);
    }
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    // Log the actual error for debugging (remove in production)
    error_log("Reservation API Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>