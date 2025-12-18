-- QuickBite Restaurant Management System Database Setup

-- Drop existing tables if they exist (in correct order to avoid foreign key constraints)
DROP TABLE IF EXISTS password_resets;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS customer_addresses;
DROP TABLE IF EXISTS contact_messages;
DROP TABLE IF EXISTS newsletter_subscribers;
DROP TABLE IF EXISTS reservations;
DROP TABLE IF EXISTS tables;
DROP TABLE IF EXISTS menu_items;
DROP TABLE IF EXISTS users;

-- Create users table for authentication
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    role ENUM('admin', 'customer') DEFAULT 'customer',
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create menu items table
CREATE TABLE menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    category ENUM('breakfast', 'lunch', 'dinner', 'drinks', 'desserts', 'appetizers') NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255),
    is_available BOOLEAN DEFAULT TRUE,
    is_featured BOOLEAN DEFAULT FALSE,
    preparation_time INT DEFAULT 15,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_available (is_available)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create tables availability table
CREATE TABLE tables (
    id INT AUTO_INCREMENT PRIMARY KEY,
    table_number VARCHAR(20) NOT NULL UNIQUE,
    capacity INT NOT NULL,
    location ENUM('indoor', 'outdoor', 'vip') DEFAULT 'indoor',
    status ENUM('available', 'occupied', 'reserved', 'maintenance') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create customer addresses table
CREATE TABLE customer_addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    address_type ENUM('home', 'work', 'other') DEFAULT 'home',
    street_address VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100),
    postal_code VARCHAR(20),
    country VARCHAR(100) DEFAULT 'South Sudan',
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    is_default BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create orders table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_number VARCHAR(50) NOT NULL UNIQUE,
    order_type ENUM('dine-in', 'takeaway', 'delivery', 'pickup') DEFAULT 'pickup',
    status ENUM('pending', 'confirmed', 'preparing', 'ready', 'out-for-delivery', 'delivered', 'cancelled', 'completed') DEFAULT 'pending',
    subtotal DECIMAL(10, 2) NOT NULL DEFAULT 0,
    tax_amount DECIMAL(10, 2) NOT NULL DEFAULT 0,
    total_amount DECIMAL(10, 2) NOT NULL,
    delivery_fee DECIMAL(10, 2) DEFAULT 0,
    delivery_address TEXT,
    delivery_distance DECIMAL(10, 2),
    estimated_delivery_time INT,
    special_instructions TEXT,
    payment_method ENUM('cash', 'card', 'mobile-money') DEFAULT 'cash',
    payment_status ENUM('pending', 'paid', 'refunded') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_status (status),
    INDEX idx_order_number (order_number),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create order items table
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    menu_item_id INT,
    item_name VARCHAR(100) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    unit_price DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    special_requests TEXT,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE SET NULL,
    INDEX idx_order (order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create reservations table
CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    number_of_people INT NOT NULL,
    table_id INT,
    reservation_date DATE NOT NULL,
    reservation_time TIME NOT NULL,
    message TEXT,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (table_id) REFERENCES tables(id) ON DELETE SET NULL,
    INDEX idx_date (reservation_date),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create newsletter subscribers table
CREATE TABLE newsletter_subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create contact messages table
CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create password resets table
CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_email (email),
    INDEX idx_token (token),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin user (password: admin123)
INSERT INTO users (username, email, password, full_name, role, status) VALUES
('admin', 'admin@quickbite.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin', 'active');

-- Insert sample customer user (password: customer123)
INSERT INTO users (username, email, password, full_name, role, status) VALUES
('customer', 'customer@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Test Customer', 'customer', 'active');

-- Insert sample menu items with South Sudanese Pound prices
INSERT INTO menu_items (name, description, category, price, image_url, is_available, is_featured) VALUES
-- South Sudanese Breakfast Items
('Kibab Salad', 'Tomatoes, green bell pepper, sliced cucumber onion, olives, and feta cheese', 'breakfast', 5500.00, './assets/images/menu-1.png', TRUE, TRUE),
('Kwaja', 'Vegetables, cheeses, ground meats, tomato sauce, seasonings and spices', 'breakfast', 4500.00, './assets/images/menu-2.png', TRUE, TRUE),
('Stewed Pumpkin', 'Traditional South Sudanese pumpkin stew with spices', 'breakfast', 5000.00, './assets/images/menu-3.png', TRUE, TRUE),

-- Main Dishes
('Grilled Beef', 'Premium grilled beef with traditional spices and vegetables', 'lunch', 5000.00, './assets/images/menu-4.png', TRUE, TRUE),
('Stuffed Okra', 'Traditional okra stuffed with meat and rice', 'dinner', 5000.00, './assets/images/menu-5.png', TRUE, TRUE),
('Mad Fish', 'Fresh fish prepared with South Sudanese spices', 'dinner', 10000.00, './assets/images/menu-6.png', TRUE, TRUE),

-- Additional Menu Items
('Ful Medames', 'Traditional fava beans with spices', 'breakfast', 3000.00, './assets/images/menu-1.png', TRUE, FALSE),
('Asida', 'Traditional porridge with milk and sugar', 'breakfast', 2500.00, './assets/images/menu-2.png', TRUE, FALSE),
('Grilled Chicken', 'Marinated grilled chicken with rice', 'lunch', 4500.00, './assets/images/menu-3.png', TRUE, FALSE),
('Lamb Stew', 'Tender lamb stew with vegetables', 'dinner', 6000.00, './assets/images/menu-4.png', TRUE, FALSE),

-- Drinks
('Fresh Orange Juice', 'Freshly squeezed orange juice', 'drinks', 1500.00, './assets/images/menu-5.png', TRUE, FALSE),
('Mango Juice', 'Fresh mango juice', 'drinks', 1800.00, './assets/images/menu-6.png', TRUE, FALSE),
('Tamarind Juice', 'Traditional tamarind drink', 'drinks', 1200.00, './assets/images/menu-1.png', TRUE, FALSE),
('Hibiscus Tea', 'Traditional hibiscus tea', 'drinks', 800.00, './assets/images/menu-2.png', TRUE, FALSE),
('Coca Cola', 'Chilled Coca Cola 330ml', 'drinks', 500.00, './assets/images/menu-3.png', TRUE, FALSE),
('Mineral Water', 'Bottled mineral water 500ml', 'drinks', 300.00, './assets/images/menu-4.png', TRUE, FALSE),
('Arabic Coffee', 'Traditional Arabic coffee', 'drinks', 1000.00, './assets/images/menu-5.png', TRUE, FALSE),
('Tea with Milk', 'Hot tea with fresh milk', 'drinks', 600.00, './assets/images/menu-6.png', TRUE, FALSE),

-- Desserts
('Baklava', 'Traditional Middle Eastern pastry', 'desserts', 2000.00, './assets/images/menu-1.png', TRUE, FALSE),
('Date Cookies', 'Sweet cookies made with dates', 'desserts', 1500.00, './assets/images/menu-2.png', TRUE, FALSE),
('Fruit Salad', 'Fresh seasonal fruits', 'desserts', 1800.00, './assets/images/menu-3.png', TRUE, FALSE),
('Rice Pudding', 'Creamy rice pudding with cinnamon', 'desserts', 1200.00, './assets/images/menu-4.png', TRUE, FALSE);

-- Insert sample tables
INSERT INTO tables (table_number, capacity, location, status) VALUES
('T01', 2, 'indoor', 'available'),
('T02', 2, 'indoor', 'available'),
('T03', 4, 'indoor', 'available'),
('T04', 4, 'indoor', 'available'),
('T05', 6, 'indoor', 'available'),
('T06', 6, 'indoor', 'available'),
('T07', 8, 'indoor', 'available'),
('T08', 2, 'outdoor', 'available'),
('T09', 4, 'outdoor', 'available'),
('T10', 6, 'vip', 'available'),
('T11', 4, 'outdoor', 'available'),
('T12', 8, 'vip', 'available');

-- Insert sample orders for demonstration
INSERT INTO orders (user_id, order_number, order_type, status, subtotal, tax_amount, total_amount, payment_method, delivery_address, special_instructions) VALUES
(2, 'QB001', 'delivery', 'completed', 9500.00, 950.00, 10450.00, 'cash', 'Juba, Sherikaat Area, Block 8', 'Please call when you arrive'),
(2, 'QB002', 'pickup', 'pending', 6000.00, 600.00, 6600.00, 'cash', '', 'Extra spicy please'),
(2, 'QB003', 'delivery', 'preparing', 15000.00, 1500.00, 16500.00, 'mobile-money', 'Juba, Munuki Area', 'Deliver to the blue gate');

-- Insert sample order items
INSERT INTO order_items (order_id, menu_item_id, item_name, quantity, unit_price, subtotal) VALUES
-- Order 1 items
(1, 1, 'Kibab Salad', 1, 5500.00, 5500.00),
(1, 4, 'Grilled Beef', 1, 5000.00, 5000.00),
-- Order 2 items  
(2, 2, 'Kwaja', 1, 4500.00, 4500.00),
(2, 11, 'Fresh Orange Juice', 1, 1500.00, 1500.00),
-- Order 3 items
(3, 6, 'Mad Fish', 1, 10000.00, 10000.00),
(3, 3, 'Stewed Pumpkin', 1, 5000.00, 5000.00);

-- Insert sample reservations
INSERT INTO reservations (user_id, name, phone, email, number_of_people, table_id, reservation_date, reservation_time, message, status) VALUES
(2, 'John Doe', '+211922123456', 'john@example.com', 4, 3, '2025-12-20', '19:00:00', 'Birthday celebration', 'confirmed'),
(2, 'Jane Smith', '+211922654321', 'jane@example.com', 2, 1, '2025-12-18', '12:30:00', 'Business lunch', 'pending');

-- Insert sample contact messages
INSERT INTO contact_messages (name, email, phone, subject, message, status) VALUES
('Michael Johnson', 'michael@example.com', '+211922111222', 'Great Service', 'I had an amazing experience at your restaurant. The food was delicious!', 'read'),
('Sarah Wilson', 'sarah@example.com', '+211922333444', 'Delivery Question', 'Do you deliver to Gudele area? What are your delivery charges?', 'new'),
('David Brown', 'david@example.com', '+211922555666', 'Catering Service', 'I would like to inquire about catering services for a wedding event.', 'replied');

-- Insert sample newsletter subscribers
INSERT INTO newsletter_subscribers (email) VALUES
('subscriber1@example.com'),
('subscriber2@example.com'),
('subscriber3@example.com'),
('customer@test.com'),
('foodlover@example.com');

-- Show success message
SELECT 'QuickBite Database Setup Complete!' as Status,
       'Tables created successfully' as Message,
       'Default admin: admin@quickbite.com / admin123' as Admin_Login,
       'Default customer: customer@test.com / customer123' as Customer_Login;
