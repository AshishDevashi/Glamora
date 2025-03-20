<?php
/**
 * API Response Handler Class
 */

class ApiResponse {
    private $status;
    private $data;
    private $message;
    private $errors;
    
    public function __construct($status = 200, $data = null, $message = null, $errors = null) {
        $this->status = $status;
        $this->data = $data;
        $this->message = $message;
        $this->errors = $errors;
    }
    
    public function success($data = null, $message = null) {
        $this->status = 200;
        $this->data = $data;
        $this->message = $message;
        $this->errors = null;
        return $this;
    }
    
    public function created($data = null, $message = 'Resource created successfully') {
        $this->status = 201;
        $this->data = $data;
        $this->message = $message;
        $this->errors = null;
        return $this;
    }
    
    public function noContent($message = 'No content') {
        $this->status = 204;
        $this->data = null;
        $this->message = $message;
        $this->errors = null;
        return $this;
    }
    
    public function badRequest($message = 'Bad request', $errors = null) {
        $this->status = 400;
        $this->data = null;
        $this->message = $message;
        $this->errors = $errors;
        return $this;
    }
    
    public function unauthorized($message = 'Unauthorized') {
        $this->status = 401;
        $this->data = null;
        $this->message = $message;
        $this->errors = null;
        return $this;
    }
    
    public function forbidden($message = 'Forbidden') {
        $this->status = 403;
        $this->data = null;
        $this->message = $message;
        $this->errors = null;
        return $this;
    }
    
    public function notFound($message = 'Resource not found') {
        $this->status = 404;
        $this->data = null;
        $this->message = $message;
        $this->errors = null;
        return $this;
    }
    
    public function methodNotAllowed($message = 'Method not allowed') {
        $this->status = 405;
        $this->data = null;
        $this->message = $message;
        $this->errors = null;
        return $this;
    }
    
    public function conflict($message = 'Resource already exists') {
        $this->status = 409;
        $this->data = null;
        $this->message = $message;
        $this->errors = null;
        return $this;
    }
    
    public function tooManyRequests($message = 'Too many requests') {
        $this->status = 429;
        $this->data = null;
        $this->message = $message;
        $this->errors = null;
        return $this;
    }
    
    public function internalServerError($message = 'Internal server error') {
        $this->status = 500;
        $this->data = null;
        $this->message = $message;
        $this->errors = null;
        return $this;
    }
    
    public function serviceUnavailable($message = 'Service unavailable') {
        $this->status = 503;
        $this->data = null;
        $this->message = $message;
        $this->errors = null;
        return $this;
    }
    
    public function send() {
        // Set status code
        http_response_code($this->status);
        
        // Set headers
        header('Content-Type: application/json');
        
        // Build response array
        $response = [];
        
        if ($this->message) {
            $response['message'] = $this->message;
        }
        
        if ($this->data !== null) {
            $response['data'] = $this->data;
        }
        
        if ($this->errors) {
            $response['errors'] = $this->errors;
        }
        
        // Send response
        echo json_encode($response);
        exit();
    }
    
    public function getStatus() {
        return $this->status;
    }
    
    public function getData() {
        return $this->data;
    }
    
    public function getMessage() {
        return $this->message;
    }
    
    public function getErrors() {
        return $this->errors;
    }
} 