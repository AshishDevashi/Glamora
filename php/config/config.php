<?php
/**
 * Application Configuration
 */

// Application settings
define('APP_NAME', 'Glamora');
define('APP_URL', 'http://localhost:8000');
define('APP_ENV', 'development'); // development, production

// Session settings
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', APP_ENV === 'production');

// Error reporting
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Time zone
date_default_timezone_set('UTC');

// Security settings
define('HASH_COST', 12); // For password_hash()
define('TOKEN_EXPIRY', 86400); // 24 hours in seconds

// File upload settings
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_FILE_TYPES', ['image/jpeg', 'image/png', 'image/gif']);
define('UPLOAD_PATH', __DIR__ . '/../uploads/');

// API settings
define('API_VERSION', 'v1');
define('API_RATE_LIMIT', 100); // requests per minute
define('API_RATE_WINDOW', 60); // seconds

// Logging settings
define('LOG_PATH', __DIR__ . '/../logs/');
define('LOG_LEVEL', APP_ENV === 'development' ? 'debug' : 'error');

// Create necessary directories
$directories = [
    UPLOAD_PATH,
    LOG_PATH,
    __DIR__ . '/../cache/'
];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Initialize session
session_start();

// Set default headers
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// CORS settings
if (APP_ENV === 'development') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
}

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
} 