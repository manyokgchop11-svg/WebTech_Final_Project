<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: ../auth/login_fixed.php');
    exit();
}

$conn = getDBConnection();
$user_id = $_SESSION['user_id'];

// Get user info
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    header('Location: ../auth/login_fixed.php');
    exit();
}

$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']) ?? '';
    
    if (empty($name) || empty($email)) {
        $error_message = 'Name and email are required';
    } else {
        $update_query = "UPDATE users SET full_name = ?, email = ?, phone = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("sssi", $name, $email, $phone, $user_id);
        
        if ($stmt->execute()) {
            $success_message = 'Profile updated successfully!';
            // Refresh user data
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
        } else {
            $error_message = 'Failed to update profile';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - QuickBite</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html, body {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%) !important;
            min-height: 100vh !important;
            color: #ffffff !important;
            font-family: 'Arial', sans-serif !important;
            overflow-x: hidden;
        }
        
        /* Header styles */
        .customer-header {
            position: sticky !important;
            top: 0 !important;
            z-index: 1000 !important;
            background: rgba(30, 30, 30, 0.95) !important;
            backdrop-filter: blur(10px) !important;
            border-bottom: 1px solid rgba(255, 215, 0, 0.3) !important;
            padding: 15px 0 !important;
        }
        
        .header-container {
            max-width: 1400px !important;
            margin: 0 auto !important;
            padding: 0 30px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
        }
        
        .customer-nav {
            display: flex !important;
            gap: 10px !important;
        }
        
        .nav-link {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            padding: 10px 18px !important;
            color: #cccccc !important;
            text-decoration: none !important;
            border-radius: 8px !important;
            transition: all 0.3s ease !important;
            font-size: 14px !important;
        }
        
        .nav-link:hover,
        .nav-link.active {
            background: rgba(255, 215, 0, 0.2) !important;
            color: #ffd700 !important;
        }
        
        .logo img {
            max-width: 120px !important;
            height: auto !important;
            border-radius: 12px !important;
        }
        
        .header-actions {
            display: flex !important;
            gap: 15px !important;
        }
        
        .btn-text {
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
            color: #cccccc !important;
            text-decoration: none !important;
            padding: 8px 15px !important;
            border-radius: 8px !important;
            transition: all 0.3s ease !important;
            font-size: 14px !important;
        }
        
        .btn-text:hover {
            background: rgba(255, 215, 0, 0.2) !important;
            color: #ffd700 !important;
        }
        
        .profile-page {
            padding: 30px 0 !important;
            min-height: calc(100vh - 100px) !important;
            width: 100% !important;
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        .profile-container {
            max-width: 900px !important;
            margin: 0 auto !important;
            padding: 20px !important;
            width: 100% !important;
            display: block !important;
            visibility: visible !important;
        }
        
        .page-title {
            color: #ffd700 !important;
            font-size: 2.8rem !important;
            margin-bottom: 30px !important;
            text-align: center !important;
            font-weight: 700 !important;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 15px !important;
            width: 100% !important;
            visibility: visible !important;
        }
        
        .profile-card {
            background: rgba(255, 255, 255, 0.08) !important;
            border: 1px solid rgba(255, 215, 0, 0.3) !important;
            border-radius: 15px !important;
            padding: 30px !important;
            backdrop-filter: blur(10px) !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3) !important;
            margin-bottom: 30px !important;
            width: 100% !important;
            display: block !important;
            visibility: visible !important;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 1.4rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-success {
            background: rgba(39, 174, 96, 0.1);
            border: 1px solid #27ae60;
            color: #27ae60;
        }
        
        .alert-error {
            background: rgba(231, 76, 60, 0.1);
            border: 1px solid #e74c3c;
            color: #e74c3c;
        }
        
        .form-section {
            margin-bottom: 40px;
        }
        
        .section-title {
            color: #ffd700 !important;
            font-size: 2rem !important;
            margin-bottom: 20px !important;
            font-weight: 600 !important;
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            visibility: visible !important;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        
        .form-group label {
            display: block !important;
            margin-bottom: 8px !important;
            font-weight: 600 !important;
            color: #ffffff !important;
            font-size: 1.4rem !important;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5) !important;
            visibility: visible !important;
        }
        
        .form-group input {
            width: 100% !important;
            padding: 12px 15px !important;
            background: rgba(255, 255, 255, 0.1) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            border-radius: 8px !important;
            color: #ffffff !important;
            font-size: 1.5rem !important;
            transition: all 0.3s ease !important;
            box-sizing: border-box !important;
            backdrop-filter: blur(5px) !important;
            display: block !important;
            visibility: visible !important;
        }
        
        .form-group input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #ffd700;
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 15px rgba(255, 215, 0, 0.3);
        }
        
        .btn {
            background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
            color: #1a1a1a;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 1.5rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn:hover {
            background: linear-gradient(135deg, #ffffff 0%, #f0f0f0 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 215, 0, 0.4);
        }
        
        .info-section {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 25px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .info-section h2 {
            color: #ffd700;
            margin-bottom: 20px;
            font-size: 2rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .info-item {
            background: rgba(255, 255, 255, 0.05);
            padding: 15px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .info-label {
            font-weight: 600;
            color: #ffd700;
            font-size: 1.3rem;
            margin-bottom: 5px;
            display: block;
        }
        
        .info-value {
            color: #e0e0e0;
            font-size: 1.5rem;
            font-weight: 500;
        }
        
        .status-active {
            color: #27ae60;
            font-weight: 600;
        }
        
        /* Scrollable container for small screens */
        .profile-page {
            overflow-y: auto;
            max-height: calc(100vh - 80px);
        }
        
        /* Ensure form elements are properly sized */
        .form-group input {
            min-height: 48px; /* Minimum touch target size */
        }
        
        .btn {
            min-height: 48px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        /* Responsive Design */
        @media (max-width: 992px) {
            .form-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
        }
        
        @media (max-width: 768px) {
            .profile-container {
                padding: 15px;
            }
            
            .profile-card {
                padding: 20px;
                margin-bottom: 20px;
            }
            
            .page-title {
                font-size: 2.2rem;
                margin-bottom: 20px;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .section-title {
                font-size: 1.8rem;
            }
            
            .form-group input {
                font-size: 16px; /* Prevents zoom on iOS */
            }
        }
        
        @media (max-width: 480px) {
            .profile-container {
                padding: 10px;
            }
            
            .profile-card {
                padding: 15px;
                margin-bottom: 15px;
            }
            
            .page-title {
                font-size: 1.8rem;
                flex-direction: column;
                gap: 8px;
                margin-bottom: 15px;
            }
            
            .section-title {
                font-size: 1.6rem;
                flex-direction: column;
                gap: 5px;
                text-align: center;
            }
            
            .btn {
                width: 100%;
                padding: 15px;
                font-size: 1.4rem;
            }
        }
        
        /* Fix for very small screens */
        @media (max-width: 360px) {
            .profile-container {
                padding: 5px;
            }
            
            .profile-card {
                padding: 10px;
            }
            
            .page-title {
                font-size: 1.6rem;
            }
        }
    </style>
</head>
<body class="customer-page">
    <!-- Simple Header -->
    <header class="customer-header">
        <div class="header-container">
            <a href="dashboard.php" class="logo">
                <img src="../assets/images/logo1.png" alt="QuickBite">
            </a>

            <nav class="customer-nav">
                <a href="dashboard.php" class="nav-link">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                <a href="menu.php" class="nav-link">
                    <i class="fas fa-utensils"></i>
                    <span>Menu</span>
                </a>
                <a href="orders.php" class="nav-link">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Orders</span>
                </a>
                <a href="profile.php" class="nav-link active">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
                </a>
            </nav>

            <div class="header-actions">
                <a href="../index.php" class="btn-text">
                    <i class="fas fa-home"></i>
                    <span>Website</span>
                </a>
                <a href="../auth/logout.php" class="btn-text">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </header>

    <main class="profile-page">
        <div class="profile-container">
            <h1 class="page-title">
                <i class="fas fa-user-circle"></i>
                My Profile
            </h1>

            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <div class="profile-card">
                <div class="form-section">
                    <h2 class="section-title">
                        <i class="fas fa-edit"></i>
                        Edit Profile Information
                    </h2>
                    
                    <form method="POST">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="name">
                                    <i class="fas fa-user"></i>
                                    Full Name
                                </label>
                                <input type="text" id="name" name="name" 
                                       value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>" 
                                       placeholder="Enter your full name" required>
                            </div>

                            <div class="form-group">
                                <label for="phone">
                                    <i class="fas fa-phone"></i>
                                    Phone Number
                                </label>
                                <input type="tel" id="phone" name="phone" 
                                       value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" 
                                       placeholder="Enter your phone number">
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <label for="email">
                                <i class="fas fa-envelope"></i>
                                Email Address
                            </label>
                            <input type="email" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($user['email']); ?>" 
                                   placeholder="Enter your email address" required>
                        </div>

                        <button type="submit" class="btn">
                            <i class="fas fa-save"></i>
                            Update Profile
                        </button>
                    </form>
                </div>
            </div>

            <div class="profile-card">
                <div class="info-section">
                    <h2 class="section-title">
                        <i class="fas fa-info-circle"></i>
                        Account Information
                    </h2>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">
                                <i class="fas fa-user-tag"></i>
                                Username
                            </span>
                            <span class="info-value"><?php echo htmlspecialchars($user['username']); ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">
                                <i class="fas fa-shield-alt"></i>
                                Account Type
                            </span>
                            <span class="info-value"><?php echo ucfirst($user['role']); ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">
                                <i class="fas fa-calendar-alt"></i>
                                Member Since
                            </span>
                            <span class="info-value"><?php echo date('F d, Y', strtotime($user['created_at'])); ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">
                                <i class="fas fa-check-circle"></i>
                                Account Status
                            </span>
                            <span class="info-value status-active">Active</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Simple script to ensure page loads properly
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Profile page loaded successfully');
            
            // Ensure all elements are visible
            const profilePage = document.querySelector('.profile-page');
            const profileContainer = document.querySelector('.profile-container');
            
            if (profilePage) {
                profilePage.style.display = 'block';
                profilePage.style.visibility = 'visible';
                profilePage.style.opacity = '1';
            }
            
            if (profileContainer) {
                profileContainer.style.display = 'block';
                profileContainer.style.visibility = 'visible';
                profileContainer.style.opacity = '1';
            }
        });
    </script>
</body>
</html>