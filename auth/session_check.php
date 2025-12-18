<?php
// Session timeout management
function checkSessionTimeout() {
    if (!isset($_SESSION['user_id'])) {
        return false; // Not logged in
    }
    
    $timeout_duration = 1800; // 30 minutes in seconds
    
    // Check if session has expired
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
        // Session expired
        session_unset();
        session_destroy();
        return false;
    }
    
    // Update last activity time
    $_SESSION['last_activity'] = time();
    return true;
}

// Auto-logout if session expired
if (session_status() === PHP_SESSION_ACTIVE) {
    if (!checkSessionTimeout() && isset($_SESSION['user_id'])) {
        header('Location: ../auth/login_fixed.php?expired=1');
        exit();
    }
}
?>