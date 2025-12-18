<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: ../auth/login_fixed.php');
    exit();
}

$conn = getDBConnection();

// Get category filter
$category_filter = $_GET['category'] ?? 'all';

// Build query
$query = "SELECT * FROM menu_items WHERE is_available = 1";
if ($category_filter !== 'all') {
    $query .= " AND category = '$category_filter'";
}
$query .= " ORDER BY category, name";

$menu_items = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - QuickBite</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/customer.css">
    <style>
        .menu-page {
            background: var(--smoky-black-1);
            min-height: 100vh;
            padding-top: 100px;
        }
        
        .menu-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 30px;
        }
        
        .menu-header {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .menu-header .headline-1 {
            color: var(--gold-crayola);
            margin-bottom: 15px;
        }
        
        .menu-header .body-2 {
            color: var(--quick-silver);
            font-size: 1.8rem;
        }
        
        .menu-search {
            max-width: 600px;
            margin: 0 auto 40px;
            position: relative;
        }
        
        .search-input {
            width: 100%;
            padding: 15px 20px 15px 50px;
            background: var(--eerie-black-2);
            border: 1px solid var(--white-alpha-10);
            border-radius: 12px;
            color: var(--white);
            font-size: 1.6rem;
        }
        
        .search-input:focus {
            outline: none;
            border-color: var(--gold-crayola);
        }
        
        .search-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--quick-silver);
            font-size: 2rem;
        }
        
        .search-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--gold-crayola);
            color: var(--smoky-black-1);
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .category-filters {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 50px;
            flex-wrap: wrap;
        }
        
        .filter-btn {
            padding: 12px 25px;
            background: var(--eerie-black-2);
            border: 1px solid var(--white-alpha-10);
            border-radius: 25px;
            color: var(--quick-silver);
            text-decoration: none;
            font-size: 1.4rem;
            transition: all 0.3s ease;
            text-transform: capitalize;
        }
        
        .filter-btn:hover,
        .filter-btn.active {
            background: var(--gold-crayola);
            color: var(--smoky-black-1);
            border-color: var(--gold-crayola);
        }
        
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 25px;
            margin-bottom: 50px;
        }
        
        .menu-card {
            background: var(--eerie-black-2);
            border: 1px solid var(--white-alpha-10);
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
            position: relative;
            min-height: 450px;
            display: flex;
            flex-direction: column;
        }
        
        .menu-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }
        
        .menu-card-image {
            height: 250px;
            position: relative;
            overflow: hidden;
            background: var(--smoky-black-1);
        }
        
        .menu-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .menu-card:hover .menu-card-image img {
            transform: scale(1.1);
        }
        
        .menu-card-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(138, 180, 70, 0.1) 0%, rgba(184, 134, 11, 0.1) 100%);
            z-index: 1;
        }
        
        .dish-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 6rem;
            color: var(--gold-crayola);
            z-index: 2;
            opacity: 0.3;
        }
        
        .category-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--smoky-black-1);
            color: var(--gold-crayola);
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 1.2rem;
            font-weight: bold;
            text-transform: uppercase;
            z-index: 3;
        }
        
        .menu-card-content {
            padding: 25px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .dish-name {
            color: var(--white);
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .dish-description {
            color: var(--quick-silver);
            font-size: 1.4rem;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        
        .dish-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
            padding-top: 15px;
        }
        
        .dish-price {
            color: var(--gold-crayola);
            font-size: 2.4rem;
            font-weight: bold;
        }
        
        .add-to-cart-btn {
            background: var(--gold-crayola);
            color: var(--smoky-black-1);
            border: none;
            padding: 12px 20px;
            border-radius: 25px;
            font-size: 1.4rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .add-to-cart-btn:hover {
            background: var(--white);
            transform: translateY(-2px);
        }
        
        .cart-notification {
            position: fixed;
            top: 100px;
            right: 30px;
            background: var(--gold-crayola);
            color: var(--smoky-black-1);
            padding: 15px 25px;
            border-radius: 12px;
            font-weight: bold;
            z-index: 1000;
            transform: translateX(400px);
            transition: transform 0.3s ease;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        
        .cart-notification.show {
            transform: translateX(0);
        }
        
        .empty-menu {
            text-align: center;
            padding: 80px 20px;
            color: var(--quick-silver);
        }
        
        .empty-menu .headline-2 {
            color: var(--gold-crayola);
            margin-bottom: 20px;
        }
        
        /* Category Icons */
        .breakfast-icon::before { content: "🍳"; }
        .lunch-icon::before { content: "🍽️"; }
        .dinner-icon::before { content: "🥘"; }
        .drinks-icon::before { content: "🥤"; }
        .desserts-icon::before { content: "🍰"; }
        .burgers-icon::before { content: "🍔"; }
        .pizza-icon::before { content: "🍕"; }
        .salads-icon::before { content: "🥗"; }
        .mains-icon::before { content: "🍖"; }
        .pasta-icon::before { content: "🍝"; }
        .beverages-icon::before { content: "☕"; }
        
        @media (max-width: 768px) {
            .menu-container {
                padding: 20px 15px;
            }
            
            .menu-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .category-filters {
                flex-direction: column;
                align-items: center;
                gap: 10px;
            }
            
            .filter-btn {
                width: 100%;
                max-width: 200px;
                text-align: center;
            }
            
            .menu-search {
                margin-bottom: 30px;
            }
            
            .search-input {
                padding: 12px 15px 12px 45px;
                font-size: 1.4rem;
            }
        }
    </style>
</head>
<body class="customer-page">
    <?php include 'includes/header_new.php'; ?>

    <main class="menu-page">
        <div class="menu-container">
            <div class="menu-header">
                <h1 class="headline-1">Our Menu</h1>
                <p class="body-2">Explore our delicious dishes</p>
            </div>

            <div class="menu-search">
                <ion-icon name="search-outline" class="search-icon"></ion-icon>
                <input type="text" class="search-input" placeholder="Search menu..." id="searchInput">
                <button class="search-btn" onclick="searchMenu()">SEARCH</button>
            </div>

            <div class="category-filters">
                <a href="?category=all" class="filter-btn <?php echo $category_filter === 'all' ? 'active' : ''; ?>">All</a>
                <a href="?category=breakfast" class="filter-btn <?php echo $category_filter === 'breakfast' ? 'active' : ''; ?>">Breakfast</a>
                <a href="?category=lunch" class="filter-btn <?php echo $category_filter === 'lunch' ? 'active' : ''; ?>">Lunch</a>
                <a href="?category=dinner" class="filter-btn <?php echo $category_filter === 'dinner' ? 'active' : ''; ?>">Dinner</a>
                <a href="?category=drinks" class="filter-btn <?php echo $category_filter === 'drinks' ? 'active' : ''; ?>">Drinks</a>
                <a href="?category=desserts" class="filter-btn <?php echo $category_filter === 'desserts' ? 'active' : ''; ?>">Desserts</a>
            </div>

            <div class="menu-grid" id="menuGrid">
                <?php if ($menu_items && $menu_items->num_rows > 0): ?>
                    <?php while ($item = $menu_items->fetch_assoc()): ?>
                        <div class="menu-card" data-name="<?php echo strtolower($item['name']); ?>" data-category="<?php echo $item['category']; ?>">
                            <div class="menu-card-image">
                                <?php if (!empty($item['image_url']) && $item['image_url'] !== 'No image'): ?>
                                    <img src="../assets/images/<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="dish-icon <?php echo $item['category']; ?>-icon" style="display: none;"></div>
                                <?php else: ?>
                                    <div class="dish-icon <?php echo $item['category']; ?>-icon"></div>
                                <?php endif; ?>
                                <div class="category-badge"><?php echo ucfirst($item['category']); ?></div>
                            </div>
                            
                            <div class="menu-card-content">
                                <h3 class="dish-name"><?php echo htmlspecialchars($item['name']); ?></h3>
                                <p class="dish-description">
                                    <?php echo htmlspecialchars($item['description'] ?? 'Delicious and freshly prepared dish'); ?>
                                </p>
                                
                                <div class="dish-footer">
                                    <span class="dish-price"><?php echo number_format($item['price'], 0); ?> SSP</span>
                                    <button class="add-to-cart-btn" onclick="addToCart(<?php echo $item['id']; ?>, '<?php echo htmlspecialchars($item['name']); ?>', <?php echo $item['price']; ?>)">
                                        <ion-icon name="add-outline"></ion-icon>
                                        Add to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty-menu">
                        <h2 class="headline-2">No menu items available</h2>
                        <p class="body-2">Please check back later for our delicious offerings!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <div class="cart-notification" id="cartNotification">
        <ion-icon name="checkmark-circle-outline"></ion-icon>
        Item added to cart!
    </div>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    
    <script>
        // Cart management - use same key as main website
        let cart = JSON.parse(localStorage.getItem('quickbite_cart')) || [];
        
        function updateCartCount() {
            const cart = JSON.parse(localStorage.getItem('quickbite_cart')) || [];
            const count = cart.reduce((total, item) => total + item.quantity, 0);
            const cartCountElements = document.querySelectorAll('#cartCount, .cart-count');
            cartCountElements.forEach(element => {
                element.textContent = count;
            });
        }
        
        function addToCart(id, name, price) {
            // Use the same cart key as the main website
            let cart = JSON.parse(localStorage.getItem('quickbite_cart')) || [];
            
            const existingItem = cart.find(item => item.name === name);
            
            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cart.push({
                    id: id,
                    name: name,
                    price: price,
                    image: `../assets/images/menu-${id}.png`, // Default image pattern
                    quantity: 1
                });
            }
            
            localStorage.setItem('quickbite_cart', JSON.stringify(cart));
            updateCartCount();
            showNotification();
        }
        
        function showNotification() {
            const notification = document.getElementById('cartNotification');
            notification.classList.add('show');
            setTimeout(() => {
                notification.classList.remove('show');
            }, 2000);
        }
        
        function searchMenu() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const menuCards = document.querySelectorAll('.menu-card');
            
            menuCards.forEach(card => {
                const dishName = card.dataset.name;
                const category = card.dataset.category;
                
                if (dishName.includes(searchTerm) || category.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
        
        // Search on Enter key
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchMenu();
            }
        });
        
        // Update cart count on page load
        updateCartCount();
    </script>
</body>
</html>