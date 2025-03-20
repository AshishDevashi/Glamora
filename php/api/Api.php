<?php
/**
 * Base API Class
 * 
 * Handles common API functionality and response formatting.
 */

class Api {
    protected $db;
    protected $auth;
    protected $product;
    protected $cart;
    protected $order;
    
    /**
     * Constructor
     * 
     * @param PDO $db Database connection instance
     */
    public function __construct($db) {
        $this->db = $db;
        $this->auth = new Auth($db);
        $this->product = new Product($db);
        $this->cart = new Cart($db, $this->auth);
        $this->order = new Order($db, $this->auth, $this->cart);
    }
    
    /**
     * Send JSON response
     * 
     * @param mixed $data Response data
     * @param int $status HTTP status code
     */
    protected function sendResponse($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Send error response
     * 
     * @param string $message Error message
     * @param int $status HTTP status code
     */
    protected function sendError($message, $status = 400) {
        $this->sendResponse([
            'success' => false,
            'message' => $message
        ], $status);
    }
    
    /**
     * Send success response
     * 
     * @param mixed $data Response data
     * @param string $message Success message
     */
    protected function sendSuccess($data, $message = '') {
        $response = [
            'success' => true,
            'data' => $data
        ];
        
        if ($message) {
            $response['message'] = $message;
        }
        
        $this->sendResponse($response);
    }
    
    /**
     * Get request body
     * 
     * @return array Request body data
     */
    protected function getRequestBody() {
        $body = file_get_contents('php://input');
        return json_decode($body, true) ?? [];
    }
    
    /**
     * Validate required fields
     * 
     * @param array $data Data to validate
     * @param array $required Required fields
     * @return bool True if valid, false otherwise
     */
    protected function validateRequired($data, $required) {
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * Require authentication
     */
    protected function requireAuth() {
        if (!$this->auth->isLoggedIn()) {
            $this->sendError('Authentication required', 401);
        }
    }
} 