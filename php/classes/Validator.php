<?php
/**
 * Input Validator Class
 */

class Validator {
    private $errors = [];
    private $data;
    
    public function __construct($data) {
        $this->data = $data;
    }
    
    public function required($field, $message = null) {
        if (!isset($this->data[$field]) || empty($this->data[$field])) {
            $this->errors[$field] = $message ?? "The {$field} field is required";
        }
        return $this;
    }
    
    public function email($field, $message = null) {
        if (isset($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = $message ?? "The {$field} must be a valid email address";
        }
        return $this;
    }
    
    public function min($field, $length, $message = null) {
        if (isset($this->data[$field]) && strlen($this->data[$field]) < $length) {
            $this->errors[$field] = $message ?? "The {$field} must be at least {$length} characters";
        }
        return $this;
    }
    
    public function max($field, $length, $message = null) {
        if (isset($this->data[$field]) && strlen($this->data[$field]) > $length) {
            $this->errors[$field] = $message ?? "The {$field} must not exceed {$length} characters";
        }
        return $this;
    }
    
    public function numeric($field, $message = null) {
        if (isset($this->data[$field]) && !is_numeric($this->data[$field])) {
            $this->errors[$field] = $message ?? "The {$field} must be a number";
        }
        return $this;
    }
    
    public function integer($field, $message = null) {
        if (isset($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_INT)) {
            $this->errors[$field] = $message ?? "The {$field} must be an integer";
        }
        return $this;
    }
    
    public function float($field, $message = null) {
        if (isset($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_FLOAT)) {
            $this->errors[$field] = $message ?? "The {$field} must be a float";
        }
        return $this;
    }
    
    public function url($field, $message = null) {
        if (isset($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_URL)) {
            $this->errors[$field] = $message ?? "The {$field} must be a valid URL";
        }
        return $this;
    }
    
    public function date($field, $format = 'Y-m-d', $message = null) {
        if (isset($this->data[$field])) {
            $date = DateTime::createFromFormat($format, $this->data[$field]);
            if (!$date || $date->format($format) !== $this->data[$field]) {
                $this->errors[$field] = $message ?? "The {$field} must be a valid date in format {$format}";
            }
        }
        return $this;
    }
    
    public function in($field, $values, $message = null) {
        if (isset($this->data[$field]) && !in_array($this->data[$field], $values)) {
            $this->errors[$field] = $message ?? "The {$field} must be one of: " . implode(', ', $values);
        }
        return $this;
    }
    
    public function regex($field, $pattern, $message = null) {
        if (isset($this->data[$field]) && !preg_match($pattern, $this->data[$field])) {
            $this->errors[$field] = $message ?? "The {$field} format is invalid";
        }
        return $this;
    }
    
    public function custom($field, $callback, $message = null) {
        if (isset($this->data[$field]) && !$callback($this->data[$field])) {
            $this->errors[$field] = $message ?? "The {$field} is invalid";
        }
        return $this;
    }
    
    public function sanitize($field, $type = 'string') {
        if (isset($this->data[$field])) {
            switch ($type) {
                case 'string':
                    $this->data[$field] = htmlspecialchars(strip_tags($this->data[$field]));
                    break;
                case 'email':
                    $this->data[$field] = filter_var($this->data[$field], FILTER_SANITIZE_EMAIL);
                    break;
                case 'url':
                    $this->data[$field] = filter_var($this->data[$field], FILTER_SANITIZE_URL);
                    break;
                case 'int':
                    $this->data[$field] = filter_var($this->data[$field], FILTER_SANITIZE_NUMBER_INT);
                    break;
                case 'float':
                    $this->data[$field] = filter_var($this->data[$field], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                    break;
            }
        }
        return $this;
    }
    
    public function isValid() {
        return empty($this->errors);
    }
    
    public function getErrors() {
        return $this->errors;
    }
    
    public function getData() {
        return $this->data;
    }
    
    public function getFirstError() {
        return reset($this->errors);
    }
} 