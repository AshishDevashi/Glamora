<?php
/**
 * Cart API Endpoints
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Cart.php';
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
        // Get cart items
        if (!isset($_GET['action']) || $_GET['action'] === 'items') {
            $items = $api->cart->getCartItems();
            $total = $api->cart->getCartTotal();
            
            $api->sendSuccess([
                'items' => $items,
                'total' => $total
            ]);
        }
        
        else {
            $api->sendError('Invalid action');
        }
        break;
    
    case 'POST':
        // Get request body
        $data = $api->getRequestBody();
        
        // Add item to cart
        if (isset($_GET['action']) && $_GET['action'] === 'add') {
            // Validate required fields
            if (!$api->validateRequired($data, ['product_id', 'period_id'])) {
                $api->sendError('Product ID and rental period are required');
            }
            
            // Add item
            $result = $api->cart->addItem($data['product_id'], $data['period_id']);
            
            if ($result['success']) {
                $api->sendSuccess($result, 'Item added to cart');
            } else {
                $api->sendError($result['message']);
            }
        }
        
        // Remove item from cart
        else if (isset($_GET['action']) && $_GET['action'] === 'remove') {
            // Validate required fields
            if (!$api->validateRequired($data, ['cart_id'])) {
                $api->sendError('Cart item ID is required');
            }
            
            // Remove item
            $result = $api->cart->removeItem($data['cart_id']);
            
            if ($result['success']) {
                $api->sendSuccess($result, 'Item removed from cart');
            } else {
                $api->sendError($result['message']);
            }
        }
        
        // Clear cart
        else if (isset($_GET['action']) && $_GET['action'] === 'clear') {
            // Clear cart
            $result = $api->cart->clearCart();
            
            if ($result['success']) {
                $api->sendSuccess($result, 'Cart cleared');
            } else {
                $api->sendError($result['message']);
            }
        }
        
        else {
            $api->sendError('Invalid action');
        }
        break;
    
    default:
        $api->sendError('Method not allowed', 405);
        break;
} 