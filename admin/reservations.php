<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login_fixed.php');
    exit();
}

$conn = getDBConnection();

// Get reservations
$query = "SELECT * FROM reservations ORDER BY reservation_date DESC, reservation_time DESC";
$reservations = $conn->query($query);

// Get statistics
$stats_query = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
    FROM reservations";
$stats_result = $conn->query($stats_query);
$stats = $stats_result ? $stats_result->fetch_assoc() : ['total' => 0, 'pending' => 0, 'confirmed' => 0, 'cancelled' => 0];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservations - QuickBite Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body class="admin-page">
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="admin-content">
        <?php include 'includes/topbar.php'; ?>
        
        <div class="content-wrapper">
            <div class="page-header">
                <h1 class="headline-1">Reservation Management</h1>
                <p class="body-2">Manage table reservations and bookings</p>
            </div>

            <!-- Statistics -->
            <div class="stats-row">
                <div class="stat-box stat-primary">
                    <ion-icon name="calendar-outline"></ion-icon>
                    <div>
                        <h3><?php echo $stats['total']; ?></h3>
                        <p>Total Reservations</p>
                    </div>
                </div>
                
                <div class="stat-box stat-warning">
                    <ion-icon name="time-outline"></ion-icon>
                    <div>
                        <h3><?php echo $stats['pending']; ?></h3>
                        <p>Pending</p>
                    </div>
                </div>
                
                <div class="stat-box stat-success">
                    <ion-icon name="checkmark-circle-outline"></ion-icon>
                    <div>
                        <h3><?php echo $stats['confirmed']; ?></h3>
                        <p>Confirmed</p>
                    </div>
                </div>
                
                <div class="stat-box stat-error">
                    <ion-icon name="close-circle-outline"></ion-icon>
                    <div>
                        <h3><?php echo $stats['cancelled']; ?></h3>
                        <p>Cancelled</p>
                    </div>
                </div>
            </div>

            <!-- Reservations Table -->
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Date & Time</th>
                            <th>Party Size</th>
                            <th>Status</th>
                            <th>Contact</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($reservations && $reservations->num_rows > 0): ?>
                            <?php while ($reservation = $reservations->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($reservation['customer_name']); ?></td>
                                    <td>
                                        <?php echo date('M j, Y', strtotime($reservation['reservation_date'])); ?><br>
                                        <small><?php echo date('g:i A', strtotime($reservation['reservation_time'])); ?></small>
                                    </td>
                                    <td><?php echo $reservation['party_size']; ?> people</td>
                                    <td>
                                        <span class="status-badge status-<?php echo $reservation['status']; ?>">
                                            <?php echo ucfirst($reservation['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($reservation['phone']); ?><br>
                                        <small><?php echo htmlspecialchars($reservation['email']); ?></small>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary">Edit</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="no-data">No reservations found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>

<?php $conn->close(); ?>