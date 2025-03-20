<?php
/**
 * Logger Class
 */

class Logger {
    private $logFile;
    private $logLevel;
    
    public function __construct($logFile = null) {
        $this->logFile = $logFile ?? LOG_PATH . date('Y-m-d') . '.log';
        $this->logLevel = LOG_LEVEL;
    }
    
    public function debug($message, $context = []) {
        if ($this->shouldLog('debug')) {
            $this->log('DEBUG', $message, $context);
        }
    }
    
    public function info($message, $context = []) {
        if ($this->shouldLog('info')) {
            $this->log('INFO', $message, $context);
        }
    }
    
    public function warning($message, $context = []) {
        if ($this->shouldLog('warning')) {
            $this->log('WARNING', $message, $context);
        }
    }
    
    public function error($message, $context = []) {
        if ($this->shouldLog('error')) {
            $this->log('ERROR', $message, $context);
        }
    }
    
    private function shouldLog($level) {
        $levels = [
            'debug' => 0,
            'info' => 1,
            'warning' => 2,
            'error' => 3
        ];
        
        return $levels[$level] >= $levels[$this->logLevel];
    }
    
    private function log($level, $message, $context = []) {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] [$level] $message";
        
        if (!empty($context)) {
            $logMessage .= ' ' . json_encode($context);
        }
        
        $logMessage .= PHP_EOL;
        
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
    }
    
    public function getLogFile() {
        return $this->logFile;
    }
    
    public function setLogLevel($level) {
        $this->logLevel = $level;
    }
} 