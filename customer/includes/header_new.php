<header class="customer-header">
    <div class="header-container">
        <a href="dashboard.php" class="logo">
            <img src="../assets/images/logo1.png" alt="QuickBite">
        </a>

        <nav class="customer-nav">
            <a href="dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <ion-icon name="home-outline"></ion-icon>
                <span>Dashboard</span>
            </a>
            <a href="menu.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'menu.php' ? 'active' : ''; ?>">
                <ion-icon name="restaurant-outline"></ion-icon>
                <span>Menu</span>
            </a>
            <a href="cart.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'cart.php' ? 'active' : ''; ?>">
                <ion-icon name="cart-outline"></ion-icon>
                <span>Cart</span>
                <span class="cart-count" id="cartCount">0</span>
            </a>
            <a href="orders.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>">
                <ion-icon name="list-outline"></ion-icon>
                <span>Orders</span>
            </a>
            <a href="profile.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">
                <ion-icon name="person-outline"></ion-icon>
                <span>Profile</span>
            </a>
        </nav>

        <div class="header-actions">
            <a href="../index.html" class="btn-text">
                <ion-icon name="home-outline"></ion-icon>
                <span>Website</span>
            </a>
            <a href="../auth/logout.php" class="btn-text">
                <ion-icon name="log-out-outline"></ion-icon>
                <span>Logout</span>
            </a>
        </div>

        <button class="mobile-menu-btn" id="mobileMenuBtn">
            <ion-icon name="menu-outline"></ion-icon>
        </button>
    </div>
</header>

<div class="mobile-menu" id="mobileMenu">
    <button class="close-menu-btn" id="closeMenuBtn">
        <ion-icon name="close-outline"></ion-icon>
    </button>
    <nav class="mobile-nav">
        <a href="dashboard.php"><ion-icon name="home-outline"></ion-icon> Dashboard</a>
        <a href="menu.php"><ion-icon name="restaurant-outline"></ion-icon> Menu</a>
        <a href="cart.php"><ion-icon name="cart-outline"></ion-icon> Cart</a>
        <a href="orders.php"><ion-icon name="list-outline"></ion-icon> Orders</a>
        <a href="profile.php"><ion-icon name="person-outline"></ion-icon> Profile</a>
        <a href="../index.html"><ion-icon name="home-outline"></ion-icon> Website</a>
        <a href="../auth/logout.php"><ion-icon name="log-out-outline"></ion-icon> Logout</a>
    </nav>
</div>

<script>
    // Mobile menu functionality
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const closeMenuBtn = document.getElementById('closeMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        
        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', function() {
                mobileMenu.classList.add('active');
            });
        }
        
        if (closeMenuBtn) {
            closeMenuBtn.addEventListener('click', function() {
                mobileMenu.classList.remove('active');
            });
        }
        
        // Update cart count in header
        function updateHeaderCartCount() {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            const count = cart.reduce((total, item) => total + item.quantity, 0);
            const cartCountElements = document.querySelectorAll('#cartCount');
            cartCountElements.forEach(element => {
                element.textContent = count;
            });
        }
        
        updateHeaderCartCount();
        
        // Listen for cart updates
        window.addEventListener('storage', updateHeaderCartCount);
    });
</script>