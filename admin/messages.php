<?php
session_start();
require_once '../config/database.php';

// Check admin authentication
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login_fixed.php');
    exit();
}

$conn = getDBConnection();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $id = intval($_POST['message_id']);
    $status = $_POST['status'];
    
    $stmt = $conn->prepare("UPDATE contact_messages SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    
    if ($stmt->execute()) {
        $success = "Message status updated successfully!";
    } else {
        $error = "Failed to update message status.";
    }
    $stmt->close();
}

// Get filter
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

// Build query
$query = "SELECT * FROM contact_messages WHERE 1=1";
$params = [];
$types = "";

if ($status_filter !== 'all') {
    $query .= " AND status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

$query .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$messages = $stmt->get_result();

// Get statistics
$stats_query = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) as new,
    SUM(CASE WHEN status = 'read' THEN 1 ELSE 0 END) as read,
    SUM(CASE WHEN status = 'replied' THEN 1 ELSE 0 END) as replied
    FROM contact_messages";
$stats_result = $conn->query($stats_query);
$stats = $stats_result ? $stats_result->fetch_assoc() : ['total' => 0, 'new' => 0, 'read' => 0, 'replied' => 0];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages - QuickBite Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body class="admin-page">
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="admin-content">
        <?php include 'includes/topbar.php'; ?>
        
        <div class="content-wrapper">
            <div class="page-header">
                <h1 class="headline-1">Contact Messages</h1>
                <p class="body-2">Manage customer inquiries and feedback</p>
            </div>

            <?php if (isset($success)): ?>
                <div class="alert alert-success">
                    <ion-icon name="checkmark-circle-outline"></ion-icon>
                    <span><?php echo $success; ?></span>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <ion-icon name="alert-circle-outline"></ion-icon>
                    <span><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <!-- Statistics -->
            <div class="stats-row">
                <div class="stat-box stat-primary">
                    <ion-icon name="mail-outline"></ion-icon>
                    <div>
                        <h3><?php echo $stats['total']; ?></h3>
                        <p>Total Messages</p>
                    </div>
                </div>
                
                <div class="stat-box stat-warning">
                    <ion-icon name="mail-unread-outline"></ion-icon>
                    <div>
                        <h3><?php echo $stats['new']; ?></h3>
                        <p>New Messages</p>
                    </div>
                </div>
                
                <div class="stat-box stat-success">
                    <ion-icon name="mail-open-outline"></ion-icon>
                    <div>
                        <h3><?php echo $stats['read']; ?></h3>
                        <p>Read Messages</p>
                    </div>
                </div>
                
                <div class="stat-box stat-primary">
                    <ion-icon name="checkmark-circle-outline"></ion-icon>
                    <div>
                        <h3><?php echo $stats['replied']; ?></h3>
                        <p>Replied</p>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="filters-section">
                <div class="filter-tabs">
                    <a href="messages.php?status=all" class="filter-tab <?php echo $status_filter === 'all' ? 'active' : ''; ?>">
                        All Messages
                    </a>
                    <a href="messages.php?status=new" class="filter-tab <?php echo $status_filter === 'new' ? 'active' : ''; ?>">
                        New
                    </a>
                    <a href="messages.php?status=read" class="filter-tab <?php echo $status_filter === 'read' ? 'active' : ''; ?>">
                        Read
                    </a>
                    <a href="messages.php?status=replied" class="filter-tab <?php echo $status_filter === 'replied' ? 'active' : ''; ?>">
                        Replied
                    </a>
                </div>
            </div>

            <!-- Messages List -->
            <div class="messages-container">
                <?php if ($messages->num_rows > 0): ?>
                    <?php while ($message = $messages->fetch_assoc()): ?>
                        <div class="message-card <?php echo $message['status']; ?>">
                            <div class="message-header">
                                <div class="sender-info">
                                    <h3><?php echo htmlspecialchars($message['name']); ?></h3>
                                    <p><?php echo htmlspecialchars($message['email']); ?></p>
                                    <?php if (!empty($message['phone'])): ?>
                                        <p><?php echo htmlspecialchars($message['phone']); ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="message-meta">
                                    <span class="status-badge status-<?php echo $message['status']; ?>">
                                        <?php echo ucfirst($message['status']); ?>
                                    </span>
                                    <small><?php echo date('M j, Y g:i A', strtotime($message['created_at'])); ?></small>
                                </div>
                            </div>
                            
                            <div class="message-content">
                                <p><?php echo nl2br(htmlspecialchars($message['message'])); ?></p>
                            </div>
                            
                            <div class="message-actions">
                                <form method="POST" class="status-form">
                                    <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                    <select name="status" onchange="this.form.submit()">
                                        <option value="new" <?php echo $message['status'] === 'new' ? 'selected' : ''; ?>>New</option>
                                        <option value="read" <?php echo $message['status'] === 'read' ? 'selected' : ''; ?>>Read</option>
                                        <option value="replied" <?php echo $message['status'] === 'replied' ? 'selected' : ''; ?>>Replied</option>
                                    </select>
                                    <input type="hidden" name="update_status" value="1">
                                </form>
                                
                                <a href="mailto:<?php echo htmlspecialchars($message['email']); ?>" class="btn btn-sm btn-primary">
                                    <ion-icon name="mail-outline"></ion-icon>
                                    Reply
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <ion-icon name="mail-outline"></ion-icon>
                        <h3>No messages found</h3>
                        <p>No contact messages match your current filter.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>

<?php
$conn->close();
?>