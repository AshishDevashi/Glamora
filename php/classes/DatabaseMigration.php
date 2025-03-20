<?php
/**
 * Database Migration Class
 */

class DatabaseMigration {
    private $db;
    private $logger;
    private $migrationsPath;
    
    public function __construct($db, $logger) {
        $this->db = $db;
        $this->logger = $logger;
        $this->migrationsPath = __DIR__ . '/../migrations/';
        
        // Create migrations table if it doesn't exist
        $this->createMigrationsTable();
    }
    
    private function createMigrationsTable() {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL,
                batch INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            
            $this->db->exec($sql);
            
        } catch (Exception $e) {
            $this->logger->error('Failed to create migrations table', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    public function run() {
        try {
            // Get all migration files
            $files = glob($this->migrationsPath . '*.sql');
            sort($files);
            
            // Get applied migrations
            $applied = $this->getAppliedMigrations();
            
            // Get pending migrations
            $pending = array_diff($files, $applied);
            
            if (empty($pending)) {
                $this->logger->info('No pending migrations');
                return true;
            }
            
            // Get current batch
            $batch = $this->getCurrentBatch() + 1;
            
            // Run pending migrations
            foreach ($pending as $file) {
                $this->logger->info('Running migration: ' . basename($file));
                
                // Read and execute SQL file
                $sql = file_get_contents($file);
                $this->db->exec($sql);
                
                // Record migration
                $this->recordMigration(basename($file), $batch);
                
                $this->logger->info('Migration completed: ' . basename($file));
            }
            
            return true;
            
        } catch (Exception $e) {
            $this->logger->error('Migration failed', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    public function rollback($batch = null) {
        try {
            // Get migrations to rollback
            if ($batch === null) {
                $batch = $this->getCurrentBatch();
            }
            
            $sql = "SELECT migration FROM migrations WHERE batch = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$batch]);
            $migrations = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            if (empty($migrations)) {
                $this->logger->info('No migrations to rollback');
                return true;
            }
            
            // Rollback migrations
            foreach ($migrations as $migration) {
                $this->logger->info('Rolling back migration: ' . $migration);
                
                // Read and execute rollback SQL file
                $file = $this->migrationsPath . str_replace('.sql', '_rollback.sql', $migration);
                if (file_exists($file)) {
                    $sql = file_get_contents($file);
                    $this->db->exec($sql);
                }
                
                // Remove migration record
                $sql = "DELETE FROM migrations WHERE migration = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$migration]);
                
                $this->logger->info('Rollback completed: ' . $migration);
            }
            
            return true;
            
        } catch (Exception $e) {
            $this->logger->error('Rollback failed', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    public function reset() {
        try {
            // Get all migrations
            $sql = "SELECT migration FROM migrations ORDER BY batch DESC, id DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $migrations = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            if (empty($migrations)) {
                $this->logger->info('No migrations to reset');
                return true;
            }
            
            // Reset all migrations
            foreach ($migrations as $migration) {
                $this->logger->info('Resetting migration: ' . $migration);
                
                // Read and execute rollback SQL file
                $file = $this->migrationsPath . str_replace('.sql', '_rollback.sql', $migration);
                if (file_exists($file)) {
                    $sql = file_get_contents($file);
                    $this->db->exec($sql);
                }
                
                // Remove migration record
                $sql = "DELETE FROM migrations WHERE migration = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$migration]);
                
                $this->logger->info('Reset completed: ' . $migration);
            }
            
            return true;
            
        } catch (Exception $e) {
            $this->logger->error('Reset failed', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    private function getAppliedMigrations() {
        $sql = "SELECT migration FROM migrations";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    private function getCurrentBatch() {
        $sql = "SELECT MAX(batch) as batch FROM migrations";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['batch'] ?? 0;
    }
    
    private function recordMigration($migration, $batch) {
        $sql = "INSERT INTO migrations (migration, batch) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$migration, $batch]);
    }
} 