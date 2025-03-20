<?php
/**
 * Authentication Class
 * 
 * Handles user authentication, registration, and session management.
 */

class Auth {
    private $db;
    
    /**
     * Constructor
     * 
     * @param PDO $db Database connection instance
     */
    public function __construct($db) {
        $this->db = $db;
        session_start();
    }
    
    /**
     * Register a new user
     * 
     * @param string $name User's full name
     * @param string $email User's email address
     * @param string $password User's password
     * @return array Response with success status and message
     */
    public function register($name, $email, $password) {
        try {
            // Validate inputs
            if (empty($name) || empty($email) || empty($password)) {
                return ['success' => false, 'message' => 'All fields are required'];
            }
            
            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return ['success' => false, 'message' => 'Invalid email format'];
            }
            
            // Check if email already exists
            $stmt = $this->db->prepare("SELECT user_id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Email already registered'];
            }
            
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user
            $stmt = $this->db->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $hashedPassword]);
            
            return ['success' => true, 'message' => 'Registration successful'];
        } catch (PDOException $e) {
            error_log("Registration Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Registration failed. Please try again.'];
        }
    }
    
    /**
     * Log in a user
     * 
     * @param string $email User's email address
     * @param string $password User's password
     * @return array Response with success status and message
     */
    public function login($email, $password) {
        try {
            // Validate inputs
            if (empty($email) || empty($password)) {
                return ['success' => false, 'message' => 'Email and password are required'];
            }
            
            // Get user by email
            $stmt = $this->db->prepare("SELECT user_id, name, email, password FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if (!$user || !password_verify($password, $user['password'])) {
                return ['success' => false, 'message' => 'Invalid email or password'];
            }
            
            // Set session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            
            return ['success' => true, 'message' => 'Login successful'];
        } catch (PDOException $e) {
            error_log("Login Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Login failed. Please try again.'];
        }
    }
    
    /**
     * Log out the current user
     * 
     * @return array Response with success status and message
     */
    public function logout() {
        try {
            // Clear session
            session_unset();
            session_destroy();
            
            return ['success' => true, 'message' => 'Logout successful'];
        } catch (Exception $e) {
            error_log("Logout Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Logout failed. Please try again.'];
        }
    }
    
    /**
     * Check if user is logged in
     * 
     * @return bool True if user is logged in, false otherwise
     */
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Get current user data
     * 
     * @return array|null User data if logged in, null otherwise
     */
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return [
            'user_id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email']
        ];
    }
} 