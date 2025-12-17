<header class="topbar">
    <button class="sidebar-toggle" id="sidebarToggle">
        <ion-icon name="menu-outline"></ion-icon>
    </button>

    <div class="topbar-search">
        <ion-icon name="search-outline"></ion-icon>
        <input type="text" placeholder="Search..." class="search-input">
    </div>

    <div class="topbar-actions">
        <button class="topbar-btn" title="Notifications">
            <ion-icon name="notifications-outline"></ion-icon>
            <span class="badge-dot"></span>
        </button>

        <div class="user-menu">
            <button class="user-btn" id="userMenuBtn">
                <div class="user-avatar">
                    <ion-icon name="person-outline"></ion-icon>
                </div>
                <span class="user-name"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                <ion-icon name="chevron-down-outline"></ion-icon>
            </button>

            <div class="user-dropdown" id="userDropdown">
                <a href="../customer/profile.php" class="dropdown-item">
                    <ion-icon name="person-outline"></ion-icon>
                    <span>Profile</span>
                </a>
                <a href="../auth/logout.php" class="dropdown-item">
                    <ion-icon name="log-out-outline"></ion-icon>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </div>
</header>
