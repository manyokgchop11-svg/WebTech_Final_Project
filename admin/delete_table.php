<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login_fixed.php');
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: tables.php?error=Invalid table ID');
    exit();
}

$conn = getDBConnection();

try {
    $table_id = intval($_GET['id']);
    
    // Check if table exists and get table number for confirmation
    $check_query = "SELECT table_number FROM tables WHERE id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("i", $table_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Table not found');
    }
    
    $table = $result->fetch_assoc();
    
    // Check if table is used in any reservations
    $reservation_check = "SELECT COUNT(*) as count FROM reservations WHERE table_id = ?";
    $reservation_stmt = $conn->prepare($reservation_check);
    $reservation_stmt->bind_param("i", $table_id);
    $reservation_stmt->execute();
    $reservation_result = $reservation_stmt->get_result();
    $reservation_count = $reservation_result->fetch_assoc()['count'];
    
    if ($reservation_count > 0) {
        // Don't delete, just mark as maintenance
        $update_query = "UPDATE tables SET status = 'maintenance' WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("i", $table_id);
        
        if ($update_stmt->execute()) {
            header('Location: tables.php?success=Table "' . urlencode($table['table_number']) . '" has been marked as maintenance (cannot delete due to existing reservations)');
        } else {
            throw new Exception('Failed to update table status');
        }
    } else {
        // Safe to delete
        $delete_query = "DELETE FROM tables WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bind_param("i", $table_id);
        
        if ($delete_stmt->execute()) {
            header('Location: tables.php?success=Table "' . urlencode($table['table_number']) . '" has been deleted successfully');
        } else {
            throw new Exception('Failed to delete table');
        }
    }
    
} catch (Exception $e) {
    header('Location: tables.php?error=' . urlencode($e->getMessage()));
}

$conn->close();
?>