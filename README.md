# QuickBite Restaurant Management System

## Project Overview
QuickBite is a comprehensive web-based restaurant management system built with PHP, MySQL, HTML, CSS, and JavaScript. The system provides separate interfaces for administrators and customers, enabling complete restaurant operations management and online ordering functionality.

## Features

### Customer Features
- **User Registration & Authentication** - Secure account creation and login
- **Menu Browsing** - Interactive menu with categories (breakfast, lunch, dinner, drinks, desserts)
- **Shopping Cart** - Add items, modify quantities, and manage orders
- **Order Management** - Place orders, track status, view history
- **Profile Management** - Update personal information and preferences
- **Order Tracking** - Real-time status updates (pending, confirmed, preparing, ready, completed)

### Admin Features
- **Dashboard** - Overview of orders, revenue, and system statistics
- **Menu Management** - Add, edit, delete menu items with categories and pricing
- **Order Management** - View, accept, reject, and update order status
- **Table Management** - Manage restaurant seating with capacity and location
- **Customer Management** - View customer information and order history
- **Reports** - Generate sales and performance reports
- **Reservations** - Manage table bookings and availability

## Technology Stack
- **Frontend**: HTML5, CSS3, JavaScript (ES6+), Font Awesome Icons
- **Backend**: PHP 8.0+
- **Database**: MySQL 8.0
- **Server**: Apache (XAMPP)
- **Architecture**: MVC Pattern with separation of concerns

## System Requirements
- PHP 8.0 or higher
- MySQL 8.0 or higher
- Apache Web Server
- Modern web browser (Chrome, Firefox, Safari, Edge)

## Installation & Setup

### 1. Prerequisites
- Install XAMPP (includes Apache, MySQL, PHP)
- Ensure ports 80 (Apache) and 3307 (MySQL) are available

### 2. Database Setup
1. Start XAMPP Control Panel
2. Start Apache and MySQL services
3. Open phpMyAdmin (http://localhost/phpmyadmin)
4. Create database named `quickbite_db`
5. Import the database structure from `database/setup.sql`

### 3. Configuration
1. Clone/download project files to `htdocs/quickbite`
2. Update database configuration in `config/database.php` if needed
3. Run `setup_tables.php` to initialize sample data

### 4. Access the System
- **Website**: http://localhost/quickbite
- **Admin Login**: admin@quickbite.com / admin123
- **Customer Login**: customer@test.com / customer123

## Project Structure
```
quickbite/
├── admin/              # Admin panel files
├── api/               # REST API endpoints
├── assets/            # CSS, JS, images
├── auth/              # Authentication system
├── config/            # Database configuration
├── customer/          # Customer dashboard
├── database/          # SQL files
├── index.html         # Main website
└── README.md          # This file
```

## Key Functionalities

### Authentication System
- Role-based access control (Admin/Customer)
- Secure password hashing
- Session management with timeout
- Login attempt monitoring

### Order Processing Workflow
1. Customer browses menu and adds items to cart
2. Customer proceeds to checkout with delivery/pickup options
3. Order is submitted to admin dashboard
4. Admin reviews and confirms order
5. Status updates: Pending → Confirmed → Preparing → Ready → Completed
6. Customer receives real-time status updates

### Security Features
- SQL injection prevention using prepared statements
- XSS protection with input sanitization
- CSRF protection for forms
- Session security with regeneration
- Password strength requirements

## Database Design
The system uses a normalized database structure with the following key tables:
- `users` - User accounts and authentication
- `menu_items` - Restaurant menu with categories and pricing
- `orders` - Order information and status
- `order_items` - Individual items within orders
- `tables` - Restaurant seating management

## Testing
- Manual testing performed on all user workflows
- Cross-browser compatibility verified
- Responsive design tested on multiple devices
- Database operations validated for data integrity

## Future Enhancements
- Payment gateway integration
- Real-time notifications
- Mobile application
- Inventory management
- Customer reviews and ratings

## Contributors
Manyok Gai Chop- Full Stack Developer

## License
This project is developed for academic purposes.

## Support
For technical support or questions manyokg8@gmail.com