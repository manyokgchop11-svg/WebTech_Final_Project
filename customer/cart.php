<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: ../auth/login_fixed.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - QuickBite</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/customer.css">
    <style>
        .cart-page {
            background: var(--smoky-black-1);
            min-height: 100vh;
            padding-top: 100px;
        }
        
        .cart-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 30px;
        }
        
        .page-title {
            color: var(--gold-crayola);
            font-size: 3rem;
            margin-bottom: 40px;
            text-align: center;
        }
        
        .cart-layout {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 40px;
        }
        
        .cart-items {
            background: var(--eerie-black-2);
            border: 1px solid var(--white-alpha-10);
            border-radius: 20px;
            padding: 30px;
        }
        
        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 25px 0;
            border-bottom: 1px solid var(--white-alpha-10);
        }
        
        .cart-item:last-child {
            border-bottom: none;
        }
        
        .item-info h3 {
            color: var(--gold-crayola);
            font-size: 1.8rem;
            margin-bottom: 8px;
        }
        
        .item-info p {
            color: var(--quick-silver);
            font-size: 1.4rem;
        }
        
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .qty-btn {
            background: var(--gold-crayola);
            color: var(--smoky-black-1);
            border: none;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            cursor: pointer;
            font-weight: bold;
            font-size: 1.6rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .qty-btn:hover {
            background: var(--white);
            transform: scale(1.1);
        }
        
        .quantity {
            min-width: 40px;
            text-align: center;
            font-weight: bold;
            color: var(--white);
            font-size: 1.6rem;
        }
        
        .item-total {
            color: var(--gold-crayola);
            font-weight: bold;
            font-size: 1.8rem;
            min-width: 120px;
            text-align: right;
        }
        
        .remove-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 8px;
            cursor: pointer;
            margin-left: 15px;
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }
        
        .remove-btn:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }
        
        .cart-summary {
            background: var(--eerie-black-2);
            border: 1px solid var(--white-alpha-10);
            border-radius: 20px;
            padding: 30px;
            height: fit-content;
        }
        
        .cart-summary h2 {
            color: var(--gold-crayola);
            font-size: 2.4rem;
            margin-bottom: 25px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            color: var(--quick-silver);
            font-size: 1.6rem;
        }
        
        .summary-row.total {
            font-size: 2rem;
            font-weight: bold;
            color: var(--gold-crayola);
            border-top: 1px solid var(--white-alpha-10);
            padding-top: 15px;
            margin-top: 20px;
        }
        
        .checkout-btn {
            width: 100%;
            background: var(--gold-crayola);
            color: var(--smoky-black-1);
            border: none;
            padding: 18px;
            border-radius: 12px;
            font-size: 1.8rem;
            font-weight: bold;
            cursor: pointer;
            margin-top: 25px;
            transition: all 0.3s ease;
        }
        
        .checkout-btn:hover {
            background: var(--white);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }
        
        .checkout-btn:disabled {
            background: var(--white-alpha-10);
            color: var(--quick-silver);
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .empty-cart {
            text-align: center;
            padding: 80px 20px;
            color: var(--quick-silver);
        }
        
        .empty-cart h3 {
            color: var(--gold-crayola);
            font-size: 2.4rem;
            margin-bottom: 20px;
        }
        
        .empty-cart p {
            font-size: 1.6rem;
            margin-bottom: 30px;
        }
        
        .btn {
            background: var(--gold-crayola);
            color: var(--smoky-black-1);
            padding: 15px 30px;
            border: none;
            border-radius: 12px;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.6rem;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            background: var(--white);
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .cart-layout {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            
            .cart-container {
                padding: 20px 15px;
            }
            
            .cart-item {
                flex-direction: column;
                gap: 20px;
                align-items: flex-start;
            }
            
            .quantity-controls {
                width: 100%;
                justify-content: space-between;
            }
        }
    </style>
</head>
<body class="customer-page">
    <?php include 'includes/header_new.php'; ?>

    <main class="cart-page">
        <div class="cart-container">
            <h1 class="page-title">Shopping Cart</h1>

            <div class="cart-layout">
                <div class="cart-items">
                    <div id="cartItemsContainer">
                        <!-- Cart items will be loaded here by JavaScript -->
                    </div>
                </div>

                <div class="cart-summary">
                    <h2>Order Summary</h2>
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span id="subtotal">0 SSP</span>
                    </div>
                    <div class="summary-row">
                        <span>Tax (10%):</span>
                        <span id="tax">0 SSP</span>
                    </div>
                    <div class="summary-row total">
                        <span>Total:</span>
                        <span id="total">0 SSP</span>
                    </div>
                    <button class="checkout-btn" id="checkoutBtn" onclick="proceedToCheckout()">
                        Proceed to Checkout
                    </button>
                </div>
            </div>
        </div>
    </main>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    
    <script>
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        
        function updateCartDisplay() {
            const container = document.getElementById('cartItemsContainer');
            
            if (cart.length === 0) {
                container.innerHTML = `
                    <div class="empty-cart">
                        <h3>Your cart is empty</h3>
                        <p>Add some delicious items from our menu!</p>
                        <a href="menu.php" class="btn">Browse Menu</a>
                    </div>
                `;
                updateSummary(0);
                return;
            }
            
            let html = '';
            cart.forEach((item, index) => {
                html += `
                    <div class="cart-item">
                        <div class="item-info">
                            <h3>${item.name}</h3>
                            <p>${item.price.toFixed(0)} SSP each</p>
                        </div>
                        <div class="quantity-controls">
                            <button class="qty-btn" onclick="updateQuantity(${index}, -1)">
                                <ion-icon name="remove-outline"></ion-icon>
                            </button>
                            <span class="quantity">${item.quantity}</span>
                            <button class="qty-btn" onclick="updateQuantity(${index}, 1)">
                                <ion-icon name="add-outline"></ion-icon>
                            </button>
                            <span class="item-total">${(item.price * item.quantity).toFixed(0)} SSP</span>
                            <button class="remove-btn" onclick="removeItem(${index})">
                                <ion-icon name="trash-outline"></ion-icon>
                            </button>
                        </div>
                    </div>
                `;
            });
            
            container.innerHTML = html;
            
            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            updateSummary(subtotal);
        }
        
        function updateQuantity(index, change) {
            cart[index].quantity += change;
            
            if (cart[index].quantity <= 0) {
                cart.splice(index, 1);
            }
            
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartDisplay();
            updateHeaderCartCount();
        }
        
        function removeItem(index) {
            cart.splice(index, 1);
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartDisplay();
            updateHeaderCartCount();
        }
        
        function updateSummary(subtotal) {
            const tax = subtotal * 0.1;
            const total = subtotal + tax;
            
            document.getElementById('subtotal').textContent = subtotal.toFixed(0) + ' SSP';
            document.getElementById('tax').textContent = tax.toFixed(0) + ' SSP';
            document.getElementById('total').textContent = total.toFixed(0) + ' SSP';
            
            const checkoutBtn = document.getElementById('checkoutBtn');
            checkoutBtn.disabled = cart.length === 0;
        }
        
        function updateHeaderCartCount() {
            const count = cart.reduce((total, item) => total + item.quantity, 0);
            const cartCountElements = document.querySelectorAll('#cartCount');
            cartCountElements.forEach(element => {
                element.textContent = count;
            });
        }
        
        function proceedToCheckout() {
            if (cart.length === 0) {
                alert('Your cart is empty!');
                return;
            }
            
            // Store cart data for checkout
            localStorage.setItem('checkoutCart', JSON.stringify(cart));
            window.location.href = 'checkout.php';
        }
        
        // Initialize cart display
        updateCartDisplay();
        updateHeaderCartCount();
    </script>
</body>
</html>