<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once '../config/database.php';

// If already logged in, redirect to appropriate dashboard
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: ../admin/dashboard.php');
    } else {
        header('Location: ../customer/dashboard.php');
    }
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password';
    } else {
        try {
            $conn = getDBConnection();
            
            $stmt = $conn->prepare("SELECT id, username, email, password, full_name, role, status FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                
                if ($user['status'] !== 'active') {
                    $error = 'Your account has been suspended. Please contact support.';
                } elseif (password_verify($password, $user['password'])) {
                    // Update last login
                    $update_stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                    $update_stmt->bind_param("i", $user['id']);
                    $update_stmt->execute();
                    
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['full_name'] = $user['full_name'];
                    $_SESSION['role'] = $user['role'];
                    
                    // Redirect based on role
                    if ($user['role'] === 'admin') {
                        header('Location: ../admin/dashboard.php');
                    } else {
                        header('Location: ../customer/dashboard.php');
                    }
                    exit();
                } else {
                    $error = 'Invalid email or password';
                }
            } else {
                $error = 'Invalid email or password';
            }
            
            $stmt->close();
            $conn->close();
        } catch (Exception $e) {
            $error = 'Login failed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - QuickBite</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <img src="../assets/images/logo1.png" alt="QuickBite Logo" class="auth-logo">
                <h1 class="headline-2">Welcome Back</h1>
                <p class="body-2">Sign in to your account</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <ion-icon name="alert-circle-outline"></ion-icon>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="email" class="label-2">Email Address</label>
                    <input type="email" id="email" name="email" class="input-field" required 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="password" class="label-2">Password</label>
                    <input type="password" id="password" name="password" class="input-field" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <span class="text text-1">Sign In</span>
                    <span class="text text-2" aria-hidden="true">Sign In</span>
                </button>
            </form>

            <div class="auth-footer">
                <p class="body-4">Don't have an account? <a href="register.php" class="link">Sign Up</a></p>
                <p class="body-4"><a href="../index.php" class="link">← Back to Website</a></p>
            </div>

            <div class="demo-credentials">
                <p class="label-2">Demo Credentials:</p>
                <p class="body-4"><strong>Admin:</strong> admin@quickbite.com / admin123</p>
            </div>
        </div>
    </div>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>
