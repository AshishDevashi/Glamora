<?php
/**
 * Order API Endpoints
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Order.php';
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
        // Get user's orders
        if (!isset($_GET['action']) || $_GET['action'] === 'list') {
            // Require authentication
            $api->requireAuth();
            
            // Get orders
            $orders = $api->order->getUserOrders();
            $api->sendSuccess($orders);
        }
        
        // Get order by number
        else if (isset($_GET['action']) && $_GET['action'] === 'get' && isset($_GET['number'])) {
            // Require authentication
            $api->requireAuth();
            
            // Get order
            $order = $api->order->getOrderByNumber($_GET['number']);
            
            if ($order) {
                $api->sendSuccess($order);
            } else {
                $api->sendError('Order not found', 404);
            }
        }
        
        else {
            $api->sendError('Invalid action');
        }
        break;
    
    case 'POST':
        // Get request body
        $data = $api->getRequestBody();
        
        // Create order
        if (isset($_GET['action']) && $_GET['action'] === 'create') {
            // Require authentication
            $api->requireAuth();
            
            // Validate required fields
            if (!$api->validateRequired($data, ['shipping', 'delivery_id'])) {
                $api->sendError('Shipping information and delivery option are required');
            }
            
            // Create order
            $result = $api->order->createOrder($data['shipping'], $data['delivery_id']);
            
            if ($result['success']) {
                $api->sendSuccess($result, 'Order created successfully');
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