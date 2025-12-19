<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Get email from form data or JSON
$email = '';
if (isset($_POST['email_address'])) {
    $email = $_POST['email_address'];
} else {
    $data = json_decode(file_get_contents('php://input'), true);
    $email = $data['email'] ?? '';
}

if (empty($email)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email is required']);
    exit();
}

$email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit();
}

try {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("INSERT INTO newsletter_subscribers (email) VALUES (?)");
    $stmt->bind_param("s", $email);
    
    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(['success' => true, 'message' => 'Successfully subscribed! You will receive our latest updates and 25% off your next order.']);
    } else {
        if ($conn->errno === 1062) {
            http_response_code(409);
            echo json_encode(['success' => false, 'message' => 'Email already subscribed']);
        } else {
            throw new Exception('Failed to subscribe');
        }
    }
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
