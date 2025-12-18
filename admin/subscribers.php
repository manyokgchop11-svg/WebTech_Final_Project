<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login_fixed.php');
    exit();
}

$conn = getDBConnection();

// Get subscribers
$query = "SELECT * FROM newsletter_subscribers ORDER BY subscribed_at DESC";
$subscribers = $conn->query($query);

// Get statistics
$stats_query = "SELECT COUNT(*) as total FROM newsletter_subscribers";
$stats_result = $conn->query($stats_query);
$stats = $stats_result ? $stats_result->fetch_assoc() : ['total' => 0];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Newsletter Subscribers - QuickBite Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body class="admin-page">
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="admin-content">
        <?php include 'includes/topbar.php'; ?>
        
        <div class="content-wrapper">
            <div class="page-header">
                <h1 class="headline-1">Newsletter Subscribers</h1>
                <p class="body-2">Manage newsletter subscriptions</p>
            </div>

            <!-- Statistics -->
            <div class="stats-row">
                <div class="stat-box stat-primary">
                    <ion-icon name="mail-outline"></ion-icon>
                    <div>
                        <h3><?php echo $stats['total']; ?></h3>
                        <p>Total Subscribers</p>
                    </div>
                </div>
            </div>

            <!-- Subscribers Table -->
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>Subscribed Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($subscribers && $subscribers->num_rows > 0): ?>
                            <?php while ($subscriber = $subscribers->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($subscriber['email']); ?></td>
                                    <td><?php echo date('M j, Y g:i A', strtotime($subscriber['subscribed_at'])); ?></td>
                                    <td>
                                        <span class="status-badge status-active">Active</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="editSubscriber(<?php echo $subscriber['id']; ?>)">Edit</button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteSubscriber(<?php echo $subscriber['id']; ?>)">Delete</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="no-data">No subscribers yet</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    
    <script>
        function editSubscriber(id) {
            alert('Edit functionality will be implemented. Subscriber ID: ' + id);
        }
        
        function deleteSubscriber(id) {
            if (confirm('Are you sure you want to delete this subscriber?')) {
                // AJAX call to delete subscriber
                fetch('delete_subscriber.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({id: id})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error deleting subscriber');
                    }
                });
            }
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>