<?php
/**
 * File Uploader Class
 */

class FileUploader {
    private $uploadPath;
    private $maxFileSize;
    private $allowedTypes;
    private $logger;
    
    public function __construct($logger) {
        $this->uploadPath = UPLOAD_PATH;
        $this->maxFileSize = MAX_FILE_SIZE;
        $this->allowedTypes = ALLOWED_FILE_TYPES;
        $this->logger = $logger;
    }
    
    public function upload($file, $subdirectory = '') {
        try {
            // Validate file
            $this->validateFile($file);
            
            // Create subdirectory if it doesn't exist
            $uploadDir = $this->uploadPath . $subdirectory;
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '_' . time() . '.' . $extension;
            $filepath = $uploadDir . $filename;
            
            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                throw new Exception('Failed to move uploaded file');
            }
            
            $this->logger->info('File uploaded successfully', [
                'filename' => $filename,
                'path' => $filepath,
                'size' => $file['size']
            ]);
            
            return [
                'success' => true,
                'filename' => $filename,
                'path' => $filepath,
                'url' => APP_URL . '/uploads/' . $subdirectory . $filename
            ];
            
        } catch (Exception $e) {
            $this->logger->error('File upload failed', [
                'error' => $e->getMessage(),
                'file' => $file['name']
            ]);
            
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    private function validateFile($file) {
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception($this->getUploadErrorMessage($file['error']));
        }
        
        // Check file size
        if ($file['size'] > $this->maxFileSize) {
            throw new Exception('File size exceeds maximum limit of ' . ($this->maxFileSize / 1024 / 1024) . 'MB');
        }
        
        // Check file type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $this->allowedTypes)) {
            throw new Exception('Invalid file type. Allowed types: ' . implode(', ', $this->allowedTypes));
        }
    }
    
    private function getUploadErrorMessage($error) {
        switch ($error) {
            case UPLOAD_ERR_INI_SIZE:
                return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
            case UPLOAD_ERR_FORM_SIZE:
                return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
            case UPLOAD_ERR_PARTIAL:
                return 'The uploaded file was only partially uploaded';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing a temporary folder';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk';
            case UPLOAD_ERR_EXTENSION:
                return 'A PHP extension stopped the file upload';
            default:
                return 'Unknown upload error';
        }
    }
    
    public function delete($filepath) {
        try {
            if (file_exists($filepath)) {
                if (unlink($filepath)) {
                    $this->logger->info('File deleted successfully', ['path' => $filepath]);
                    return true;
                }
            }
            return false;
        } catch (Exception $e) {
            $this->logger->error('File deletion failed', [
                'error' => $e->getMessage(),
                'path' => $filepath
            ]);
            return false;
        }
    }
} 