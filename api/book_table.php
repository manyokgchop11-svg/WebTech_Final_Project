<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

// Validate required fields
$required_fields = ['name', 'phone', 'number_of_people', 'reservation_date', 'reservation_time'];
$errors = [];

foreach ($required_fields as $field) {
    if (empty($data[$field])) {
        $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
    }
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Validation failed', 'errors' => $errors]);
    exit();
}

// Sanitize inputs
$name = htmlspecialchars(trim($data['name']));
$phone = htmlspecialchars(trim($data['phone']));
$email = isset($data['email']) ? filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL) : null;
$number_of_people = intval($data['number_of_people']);
$reservation_date = $data['reservation_date'];
$reservation_time = $data['reservation_time'];
$message = isset($data['message']) ? htmlspecialchars(trim($data['message'])) : null;

// Validate email if provided
if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit();
}

// Validate date (must be today or future)
$today = date('Y-m-d');
if ($reservation_date < $today) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Reservation date must be today or in the future']);
    exit();
}

// Insert into database
try {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("INSERT INTO reservations (name, phone, email, number_of_people, reservation_date, reservation_time, message) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssisss", $name, $phone, $email, $number_of_people, $reservation_date, $reservation_time, $message);
    
    if ($stmt->execute()) {
        $reservation_id = $stmt->insert_id;
        
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Reservation successfully created!',
            'reservation_id' => $reservation_id,
            'data' => [
                'name' => $name,
                'date' => $reservation_date,
                'time' => $reservation_time,
                'people' => $number_of_people
            ]
        ]);
    } else {
        throw new Exception('Failed to create reservation');
    }
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
