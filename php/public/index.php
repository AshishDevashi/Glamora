<?php
/**
 * Frontend Request Handler
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Logger.php';
require_once __DIR__ . '/../classes/Auth.php';

// Create logger instance
$logger = new Logger();

try {
    // Get database connection
    $db = getDbConnection();
    
    // Create auth instance
    $auth = new Auth($db);
    
    // Get request path
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $path = trim($path, '/');
    
    // Handle static files
    if (preg_match('/\.(css|js|jpg|jpeg|png|gif|ico)$/', $path)) {
        $file = __DIR__ . '/' . $path;
        if (file_exists($file)) {
            $mimeType = mime_content_type($file);
            header('Content-Type: ' . $mimeType);
            readfile($file);
            exit();
        }
    }
    
    // Route request
    switch ($path) {
        case '':
        case 'index':
            require_once __DIR__ . '/../views/home.php';
            break;
            
        case 'products':
            require_once __DIR__ . '/../views/products.php';
            break;
            
        case 'product':
            require_once __DIR__ . '/../views/product.php';
            break;
            
        case 'cart':
            require_once __DIR__ . '/../views/cart.php';
            break;
            
        case 'checkout':
            if (!$auth->isLoggedIn()) {
                header('Location: /login');
                exit();
            }
            require_once __DIR__ . '/../views/checkout.php';
            break;
            
        case 'orders':
            if (!$auth->isLoggedIn()) {
                header('Location: /login');
                exit();
            }
            require_once __DIR__ . '/../views/orders.php';
            break;
            
        case 'login':
            if ($auth->isLoggedIn()) {
                header('Location: /');
                exit();
            }
            require_once __DIR__ . '/../views/login.php';
            break;
            
        case 'register':
            if ($auth->isLoggedIn()) {
                header('Location: /');
                exit();
            }
            require_once __DIR__ . '/../views/register.php';
            break;
            
        case 'logout':
            $auth->logout();
            header('Location: /');
            exit();
            break;
            
        default:
            require_once __DIR__ . '/../views/404.php';
    }
    
} catch (Exception $e) {
    $logger->error('Frontend request failed', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    
    require_once __DIR__ . '/../views/500.php';
} 