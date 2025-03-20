<?php
/**
 * API Request Handler
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Logger.php';
require_once __DIR__ . '/../classes/RateLimiter.php';
require_once __DIR__ . '/../classes/ApiResponse.php';
require_once __DIR__ . '/../classes/Validator.php';
require_once __DIR__ . '/../classes/Product.php';
require_once __DIR__ . '/../classes/Cart.php';
require_once __DIR__ . '/../classes/Order.php';
require_once __DIR__ . '/../classes/Auth.php';

// Create logger instance
$logger = new Logger();

try {
    // Get database connection
    $db = getDbConnection();
    
    // Create rate limiter instance
    $rateLimiter = new RateLimiter($db, $logger);
    
    // Get client IP
    $clientIp = $_SERVER['REMOTE_ADDR'];
    
    // Check rate limit
    if (!$rateLimiter->check($clientIp)) {
        $response = new ApiResponse();
        $response->tooManyRequests()->send();
    }
    
    // Get request path
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $path = str_replace('/api/', '', $path);
    $path = trim($path, '/');
    
    // Get request method
    $method = $_SERVER['REQUEST_METHOD'];
    
    // Create API response instance
    $response = new ApiResponse();
    
    // Route request
    switch ($path) {
        case 'auth':
            require_once __DIR__ . '/auth.php';
            break;
            
        case 'products':
            require_once __DIR__ . '/products.php';
            break;
            
        case 'cart':
            require_once __DIR__ . '/cart.php';
            break;
            
        case 'orders':
            require_once __DIR__ . '/orders.php';
            break;
            
        default:
            $response->notFound('API endpoint not found')->send();
    }
    
} catch (Exception $e) {
    $logger->error('API request failed', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    
    $response = new ApiResponse();
    $response->internalServerError()->send();
} 