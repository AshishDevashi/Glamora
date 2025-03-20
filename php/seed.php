<?php
/**
 * Database Seeding Script
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/classes/Logger.php';

// Create logger instance
$logger = new Logger();

try {
    // Get database connection
    $db = getDbConnection();
    
    // Start transaction
    $db->beginTransaction();
    
    // Insert categories
    $categories = [
        ['name' => 'Necklaces', 'description' => 'Beautiful necklaces for any occasion'],
        ['name' => 'Earrings', 'description' => 'Elegant earrings to enhance your look'],
        ['name' => 'Bracelets', 'description' => 'Stylish bracelets to adorn your wrist'],
        ['name' => 'Rings', 'description' => 'Stunning rings to complete your ensemble']
    ];
    
    $stmt = $db->prepare("
        INSERT INTO categories (name, description) 
        VALUES (:name, :description)
    ");
    
    foreach ($categories as $category) {
        $stmt->execute($category);
    }
    
    // Insert rental periods
    $periods = [
        ['name' => '1 Day', 'days' => 1, 'discount' => 0],
        ['name' => '3 Days', 'days' => 3, 'discount' => 10],
        ['name' => '5 Days', 'days' => 5, 'discount' => 15],
        ['name' => '7 Days', 'days' => 7, 'discount' => 20]
    ];
    
    $stmt = $db->prepare("
        INSERT INTO rental_periods (name, days, discount) 
        VALUES (:name, :days, :discount)
    ");
    
    foreach ($periods as $period) {
        $stmt->execute($period);
    }
    
    // Insert delivery options
    $deliveryOptions = [
        ['name' => 'Standard Delivery', 'price' => 5.00, 'days' => 3],
        ['name' => 'Express Delivery', 'price' => 10.00, 'days' => 1],
        ['name' => 'Next Day Delivery', 'price' => 15.00, 'days' => 1]
    ];
    
    $stmt = $db->prepare("
        INSERT INTO delivery_options (name, price, days) 
        VALUES (:name, :price, :days)
    ");
    
    foreach ($deliveryOptions as $option) {
        $stmt->execute($option);
    }
    
    // Insert sample products
    $products = [
        [
            'name' => 'Diamond Necklace',
            'description' => 'Elegant diamond necklace perfect for special occasions',
            'category_id' => 1,
            'base_price' => 100.00,
            'image_url' => '/images/products/diamond-necklace.jpg',
            'stock' => 5
        ],
        [
            'name' => 'Pearl Earrings',
            'description' => 'Classic pearl earrings that never go out of style',
            'category_id' => 2,
            'base_price' => 75.00,
            'image_url' => '/images/products/pearl-earrings.jpg',
            'stock' => 8
        ],
        [
            'name' => 'Gold Bracelet',
            'description' => 'Beautiful gold bracelet with intricate design',
            'category_id' => 3,
            'base_price' => 150.00,
            'image_url' => '/images/products/gold-bracelet.jpg',
            'stock' => 3
        ],
        [
            'name' => 'Sapphire Ring',
            'description' => 'Stunning sapphire ring with diamond accents',
            'category_id' => 4,
            'base_price' => 200.00,
            'image_url' => '/images/products/sapphire-ring.jpg',
            'stock' => 4
        ]
    ];
    
    $stmt = $db->prepare("
        INSERT INTO products (name, description, category_id, base_price, image_url, stock) 
        VALUES (:name, :description, :category_id, :base_price, :image_url, :stock)
    ");
    
    foreach ($products as $product) {
        $stmt->execute($product);
    }
    
    // Insert admin user
    $password = password_hash('admin123', PASSWORD_DEFAULT, ['cost' => HASH_COST]);
    
    $stmt = $db->prepare("
        INSERT INTO users (name, email, password, role) 
        VALUES ('Admin', 'admin@glamora.com', :password, 'admin')
    ");
    
    $stmt->execute(['password' => $password]);
    
    // Commit transaction
    $db->commit();
    
    $logger->info('Database seeded successfully');
    echo "Database seeded successfully\n";
    
} catch (Exception $e) {
    // Rollback transaction
    $db->rollBack();
    
    $logger->error('Database seeding failed', [
        'error' => $e->getMessage()
    ]);
    
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
} 