/**
 * Optimized Cart Management System
 * Designed for better performance and reduced DOM manipulation
 */

class CartManager {
  constructor() {
    this.cart = JSON.parse(localStorage.getItem('quickbite_cart')) || [];
    this.cartCountElements = null;
    this.notificationContainer = null;
    this.isInitialized = false;
    
    // Bind methods to preserve context
    this.orderItem = this.orderItem.bind(this);
    this.addToCart = this.addToCart.bind(this);
    this.updateCartCount = this.updateCartCount.bind(this);
    this.showOrderNotification = this.showOrderNotification.bind(this);
  }
  
  init() {
    if (this.isInitialized) return;
    
    // Cache DOM elements
    this.cartCountElements = document.querySelectorAll('.cart-count');
    
    // Create notification container once
    this.createNotificationContainer();
    
    // Update cart count
    this.updateCartCount();
    
    this.isInitialized = true;
  }
  
  createNotificationContainer() {
    this.notificationContainer = document.createElement('div');
    this.notificationContainer.id = 'notification-container';
    this.notificationContainer.style.cssText = `
      position: fixed;
      top: 100px;
      right: 20px;
      z-index: 10000;
      pointer-events: none;
    `;
    document.body.appendChild(this.notificationContainer);
  }
  
  orderItem(itemName, price, image) {
    try {
      // Add item to cart
      this.addToCart({
        name: itemName,
        price: price,
        image: image,
        quantity: 1
      });
      
      // Show success message
      this.showOrderNotification(`"${itemName}" added to cart!`);
      
      // Store cart state flag for after login
      localStorage.setItem('quickbite_pending_redirect', 'true');
      localStorage.setItem('quickbite_last_added_item', itemName);
      
      // Redirect to login with customer role pre-selected
      setTimeout(() => {
        // Go to login page with customer role pre-selected and redirect to cart
        const redirectUrl = encodeURIComponent('customer/orders.php?tab=cart');
        window.location.href = `auth/login_fixed.php?redirect=${redirectUrl}&role=customer&preselect=customer`;
      }, 1500);
    } catch (error) {
      console.error('Error adding item to cart:', error);
      this.showOrderNotification('Error adding item to cart. Please try again.');
    }
  }
  
  addToCart(item) {
    const existingItemIndex = this.cart.findIndex(cartItem => cartItem.name === item.name);
    
    if (existingItemIndex !== -1) {
      this.cart[existingItemIndex].quantity += 1;
    } else {
      this.cart.push(item);
    }
    
    // Save to localStorage with error handling
    try {
      localStorage.setItem('quickbite_cart', JSON.stringify(this.cart));
      this.updateCartCount();
    } catch (error) {
      console.error('Error saving to localStorage:', error);
    }
  }
  
  updateCartCount() {
    if (!this.cartCountElements) return;
    
    const totalItems = this.cart.reduce((sum, item) => sum + item.quantity, 0);
    
    // Use requestAnimationFrame for better performance
    requestAnimationFrame(() => {
      this.cartCountElements.forEach(element => {
        if (element) element.textContent = totalItems;
      });
    });
  }
  
  showOrderNotification(message) {
    if (!this.notificationContainer) return;
    
    // Simple notification 
    this.notificationContainer.innerHTML = `
      <div class="order-notification show" style="
        background: #28a745;
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        font-size: 16px;
        font-weight: 500;
        pointer-events: auto;
        animation: slideIn 0.3s ease-out;
      ">
        <div class="notification-content">
          <span>✓ ${message}</span>
        </div>
      </div>
      <style>
        @keyframes slideIn {
          from { transform: translateX(100%); opacity: 0; }
          to { transform: translateX(0); opacity: 1; }
        }
      </style>
    `;
    
    // Remove notification after 2 seconds
    setTimeout(() => {
      this.notificationContainer.innerHTML = '';
    }, 2000);
  }
}

// Create global cart manager instance
const cartManager = new CartManager();

// Global function for backward compatibility
function orderItem(itemName, price, image) {
  cartManager.orderItem(itemName, price, image);
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
  cartManager.init();
});