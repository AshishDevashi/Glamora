<?php
/**
 * Authentication API Endpoints
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/Api.php';

// Get database connection
$db = getDbConnection();

// Create API instance
$api = new Api($db);

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Handle request
switch ($method) {
    case 'POST':
        // Get request body
        $data = $api->getRequestBody();
        
        // Handle registration
        if (isset($_GET['action']) && $_GET['action'] === 'register') {
            // Validate required fields
            if (!$api->validateRequired($data, ['name', 'email', 'password'])) {
                $api->sendError('All fields are required');
            }
            
            // Register user
            $result = $api->auth->register($data['name'], $data['email'], $data['password']);
            
            if ($result['success']) {
                $api->sendSuccess($result, 'Registration successful');
            } else {
                $api->sendError($result['message']);
            }
        }
        
        // Handle login
        else if (isset($_GET['action']) && $_GET['action'] === 'login') {
            // Validate required fields
            if (!$api->validateRequired($data, ['email', 'password'])) {
                $api->sendError('Email and password are required');
            }
            
            // Login user
            $result = $api->auth->login($data['email'], $data['password']);
            
            if ($result['success']) {
                $api->sendSuccess($result, 'Login successful');
            } else {
                $api->sendError($result['message']);
            }
        }
        
        // Handle logout
        else if (isset($_GET['action']) && $_GET['action'] === 'logout') {
            // Logout user
            $result = $api->auth->logout();
            
            if ($result['success']) {
                $api->sendSuccess($result, 'Logout successful');
            } else {
                $api->sendError($result['message']);
            }
        }
        
        else {
            $api->sendError('Invalid action');
        }
        break;
    
    case 'GET':
        // Get current user
        if (isset($_GET['action']) && $_GET['action'] === 'user') {
            $user = $api->auth->getCurrentUser();
            
            if ($user) {
                $api->sendSuccess($user);
            } else {
                $api->sendError('Not authenticated', 401);
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