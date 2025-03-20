<?php
/**
 * Order Class
 * 
 * Handles order processing and management.
 */

class Order {
    private $db;
    private $auth;
    private $cart;
    
    /**
     * Constructor
     * 
     * @param PDO $db Database connection instance
     * @param Auth $auth Authentication instance
     * @param Cart $cart Cart instance
     */
    public function __construct($db, $auth, $cart) {
        $this->db = $db;
        $this->auth = $auth;
        $this->cart = $cart;
    }
    
    /**
     * Create a new order
     * 
     * @param array $shippingData Shipping information
     * @param int $deliveryId Delivery option ID
     * @return array Response with success status and message
     */
    public function createOrder($shippingData, $deliveryId) {
        try {
            // Start transaction
            $this->db->beginTransaction();
            
            // Validate shipping data
            if (!$this->validateShippingData($shippingData)) {
                return ['success' => false, 'message' => 'Invalid shipping information'];
            }
            
            // Get cart items and total
            $cartItems = $this->cart->getCartItems();
            $subtotal = $this->cart->getCartTotal();
            
            if (empty($cartItems)) {
                return ['success' => false, 'message' => 'Cart is empty'];
            }
            
            // Get delivery option
            $deliveryOption = $this->getDeliveryOption($deliveryId);
            if (!$deliveryOption) {
                return ['success' => false, 'message' => 'Invalid delivery option'];
            }
            
            // Calculate total
            $total = $subtotal + $deliveryOption['price'];
            
            // Generate order number
            $orderNumber = $this->generateOrderNumber();
            
            // Get user ID
            $userId = $this->auth->isLoggedIn() ? $this->auth->getCurrentUser()['user_id'] : null;
            
            // Create order
            $stmt = $this->db->prepare("
                INSERT INTO orders (
                    user_id, order_number, subtotal, delivery_fee, total, 
                    delivery_id, shipping_name, shipping_email, shipping_address,
                    shipping_city, shipping_state, shipping_zip, shipping_country,
                    shipping_phone
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                )
            ");
            
            $stmt->execute([
                $userId,
                $orderNumber,
                $subtotal,
                $deliveryOption['price'],
                $total,
                $deliveryId,
                $shippingData['name'],
                $shippingData['email'],
                $shippingData['address'],
                $shippingData['city'],
                $shippingData['state'],
                $shippingData['zip'],
                $shippingData['country'],
                $shippingData['phone'] ?? null
            ]);
            
            $orderId = $this->db->lastInsertId();
            
            // Add order items
            foreach ($cartItems as $item) {
                $stmt = $this->db->prepare("
                    INSERT INTO order_items (order_id, product_id, period_id, price)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([
                    $orderId,
                    $item['product_id'],
                    $item['period_id'],
                    $item['price']
                ]);
            }
            
            // Clear cart
            $this->cart->clearCart();
            
            // Commit transaction
            $this->db->commit();
            
            return [
                'success' => true,
                'message' => 'Order created successfully',
                'order_number' => $orderNumber
            ];
        } catch (PDOException $e) {
            // Rollback transaction on error
            $this->db->rollBack();
            error_log("Create Order Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to create order'];
        }
    }
    
    /**
     * Get order by order number
     * 
     * @param string $orderNumber Order number
     * @return array|null Order details or null if not found
     */
    public function getOrderByNumber($orderNumber) {
        try {
            $stmt = $this->db->prepare("
                SELECT o.*, do.name as delivery_option, do.description as delivery_description
                FROM orders o
                JOIN delivery_options do ON o.delivery_id = do.delivery_id
                WHERE o.order_number = ?
            ");
            $stmt->execute([$orderNumber]);
            $order = $stmt->fetch();
            
            if (!$order) {
                return null;
            }
            
            // Get order items
            $order['items'] = $this->getOrderItems($order['order_id']);
            
            return $order;
        } catch (PDOException $e) {
            error_log("Get Order Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get user's orders
     * 
     * @return array Array of user's orders
     */
    public function getUserOrders() {
        try {
            if (!$this->auth->isLoggedIn()) {
                return [];
            }
            
            $userId = $this->auth->getCurrentUser()['user_id'];
            
            $stmt = $this->db->prepare("
                SELECT o.*, do.name as delivery_option
                FROM orders o
                JOIN delivery_options do ON o.delivery_id = do.delivery_id
                WHERE o.user_id = ?
                ORDER BY o.created_at DESC
            ");
            $stmt->execute([$userId]);
            
            $orders = $stmt->fetchAll();
            
            // Get items for each order
            foreach ($orders as &$order) {
                $order['items'] = $this->getOrderItems($order['order_id']);
            }
            
            return $orders;
        } catch (PDOException $e) {
            error_log("Get User Orders Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get order items
     * 
     * @param int $orderId Order ID
     * @return array Array of order items
     */
    private function getOrderItems($orderId) {
        try {
            $stmt = $this->db->prepare("
                SELECT oi.*, p.name, p.description, p.image_url, rp.name as rental_period
                FROM order_items oi
                JOIN products p ON oi.product_id = p.product_id
                JOIN rental_periods rp ON oi.period_id = rp.period_id
                WHERE oi.order_id = ?
            ");
            $stmt->execute([$orderId]);
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get Order Items Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get delivery option
     * 
     * @param int $deliveryId Delivery option ID
     * @return array|null Delivery option details or null if not found
     */
    private function getDeliveryOption($deliveryId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM delivery_options WHERE delivery_id = ?");
            $stmt->execute([$deliveryId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Get Delivery Option Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Validate shipping data
     * 
     * @param array $shippingData Shipping information
     * @return bool True if valid, false otherwise
     */
    private function validateShippingData($shippingData) {
        $required = ['name', 'email', 'address', 'city', 'state', 'zip', 'country'];
        
        foreach ($required as $field) {
            if (empty($shippingData[$field])) {
                return false;
            }
        }
        
        if (!filter_var($shippingData['email'], FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Generate unique order number
     * 
     * @return string Unique order number
     */
    private function generateOrderNumber() {
        $prefix = 'ORD';
        $timestamp = time();
        $random = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6);
        return $prefix . $timestamp . $random;
    }
} 