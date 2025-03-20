<?php
/**
 * Rate Limiter Class
 */

class RateLimiter {
    private $db;
    private $logger;
    private $limit;
    private $window;
    
    public function __construct($db, $logger) {
        $this->db = $db;
        $this->logger = $logger;
        $this->limit = API_RATE_LIMIT;
        $this->window = API_RATE_WINDOW;
    }
    
    public function check($key) {
        try {
            // Get current timestamp
            $now = time();
            
            // Clean up old records
            $this->cleanup($now);
            
            // Get current count
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM rate_limits 
                WHERE `key` = ? AND timestamp > ?
            ");
            
            $stmt->execute([$key, $now - $this->window]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $count = $result['count'];
            
            // Check if limit exceeded
            if ($count >= $this->limit) {
                $this->logger->warning('Rate limit exceeded', [
                    'key' => $key,
                    'count' => $count
                ]);
                
                return false;
            }
            
            // Add new record
            $stmt = $this->db->prepare("
                INSERT INTO rate_limits (`key`, timestamp) 
                VALUES (?, ?)
            ");
            
            $stmt->execute([$key, $now]);
            
            return true;
            
        } catch (Exception $e) {
            $this->logger->error('Rate limit check failed', [
                'error' => $e->getMessage(),
                'key' => $key
            ]);
            
            // In case of error, allow the request
            return true;
        }
    }
    
    private function cleanup($now) {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM rate_limits 
                WHERE timestamp <= ?
            ");
            
            $stmt->execute([$now - $this->window]);
            
        } catch (Exception $e) {
            $this->logger->error('Rate limit cleanup failed', [
                'error' => $e->getMessage()
            ]);
        }
    }
    
    public function getRemaining($key) {
        try {
            $now = time();
            
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM rate_limits 
                WHERE `key` = ? AND timestamp > ?
            ");
            
            $stmt->execute([$key, $now - $this->window]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return max(0, $this->limit - $result['count']);
            
        } catch (Exception $e) {
            $this->logger->error('Failed to get remaining rate limit', [
                'error' => $e->getMessage(),
                'key' => $key
            ]);
            
            return 0;
        }
    }
    
    public function reset($key) {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM rate_limits 
                WHERE `key` = ?
            ");
            
            $stmt->execute([$key]);
            
            return true;
            
        } catch (Exception $e) {
            $this->logger->error('Failed to reset rate limit', [
                'error' => $e->getMessage(),
                'key' => $key
            ]);
            
            return false;
        }
    }
} 