-- Database Schema for Jewelry Rental Platform

-- Users Table
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories Table
CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products (Jewelry) Table
CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    category_id INT,
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

-- Rental Periods Table
CREATE TABLE rental_periods (
    period_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    days INT NOT NULL,
    price_multiplier DECIMAL(5, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cart Table
CREATE TABLE cart_items (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT NOT NULL,
    period_id INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    session_id VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id),
    FOREIGN KEY (period_id) REFERENCES rental_periods(period_id)
);

-- Delivery Options Table
CREATE TABLE delivery_options (
    delivery_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders Table
CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    order_number VARCHAR(20) NOT NULL UNIQUE,
    subtotal DECIMAL(10, 2) NOT NULL,
    delivery_fee DECIMAL(10, 2) NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    delivery_id INT NOT NULL,
    shipping_name VARCHAR(100) NOT NULL,
    shipping_email VARCHAR(100) NOT NULL,
    shipping_address VARCHAR(255) NOT NULL,
    shipping_city VARCHAR(100) NOT NULL,
    shipping_state VARCHAR(100) NOT NULL,
    shipping_zip VARCHAR(20) NOT NULL,
    shipping_country VARCHAR(100) NOT NULL,
    shipping_phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (delivery_id) REFERENCES delivery_options(delivery_id)
);

-- Order Items Table
CREATE TABLE order_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    period_id INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id),
    FOREIGN KEY (period_id) REFERENCES rental_periods(period_id)
);

-- Initial Data Insertion

-- Categories
INSERT INTO categories (name) VALUES 
('Necklaces'),
('Earrings'),
('Bracelets'),
('Rings'),
('Watches'),
('Brooches');

-- Rental Periods
INSERT INTO rental_periods (name, days, price_multiplier) VALUES 
('3 Days', 3, 1.0),
('7 Days', 7, 2.0),
('14 Days', 14, 3.5);

-- Delivery Options
INSERT INTO delivery_options (name, description, price) VALUES 
('Standard Delivery', '5-7 business days', 5.99),
('Express Delivery', '2-3 business days', 12.99);

-- Sample Products
INSERT INTO products (name, description, price, image_url, category_id) VALUES 
('Diamond Tennis Bracelet', 'Elegant tennis bracelet featuring 3.00 carats of round brilliant diamonds set in 14K white gold.', 89.99, 'https://images.unsplash.com/photo-1611652022419-a9419f74343d?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80', 3),
('Pearl Necklace', 'Classic 18-inch strand of AAA-grade white South Sea pearls with a 14K gold clasp.', 65.99, 'https://images.unsplash.com/photo-1599643477877-530eb83abc8e?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80', 1),
('Sapphire Stud Earrings', 'Stunning blue sapphire stud earrings totaling 2.00 carats set in 18K white gold with diamond halos.', 75.99, 'https://images.unsplash.com/photo-1588444837495-c6cfeb53f32d?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80', 2),
('Emerald Statement Ring', 'Bold emerald statement ring featuring a 3.50 carat Colombian emerald surrounded by diamonds in platinum.', 99.99, 'https://images.unsplash.com/photo-1605100804763-247f67b3557e?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80', 4),
('Gold Chain Necklace', 'Heavy 24K gold Cuban link chain necklace, 22 inches in length.', 79.99, 'https://images.unsplash.com/photo-1589128777073-263566ae5e4d?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80', 1),
('Ruby Pendant', 'Exquisite ruby pendant featuring a 2.00 carat Burmese ruby surrounded by diamonds on an 18K gold chain.', 85.99, 'https://images.unsplash.com/photo-1602173574767-37ac01994b2a?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80', 1),
('Diamond Hoop Earrings', 'Sparkling diamond hoop earrings with 1.50 carats of diamonds set in 14K white gold.', 69.99, 'https://images.unsplash.com/photo-1629224316810-9d8805b95e76?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80', 2),
('Vintage Brooch', 'Stunning vintage-inspired brooch featuring diamonds, sapphires, and rubies in a floral design.', 59.99, 'https://images.unsplash.com/photo-1620656798932-902cbe3e3f1d?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80', 6),
('Men\'s Luxury Watch', 'Sophisticated men\'s luxury watch with automatic movement, sapphire crystal, and stainless steel bracelet.', 129.99, 'https://images.unsplash.com/photo-1587836374828-4dbafa94cf0e?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80', 5),
('Diamond Tennis Necklace', 'Breathtaking diamond tennis necklace featuring 10.00 carats of round brilliant diamonds in 18K white gold.', 149.99, 'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80', 1),
('Amethyst Cocktail Ring', 'Bold amethyst cocktail ring featuring a 10.00 carat amethyst surrounded by diamonds in 14K rose gold.', 69.99, 'https://images.unsplash.com/photo-1603561591411-07134e71a2a9?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80', 4),
('Pearl Drop Earrings', 'Elegant pearl drop earrings featuring South Sea pearls suspended from diamond-set 18K white gold.', 79.99, 'https://images.unsplash.com/photo-1611107683227-e9060eccd846?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80', 2);

-- Create indexes for performance
CREATE INDEX idx_cart_user ON cart_items(user_id);
CREATE INDEX idx_cart_session ON cart_items(session_id);
CREATE INDEX idx_orders_user ON orders(user_id);
CREATE INDEX idx_orders_number ON orders(order_number);
CREATE INDEX idx_products_category ON products(category_id); 