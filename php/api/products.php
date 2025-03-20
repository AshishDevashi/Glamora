<?php
/**
 * Product API Endpoints
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Product.php';
require_once __DIR__ . '/Api.php';

// Get database connection
$db = getDbConnection();

// Create API instance
$api = new Api($db);

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Handle request
switch ($method) {
    case 'GET':
        // Get all products
        if (!isset($_GET['action']) || $_GET['action'] === 'list') {
            // Get filters from query parameters
            $filters = [
                'category' => $_GET['category'] ?? null,
                'min_price' => $_GET['min_price'] ?? null,
                'max_price' => $_GET['max_price'] ?? null,
                'search' => $_GET['search'] ?? null
            ];
            
            // Get sorting
            $sort = $_GET['sort'] ?? 'name ASC';
            
            // Get pagination
            $page = max(1, intval($_GET['page'] ?? 1));
            $limit = max(1, min(50, intval($_GET['limit'] ?? 12)));
            $offset = ($page - 1) * $limit;
            
            // Get products
            $products = $api->product->getAllProducts($filters, $sort, $limit, $offset);
            
            // Get total count
            $total = $api->product->getTotalProducts($filters);
            
            $api->sendSuccess([
                'products' => $products,
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'pages' => ceil($total / $limit)
            ]);
        }
        
        // Get product by ID
        else if (isset($_GET['action']) && $_GET['action'] === 'get' && isset($_GET['id'])) {
            $product = $api->product->getProductById($_GET['id']);
            
            if ($product) {
                $api->sendSuccess($product);
            } else {
                $api->sendError('Product not found', 404);
            }
        }
        
        // Get categories
        else if (isset($_GET['action']) && $_GET['action'] === 'categories') {
            $categories = $api->product->getCategories();
            $api->sendSuccess($categories);
        }
        
        // Get rental periods
        else if (isset($_GET['action']) && $_GET['action'] === 'rental-periods') {
            $periods = $api->product->getRentalPeriods();
            $api->sendSuccess($periods);
        }
        
        else {
            $api->sendError('Invalid action');
        }
        break;
    
    default:
        $api->sendError('Method not allowed', 405);
        break;
} 