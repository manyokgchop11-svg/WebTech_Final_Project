'use strict';

/**
 * BOOKING SYSTEM
 */

// API endpoints
// Automatically detect the correct API path based on current location
const currentPath = window.location.pathname;
const basePath = currentPath.substring(0, currentPath.lastIndexOf('/'));
const API_BASE_URL = window.location.origin + basePath + '/api';

// Show notification
function showNotification(message, type = 'success') {
  const notification = document.createElement('div');
  notification.className = `notification notification-${type}`;
  notification.innerHTML = `
    <div class="notification-content">
      <ion-icon name="${type === 'success' ? 'checkmark-circle' : 'alert-circle'}"></ion-icon>
      <span>${message}</span>
    </div>
  `;
  
  document.body.appendChild(notification);
  
  setTimeout(() => {
    notification.classList.add('show');
  }, 100);
  
  setTimeout(() => {
    notification.classList.remove('show');
    setTimeout(() => {
      notification.remove();
    }, 300);
  }, 5000);
}

// Handle reservation form submission
const reservationForm = document.querySelector('.reservation-form .form-left');

if (reservationForm) {
  reservationForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('.btn');
    const originalText = submitBtn.querySelector('.text-1').textContent;
    
    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtn.querySelector('.text-1').textContent = 'Booking...';
    submitBtn.querySelector('.text-2').textContent = 'Booking...';
    
    // Get form data
    const formData = {
      name: this.querySelector('input[name="name"]').value,
      phone: this.querySelector('input[name="phone"]').value,
      email: this.querySelector('input[name="email"]')?.value || '',
      number_of_people: parseInt(this.querySelector('select[name="person"]').value),
      reservation_date: this.querySelector('input[name="reservation-date"]').value,
      reservation_time: this.querySelector('select[name="person"]:last-of-type').value,
      message: this.querySelector('textarea[name="message"]').value
    };
    
    try {
      const response = await fetch(`${API_BASE_URL}/book_table.php`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData)
      });
      
      const result = await response.json();
      
      if (result.success) {
        showNotification(result.message, 'success');
        this.reset();
      } else {
        showNotification(result.message || 'Booking failed. Please try again.', 'error');
      }
      
    } catch (error) {
      console.error('Error:', error);
      showNotification('Network error. Please check your connection and try again.', 'error');
    } finally {
      // Re-enable button
      submitBtn.disabled = false;
      submitBtn.querySelector('.text-1').textContent = originalText;
      submitBtn.querySelector('.text-2').textContent = originalText;
    }
  });
}

// Handle newsletter subscription
const newsletterForm = document.querySelector('.footer-brand form');

if (newsletterForm) {
  newsletterForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('.btn');
    const emailInput = this.querySelector('input[name="email_address"]');
    const originalText = submitBtn.querySelector('.text-1').textContent;
    
    submitBtn.disabled = true;
    submitBtn.querySelector('.text-1').textContent = 'Subscribing...';
    submitBtn.querySelector('.text-2').textContent = 'Subscribing...';
    
    const formData = {
      email: emailInput.value
    };
    
    try {
      const response = await fetch(`${API_BASE_URL}/subscribe.php`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData)
      });
      
      const result = await response.json();
      
      if (result.success) {
        showNotification(result.message, 'success');
        emailInput.value = '';
      } else {
        showNotification(result.message || 'Subscription failed. Please try again.', 'error');
      }
      
    } catch (error) {
      console.error('Error:', error);
      showNotification('Network error. Please try again.', 'error');
    } finally {
      submitBtn.disabled = false;
      submitBtn.querySelector('.text-1').textContent = originalText;
      submitBtn.querySelector('.text-2').textContent = originalText;
    }
  });
}

// Set minimum date for reservation (today)
const dateInput = document.querySelector('input[name="reservation-date"]');
if (dateInput) {
  const today = new Date().toISOString().split('T')[0];
  dateInput.setAttribute('min', today);
}


// Handle contact form submission
const contactForm = document.getElementById('contactForm');

if (contactForm) {
  contactForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('.btn');
    const originalText = submitBtn.querySelector('.text-1').textContent;
    
    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtn.querySelector('.text-1').textContent = 'Sending...';
    submitBtn.querySelector('.text-2').textContent = 'Sending...';
    
    // Get form data
    const formData = {
      name: this.querySelector('input[name="contact_name"]').value,
      email: this.querySelector('input[name="contact_email"]').value,
      phone: this.querySelector('input[name="contact_phone"]').value,
      subject: this.querySelector('input[name="contact_subject"]').value,
      message: this.querySelector('textarea[name="contact_message"]').value
    };
    
    try {
      const response = await fetch(`${API_BASE_URL}/contact.php`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData)
      });
      
      const result = await response.json();
      
      if (result.success) {
        showNotification(result.message, 'success');
        this.reset();
      } else {
        showNotification(result.message || 'Failed to send message. Please try again.', 'error');
      }
      
    } catch (error) {
      console.error('Error:', error);
      showNotification('Network error. Please check your connection and try again.', 'error');
    } finally {
      // Re-enable button
      submitBtn.disabled = false;
      submitBtn.querySelector('.text-1').textContent = originalText;
      submitBtn.querySelector('.text-2').textContent = originalText;
    }
  });
}
