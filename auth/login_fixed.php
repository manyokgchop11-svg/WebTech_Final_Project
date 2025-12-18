<?php
session_start();
require_once '../config/database.php';

// If already logged in, check role compatibility
if (isset($_SESSION['user_id'])) {
    $redirect = $_GET['redirect'] ?? '';
    $required_role = $_GET['role'] ?? '';
    
    // If redirect requires specific role and user has different role, logout first
    if (!empty($required_role) && $_SESSION['role'] !== $required_role) {
        session_destroy();
        session_start();
        $error = 'Please login with a ' . ucfirst($required_role) . ' account to access this feature.';
    } else {
        // Redirect based on parameters or role
        if (!empty($redirect)) {
            header('Location: ../' . $redirect);
        } elseif ($_SESSION['role'] === 'admin') {
            header('Location: ../admin/dashboard.php');
        } else {
            header('Location: ../customer/dashboard.php');
        }
        exit();
    }
}

$error = '';

// Check for session expiration
if (isset($_GET['expired']) && $_GET['expired'] == '1') {
    $error = 'Your session has expired for security reasons. Please login again.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $selected_role = $_POST['role'] ?? '';
    
    if (empty($email) || empty($password) || empty($selected_role)) {
        $error = 'Please fill in all fields including role selection';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long';
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
                } elseif ($user['role'] !== $selected_role) {
                    $error = 'Invalid role selection. Please select the correct role for your account.';
                } elseif (password_verify($password, $user['password'])) {
                    // Set session variables with timeout
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['full_name'] = $user['full_name'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['login_time'] = time();
                    $_SESSION['last_activity'] = time();
                    
                    // Redirect based on role and redirect parameter
                    $redirect = $_GET['redirect'] ?? '';
                    
                    if (!empty($redirect)) {
                        // Check if redirect is customer-specific and user is customer
                        if (strpos($redirect, 'customer/') === 0 && $user['role'] === 'customer') {
                            // Add a flag to indicate successful login with cart items
                            $redirect_url = '../' . $redirect;
                            if (strpos($redirect, '?') !== false) {
                                $redirect_url .= '&login_success=1';
                            } else {
                                $redirect_url .= '?login_success=1';
                            }
                            header('Location: ' . $redirect_url);
                        } elseif (strpos($redirect, 'admin/') === 0 && $user['role'] === 'admin') {
                            header('Location: ../' . $redirect);
                        } else {
                            // Redirect to appropriate dashboard if redirect doesn't match role
                            if ($user['role'] === 'admin') {
                                header('Location: ../admin/dashboard.php');
                            } else {
                                header('Location: ../customer/dashboard.php?login_success=1');
                            }
                        }
                    } else {
                        // No redirect specified,
                        if ($user['role'] === 'admin') {
                            header('Location: ../admin/dashboard.php');
                        } else {
                            header('Location: ../customer/dashboard.php?login_success=1');
                        }
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        .login-container {
            background: #333;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            width: 100%;
            max-width: 400px;
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo img {
            max-width: 120px;
            border-radius: 8px;
        }
        h1 {
            color: #ffd700;
            text-align: center;
            margin-bottom: 10px;
        }
        .subtitle {
            text-align: center;
            color: #ccc;
            margin-bottom: 30px;
        }
        .error {
            background: rgba(231, 76, 60, 0.1);
            border: 1px solid #e74c3c;
            color: #e74c3c;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: white;
            font-weight: bold;
        }
        input {
            width: 100%;
            padding: 12px;
            background: #222;
            border: 1px solid #555;
            border-radius: 6px;
            color: white;
            font-size: 16px;
        }
        input:focus {
            outline: none;
            border-color: #ffd700;
        }
        .btn {
            width: 100%;
            padding: 12px;
            background: #ffd700;
            color: #000;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #ffed4e;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #555;
        }
        .footer a {
            color: #ffd700;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }

    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <img src="../assets/images/logo1.png" alt="QuickBite Logo">
        </div>
        
        <h1>Welcome Back</h1>
        <p class="subtitle">Sign in to your account</p>

        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="role">Login As</label>
                <select id="role" name="role" required style="width: 100%; padding: 12px; background: #222; border: 1px solid #555; border-radius: 6px; color: white; font-size: 16px;">
                    <option value="">Select Role</option>
                    <option value="admin" <?php echo (isset($_POST['role']) && $_POST['role'] === 'admin') ? 'selected' : ''; ?>>Administrator</option>
                    <option value="customer" <?php echo (isset($_POST['role']) && $_POST['role'] === 'customer') || (isset($_GET['preselect']) && $_GET['preselect'] === 'customer') ? 'selected' : ''; ?>>Customer</option>
                </select>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required 
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-input-container" style="position: relative;">
                    <input type="password" id="password" name="password" required minlength="8" 
                           style="padding-right: 45px;">
                    <button type="button" class="password-toggle" onclick="togglePassword()" 
                            style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); 
                                   background: none; border: none; color: #ffd700; cursor: pointer; 
                                   font-size: 18px; padding: 0; width: 24px; height: 24px;">
                        <i class="fas fa-eye" id="passwordToggleIcon"></i>
                    </button>
                </div>
                <small style="color: #ccc; font-size: 12px; margin-top: 5px; display: block;">
                    Password must be at least 8 characters long
                </small>
            </div>

            <button type="submit" class="btn">Sign In</button>
        </form>

        <div class="footer">
            <p>Don't have an account? <a href="register.php">Sign Up</a></p>
            <p><a href="#" onclick="showForgotPassword()">Forgot Password?</a></p>
            <p><a href="../index.html">← Back to Website</a></p>
        </div>

        <!-- Forgot Password Modal -->
        <div id="forgotPasswordModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
            <div class="modal-content" style="background: #333; margin: 10% auto; padding: 30px; border-radius: 12px; width: 90%; max-width: 400px; border: 1px solid #555;">
                <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="color: #ffd700; margin: 0;">Reset Password</h3>
                    <span class="close" onclick="closeForgotPassword()" style="color: #ccc; font-size: 24px; cursor: pointer;">&times;</span>
                </div>
                <form id="forgotPasswordForm" onsubmit="handleForgotPassword(event)">
                    <div class="form-group">
                        <label for="resetEmail">Email Address</label>
                        <input type="email" id="resetEmail" name="resetEmail" required 
                               style="width: 100%; padding: 12px; background: #222; border: 1px solid #555; border-radius: 6px; color: white; font-size: 16px;">
                    </div>
                    <button type="submit" class="btn" style="width: 100%; margin-top: 15px;">
                        Send Reset Link
                    </button>
                </form>
                <div id="resetMessage" style="margin-top: 15px; padding: 10px; border-radius: 6px; display: none;"></div>
            </div>
        </div>

        <script>
            // Password visibility toggle
            function togglePassword() {
                const passwordInput = document.getElementById('password');
                const toggleIcon = document.getElementById('passwordToggleIcon');
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    toggleIcon.className = 'fas fa-eye-slash';
                } else {
                    passwordInput.type = 'password';
                    toggleIcon.className = 'fas fa-eye';
                }
            }

            // Forgot password functionality
            function showForgotPassword() {
                document.getElementById('forgotPasswordModal').style.display = 'block';
            }

            function closeForgotPassword() {
                document.getElementById('forgotPasswordModal').style.display = 'none';
                document.getElementById('resetMessage').style.display = 'none';
            }

            function handleForgotPassword(event) {
                event.preventDefault();
                const email = document.getElementById('resetEmail').value;
                const messageDiv = document.getElementById('resetMessage');
                
                // Simulate sending reset email (in real app, this would be an AJAX call)
                messageDiv.style.display = 'block';
                messageDiv.style.background = 'rgba(40, 167, 69, 0.1)';
                messageDiv.style.border = '1px solid #28a745';
                messageDiv.style.color = '#28a745';
                messageDiv.innerHTML = `
                    <i class="fas fa-check-circle"></i> 
                    Password reset instructions have been sent to ${email}
                `;
                
                // Clear form
                document.getElementById('forgotPasswordForm').reset();
                
                // Auto close after 3 seconds
                setTimeout(() => {
                    closeForgotPassword();
                }, 3000);
            }

            // Auto-select customer role when coming from Order Now buttons
            document.addEventListener('DOMContentLoaded', function() {
                const urlParams = new URLSearchParams(window.location.search);
                const preselect = urlParams.get('preselect');
                
                if (preselect === 'customer') {
                    document.getElementById('role').value = 'customer';
                    // Show a helpful message
                    const roleSelect = document.getElementById('role');
                    roleSelect.style.borderColor = '#ffd700';
                    roleSelect.style.boxShadow = '0 0 10px rgba(255, 215, 0, 0.3)';
                    
                    // Add a note for the user
                    const noteDiv = document.createElement('div');
                    noteDiv.style.cssText = 'background: rgba(255, 215, 0, 0.1); border: 1px solid #ffd700; color: #ffd700; padding: 10px; border-radius: 6px; margin-top: 10px; font-size: 14px; text-align: center;';
                    noteDiv.innerHTML = '💡 Customer role selected automatically for ordering';
                    roleSelect.parentNode.appendChild(noteDiv);
                }

                // Auto-logout timer (30 minutes = 1800000 ms)
                let logoutTimer;
                function resetLogoutTimer() {
                    clearTimeout(logoutTimer);
                    logoutTimer = setTimeout(() => {
                        alert('Your session has expired for security reasons. Please login again.');
                        window.location.href = '../auth/logout.php';
                    }, 1800000); // 30 minutes
                }

                // Reset timer on user activity
                document.addEventListener('click', resetLogoutTimer);
                document.addEventListener('keypress', resetLogoutTimer);
                document.addEventListener('scroll', resetLogoutTimer);
                document.addEventListener('mousemove', resetLogoutTimer);

                // Start the timer
                resetLogoutTimer();
            });

            // Close modal when clicking outside
            window.onclick = function(event) {
                const modal = document.getElementById('forgotPasswordModal');
                if (event.target === modal) {
                    closeForgotPassword();
                }
            }
        </script>


    </div>
</body>
</html>