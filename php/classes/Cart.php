<?php
/**
 * Cart Class
 * 
 * Handles shopping cart operations including adding, removing, and updating items.
 */

class Cart {
    private $db;
    private $auth;
    
    /**
     * Constructor
     * 
     * @param PDO $db Database connection instance
     * @param Auth $auth Authentication instance
     */
    public function __construct($db, $auth) {
        $this->db = $db;
        $this->auth = $auth;
    }
    
    /**
     * Add item to cart
     * 
     * @param int $productId Product ID
     * @param int $periodId Rental period ID
     * @return array Response with success status and message
     */
    public function addItem($productId, $periodId) {
        try {
            // Get product details
            $product = $this->getProductDetails($productId);
            if (!$product) {
                return ['success' => false, 'message' => 'Product not found'];
            }
            
            // Calculate rental price
            $price = $this->calculateRentalPrice($product['price'], $periodId);
            
            // Get user ID or session ID
            $userId = $this->auth->isLoggedIn() ? $this->auth->getCurrentUser()['user_id'] : null;
            $sessionId = !$userId ? session_id() : null;
            
            // Check if item already exists in cart
            $stmt = $this->db->prepare("
                SELECT cart_id FROM cart_items 
                WHERE product_id = ? AND period_id = ? 
                AND ((user_id = ? AND user_id IS NOT NULL) OR (session_id = ? AND session_id IS NOT NULL))
            ");
            $stmt->execute([$productId, $periodId, $userId, $sessionId]);
            
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Item already in cart'];
            }
            
            // Add item to cart
            $stmt = $this->db->prepare("
                INSERT INTO cart_items (user_id, product_id, period_id, price, session_id) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$userId, $productId, $periodId, $price, $sessionId]);
            
            return ['success' => true, 'message' => 'Item added to cart'];
        } catch (PDOException $e) {
            error_log("Add to Cart Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to add item to cart'];
        }
    }
    
    /**
     * Remove item from cart
     * 
     * @param int $cartId Cart item ID
     * @return array Response with success status and message
     */
    public function removeItem($cartId) {
        try {
            // Get user ID or session ID
            $userId = $this->auth->isLoggedIn() ? $this->auth->getCurrentUser()['user_id'] : null;
            $sessionId = !$userId ? session_id() : null;
            
            // Delete item from cart
            $stmt = $this->db->prepare("
                DELETE FROM cart_items 
                WHERE cart_id = ? 
                AND ((user_id = ? AND user_id IS NOT NULL) OR (session_id = ? AND session_id IS NOT NULL))
            ");
            $stmt->execute([$cartId, $userId, $sessionId]);
            
            if ($stmt->rowCount() === 0) {
                return ['success' => false, 'message' => 'Item not found in cart'];
            }
            
            return ['success' => true, 'message' => 'Item removed from cart'];
        } catch (PDOException $e) {
            error_log("Remove from Cart Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to remove item from cart'];
        }
    }
    
    /**
     * Get cart items
     * 
     * @return array Array of cart items with product details
     */
    public function getCartItems() {
        try {
            // Get user ID or session ID
            $userId = $this->auth->isLoggedIn() ? $this->auth->getCurrentUser()['user_id'] : null;
            $sessionId = !$userId ? session_id() : null;
            
            // Get cart items with product details
            $sql = "SELECT ci.*, p.name, p.description, p.image_url, rp.name as rental_period 
                    FROM cart_items ci 
                    JOIN products p ON ci.product_id = p.product_id 
                    JOIN rental_periods rp ON ci.period_id = rp.period_id 
                    WHERE (ci.user_id = ? AND ci.user_id IS NOT NULL) 
                    OR (ci.session_id = ? AND ci.session_id IS NOT NULL)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId, $sessionId]);
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get Cart Items Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get cart total
     * 
     * @return float Total price of all items in cart
     */
    public function getCartTotal() {
        try {
            // Get user ID or session ID
            $userId = $this->auth->isLoggedIn() ? $this->auth->getCurrentUser()['user_id'] : null;
            $sessionId = !$userId ? session_id() : null;
            
            // Calculate total
            $stmt = $this->db->prepare("
                SELECT SUM(price) as total 
                FROM cart_items 
                WHERE ((user_id = ? AND user_id IS NOT NULL) OR (session_id = ? AND session_id IS NOT NULL))
            ");
            $stmt->execute([$userId, $sessionId]);
            $result = $stmt->fetch();
            
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Get Cart Total Error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Clear cart
     * 
     * @return array Response with success status and message
     */
    public function clearCart() {
        try {
            // Get user ID or session ID
            $userId = $this->auth->isLoggedIn() ? $this->auth->getCurrentUser()['user_id'] : null;
            $sessionId = !$userId ? session_id() : null;
            
            // Delete all items from cart
            $stmt = $this->db->prepare("
                DELETE FROM cart_items 
                WHERE ((user_id = ? AND user_id IS NOT NULL) OR (session_id = ? AND session_id IS NOT NULL))
            ");
            $stmt->execute([$userId, $sessionId]);
            
            return ['success' => true, 'message' => 'Cart cleared'];
        } catch (PDOException $e) {
            error_log("Clear Cart Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to clear cart'];
        }
    }
    
    /**
     * Get product details
     * 
     * @param int $productId Product ID
     * @return array|null Product details or null if not found
     */
    private function getProductDetails($productId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM products WHERE product_id = ? AND active = 1");
            $stmt->execute([$productId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Get Product Details Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Calculate rental price
     * 
     * @param float $basePrice Base price of the product
     * @param int $periodId Rental period ID
     * @return float Calculated rental price
     */
    private function calculateRentalPrice($basePrice, $periodId) {
        try {
            $stmt = $this->db->prepare("SELECT price_multiplier FROM rental_periods WHERE period_id = ?");
            $stmt->execute([$periodId]);
            $period = $stmt->fetch();
            
            if ($period) {
                return $basePrice * $period['price_multiplier'];
            }
            
            return $basePrice;
        } catch (PDOException $e) {
            error_log("Calculate Rental Price Error: " . $e->getMessage());
            return $basePrice;
        }
    }
} 