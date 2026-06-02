<?php
/**
 * Utility class for common functions
 */

class Utility {
    
    /**
     * Validate email address
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate username (alphanumeric and underscore)
     */
    public static function validateUsername($username) {
        return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username);
    }

    /**
     * Validate password strength
     */
    public static function validatePassword($password) {
        // Min 8 chars, at least 1 uppercase, 1 lowercase, 1 number
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $password);
    }

    /**
     * Sanitize user input
     */
    public static function sanitize($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Generate random token
     */
    public static function generateToken($length = 32) {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Format file size
     */
    public static function formatFileSize($bytes) {
        $units = array('B', 'KB', 'MB', 'GB');
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Get relative time (e.g., "2 hours ago")
     */
    public static function getRelativeTime($date) {
        $datetime = new DateTime($date);
        $now = new DateTime();
        $interval = $now->diff($datetime);

        if ($interval->y > 0) {
            return $interval->y . ' year' . ($interval->y > 1 ? 's' : '') . ' ago';
        } elseif ($interval->m > 0) {
            return $interval->m . ' month' . ($interval->m > 1 ? 's' : '') . ' ago';
        } elseif ($interval->d > 0) {
            return $interval->d . ' day' . ($interval->d > 1 ? 's' : '') . ' ago';
        } elseif ($interval->h > 0) {
            return $interval->h . ' hour' . ($interval->h > 1 ? 's' : '') . ' ago';
        } elseif ($interval->i > 0) {
            return $interval->i . ' minute' . ($interval->i > 1 ? 's' : '') . ' ago';
        } else {
            return 'just now';
        }
    }

    /**
     * Validate file upload
     */
    public static function validateFileUpload($file, $max_size = MAX_FILE_SIZE) {
        if (!isset($file['tmp_name']) || !isset($file['size'])) {
            return array('success' => false, 'message' => 'Invalid file');
        }

        if ($file['size'] > $max_size) {
            return array('success' => false, 'message' => 'File too large');
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ALLOWED_EXTENSIONS)) {
            return array('success' => false, 'message' => 'File type not allowed');
        }

        return array('success' => true, 'message' => 'File valid');
    }

    /**
     * Upload file
     */
    public static function uploadFile($file) {
        $validation = self::validateFileUpload($file);
        if (!$validation['success']) {
            return $validation;
        }

        if (!is_dir(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR, 0755, true);
        }

        $filename = time() . '_' . basename($file['name']);
        $filepath = UPLOAD_DIR . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return array('success' => true, 'filename' => $filename, 'path' => $filepath);
        }

        return array('success' => false, 'message' => 'Upload failed');
    }

    /**
     * Send JSON response
     */
    public static function jsonResponse($success, $message = '', $data = array()) {
        return array_merge(
            array('success' => $success, 'message' => $message),
            $data
        );
    }
}
