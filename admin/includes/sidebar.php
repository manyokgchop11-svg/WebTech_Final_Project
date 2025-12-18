<aside class="sidebar">
    <div class="sidebar-header">
        <img src="../assets/images/logo1.png" alt="QuickBite" class="sidebar-logo">
        <h2 class="title-3">Admin Panel</h2>
    </div>

    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
            <ion-icon name="grid-outline"></ion-icon>
            <span>Dashboard</span>
        </a>

        <a href="orders.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>">
            <ion-icon name="cart-outline"></ion-icon>
            <span>Orders</span>
            <?php if (isset($stats['pending_orders']) && $stats['pending_orders'] > 0): ?>
                <span class="badge"><?php echo $stats['pending_orders']; ?></span>
            <?php endif; ?>
        </a>

        <a href="reservations.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'reservations.php' ? 'active' : ''; ?>">
            <ion-icon name="calendar-outline"></ion-icon>
            <span>Reservations</span>
        </a>

        <a href="menu.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'menu.php' ? 'active' : ''; ?>">
            <ion-icon name="restaurant-outline"></ion-icon>
            <span>Menu Management</span>
        </a>

        <a href="tables.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'tables.php' ? 'active' : ''; ?>">
            <ion-icon name="grid-outline"></ion-icon>
            <span>Tables</span>
        </a>

        <a href="customers.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'customers.php' ? 'active' : ''; ?>">
            <ion-icon name="people-outline"></ion-icon>
            <span>Customers</span>
        </a>

        <a href="messages.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'messages.php' ? 'active' : ''; ?>">
            <ion-icon name="mail-outline"></ion-icon>
            <span>Messages</span>
        </a>

        <a href="subscribers.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'subscribers.php' ? 'active' : ''; ?>">
            <ion-icon name="mail-open-outline"></ion-icon>
            <span>Subscribers</span>
        </a>

        <a href="reports.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>">
            <ion-icon name="bar-chart-outline"></ion-icon>
            <span>Reports</span>
        </a>

        <div class="nav-divider"></div>

        <a href="../index.php" class="nav-item">
            <ion-icon name="home-outline"></ion-icon>
            <span>View Website</span>
        </a>

        <a href="../auth/logout.php" class="nav-item">
            <ion-icon name="log-out-outline"></ion-icon>
            <span>Logout</span>
        </a>
    </nav>
</aside>
