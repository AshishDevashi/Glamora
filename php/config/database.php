<?php
/**
 * Database Configuration
 * 
 * This file contains configuration settings for the database connection.
 * Modify these settings according to your environment.
 */

// Define database connection constants
define('DB_HOST', 'localhost');
define('DB_NAME', 'jewelry_rental');
define('DB_USER', 'root');     // Change this to your MySQL username
define('DB_PASS', '');         // Change this to your MySQL password

/**
 * Get database connection
 * 
 * @return PDO Database connection instance
 */
function getDbConnection() {
    try {
        // Create PDO instance
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ];
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        // Handle connection errors
        error_log("Database Connection Error: " . $e->getMessage());
        die("Database connection failed. Please try again later.");
    }
} 