<?php
session_start();
require_once '../config/database.php';

// If already logged in, redirect
if (isset($_SESSION['user_id'])) {
    header('Location: ' . ($_SESSION['role'] === 'admin' ? '../admin/dashboard.php' : '../customer/dashboard.php'));
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if (empty($username) || empty($email) || empty($full_name) || empty($password)) {
        $error = 'Please fill in all required fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        try {
            $conn = getDBConnection();
            
            // Check if username or email already exists
            $check_stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $check_stmt->bind_param("ss", $username, $email);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows > 0) {
                $error = 'Username or email already exists';
            } else {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert new user
                $stmt = $conn->prepare("INSERT INTO users (username, email, password, full_name, phone, role) VALUES (?, ?, ?, ?, ?, 'customer')");
                $stmt->bind_param("sssss", $username, $email, $hashed_password, $full_name, $phone);
                
                if ($stmt->execute()) {
                    $success = 'Registration successful! You can now login.';
                    // Auto-login
                    $_SESSION['user_id'] = $stmt->insert_id;
                    $_SESSION['username'] = $username;
                    $_SESSION['email'] = $email;
                    $_SESSION['full_name'] = $full_name;
                    $_SESSION['role'] = 'customer';
                    
                    header('Location: ../customer/dashboard.php');
                    exit();
                } else {
                    $error = 'Registration failed. Please try again.';
                }
                
                $stmt->close();
            }
            
            $check_stmt->close();
            $conn->close();
        } catch (Exception $e) {
            $error = 'Registration failed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - QuickBite</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Enhanced registration form styles */
        .auth-box {
            max-height: none !important;
            overflow: visible !important;
        }
        
        .form-group {
            margin-bottom: 25px !important;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--gold-crayola);
            font-weight: 600;
            font-size: 1.4rem;
        }
        
        .input-field {
            width: 100%;
            padding: 15px;
            background: var(--smoky-black-1);
            border: 2px solid var(--white-alpha-10);
            border-radius: 8px;
            color: var(--white);
            font-size: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .input-field:focus {
            outline: none;
            border-color: var(--gold-crayola);
            background: var(--eerie-black-1);
        }
        
        .input-field::placeholder {
            color: var(--quick-silver);
        }
        
        .required-field {
            color: #ff6b6b;
        }
        
        .btn-block {
            width: 100%;
            padding: 18px;
            font-size: 1.6rem;
            margin-top: 30px;
        }
        
        .form-help {
            font-size: 1.2rem;
            color: var(--quick-silver);
            margin-top: 5px;
        }
        
        @media (max-width: 768px) {
            .auth-container {
                padding: 10px;
            }
            
            .auth-box {
                padding: 30px 20px;
            }
            
            .input-field {
                padding: 12px;
                font-size: 1.4rem;
            }
        }
    </style>
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <img src="../assets/images/logo1.png" alt="QuickBite Logo" class="auth-logo">
                <h1 class="headline-2">Create Account</h1>
                <p class="body-2">Join QuickBite today</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <ion-icon name="alert-circle-outline"></ion-icon>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <ion-icon name="checkmark-circle-outline"></ion-icon>
                    <span><?php echo htmlspecialchars($success); ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="full_name">Full Name <span class="required-field">*</span></label>
                    <input type="text" id="full_name" name="full_name" class="input-field" required
                           placeholder="Enter your full name"
                           value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
                    <div class="form-help">Your first and last name</div>
                </div>

                <div class="form-group">
                    <label for="username">Username <span class="required-field">*</span></label>
                    <input type="text" id="username" name="username" class="input-field" required
                           placeholder="Choose a username"
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                    <div class="form-help">This will be your login username</div>
                </div>

                <div class="form-group">
                    <label for="email">Email Address <span class="required-field">*</span></label>
                    <input type="email" id="email" name="email" class="input-field" required
                           placeholder="your.email@example.com"
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    <div class="form-help">We'll use this for order confirmations</div>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" class="input-field"
                           placeholder="+211 123 456 789"
                           value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                    <div class="form-help">Optional - for delivery updates</div>
                </div>

                <div class="form-group">
                    <label for="password">Password <span class="required-field">*</span></label>
                    <div class="password-input-container" style="position: relative;">
                        <input type="password" id="password" name="password" class="input-field" required minlength="8"
                               placeholder="Create a secure password" style="padding-right: 45px;">
                        <button type="button" class="password-toggle" onclick="togglePassword('password', 'passwordToggleIcon')" 
                                style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); 
                                       background: none; border: none; color: var(--gold-crayola); cursor: pointer; 
                                       font-size: 18px; padding: 0; width: 24px; height: 24px;">
                            <i class="fas fa-eye" id="passwordToggleIcon"></i>
                        </button>
                    </div>
                    <div class="form-help">Minimum 8 characters required</div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password <span class="required-field">*</span></label>
                    <div class="password-input-container" style="position: relative;">
                        <input type="password" id="confirm_password" name="confirm_password" class="input-field" required minlength="8"
                               placeholder="Re-enter your password" style="padding-right: 45px;">
                        <button type="button" class="password-toggle" onclick="togglePassword('confirm_password', 'confirmPasswordToggleIcon')" 
                                style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); 
                                       background: none; border: none; color: var(--gold-crayola); cursor: pointer; 
                                       font-size: 18px; padding: 0; width: 24px; height: 24px;">
                            <i class="fas fa-eye" id="confirmPasswordToggleIcon"></i>
                        </button>
                    </div>
                    <div class="form-help">Must match the password above</div>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <span class="text text-1">Create Account</span>
                    <span class="text text-2" aria-hidden="true">Create Account</span>
                </button>
            </form>

            <div class="auth-footer">
                <p class="body-4">Already have an account? <a href="login_fixed.php" class="link">Sign In</a></p>
                <p class="body-4"><a href="../index.php" class="link">← Back to Website</a></p>
            </div>
        </div>
    </div>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    
    <script>
        // Password visibility toggle
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                toggleIcon.className = 'fas fa-eye';
            }
        }

        // Password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const helpDiv = this.parentNode.nextElementSibling;
            
            if (password.length === 0) {
                helpDiv.textContent = 'Minimum 8 characters required';
                helpDiv.style.color = '#ccc';
            } else if (password.length < 8) {
                helpDiv.textContent = `${8 - password.length} more characters needed`;
                helpDiv.style.color = '#e74c3c';
            } else {
                helpDiv.textContent = 'Password strength: Good ✓';
                helpDiv.style.color = '#27ae60';
            }
        });

        // Password match validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            const helpDiv = this.parentNode.nextElementSibling;
            
            if (confirmPassword.length === 0) {
                helpDiv.textContent = 'Must match the password above';
                helpDiv.style.color = '#ccc';
            } else if (password !== confirmPassword) {
                helpDiv.textContent = 'Passwords do not match';
                helpDiv.style.color = '#e74c3c';
            } else {
                helpDiv.textContent = 'Passwords match ✓';
                helpDiv.style.color = '#27ae60';
            }
        });
    </script>
</body>
</html>
