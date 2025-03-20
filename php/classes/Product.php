<?php
/**
 * Product Class
 * 
 * Handles product-related operations including listing, filtering, and details.
 */

class Product {
    private $db;
    
    /**
     * Constructor
     * 
     * @param PDO $db Database connection instance
     */
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Get all products with optional filtering
     * 
     * @param array $filters Array of filters (category, price range, etc.)
     * @param string $sort Sort field and direction
     * @param int $limit Number of items to return
     * @param int $offset Offset for pagination
     * @return array Array of products
     */
    public function getAllProducts($filters = [], $sort = 'name ASC', $limit = 12, $offset = 0) {
        try {
            $sql = "SELECT p.*, c.name as category_name 
                    FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.category_id 
                    WHERE p.active = 1";
            
            $params = [];
            
            // Apply filters
            if (!empty($filters['category'])) {
                $sql .= " AND c.name = ?";
                $params[] = $filters['category'];
            }
            
            if (!empty($filters['min_price'])) {
                $sql .= " AND p.price >= ?";
                $params[] = $filters['min_price'];
            }
            
            if (!empty($filters['max_price'])) {
                $sql .= " AND p.price <= ?";
                $params[] = $filters['max_price'];
            }
            
            if (!empty($filters['search'])) {
                $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
                $searchTerm = "%{$filters['search']}%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            // Add sorting
            $sql .= " ORDER BY " . $sort;
            
            // Add pagination
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get Products Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get product by ID
     * 
     * @param int $productId Product ID
     * @return array|null Product data or null if not found
     */
    public function getProductById($productId) {
        try {
            $stmt = $this->db->prepare("
                SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.category_id 
                WHERE p.product_id = ? AND p.active = 1
            ");
            $stmt->execute([$productId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Get Product Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get all categories
     * 
     * @return array Array of categories
     */
    public function getCategories() {
        try {
            $stmt = $this->db->query("SELECT * FROM categories ORDER BY name");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get Categories Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get rental periods
     * 
     * @return array Array of rental periods
     */
    public function getRentalPeriods() {
        try {
            $stmt = $this->db->query("SELECT * FROM rental_periods ORDER BY days");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get Rental Periods Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Calculate rental price
     * 
     * @param float $basePrice Base price of the product
     * @param int $periodId Rental period ID
     * @return float Calculated rental price
     */
    public function calculateRentalPrice($basePrice, $periodId) {
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
    
    /**
     * Get total number of products matching filters
     * 
     * @param array $filters Array of filters
     * @return int Total number of products
     */
    public function getTotalProducts($filters = []) {
        try {
            $sql = "SELECT COUNT(*) as total 
                    FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.category_id 
                    WHERE p.active = 1";
            
            $params = [];
            
            // Apply filters
            if (!empty($filters['category'])) {
                $sql .= " AND c.name = ?";
                $params[] = $filters['category'];
            }
            
            if (!empty($filters['min_price'])) {
                $sql .= " AND p.price >= ?";
                $params[] = $filters['min_price'];
            }
            
            if (!empty($filters['max_price'])) {
                $sql .= " AND p.price <= ?";
                $params[] = $filters['max_price'];
            }
            
            if (!empty($filters['search'])) {
                $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
                $searchTerm = "%{$filters['search']}%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            
            return $result['total'];
        } catch (PDOException $e) {
            error_log("Get Total Products Error: " . $e->getMessage());
            return 0;
        }
    }
} 