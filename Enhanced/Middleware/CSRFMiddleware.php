<?php
/**
 * CSRF Protection Middleware
 * Generates and validates CSRF tokens to prevent Cross-Site Request Forgery attacks
 */
class CSRFMiddleware {
    
    /**
     * Generate a CSRF token and store in session
     */
    public static function generateToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Get the hidden input field with CSRF token
     */
    public static function getTokenField() {
        $token = self::generateToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Validate the CSRF token from the submitted form
     */
    public static function validateToken() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) {
                self::rejectRequest();
            }
            if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                self::rejectRequest();
            }
            // Regenerate token after successful validation
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    /**
     * Reject the request if CSRF validation fails
     */
    private static function rejectRequest() {
        http_response_code(403);
        die('Error: Invalid CSRF token. This request has been blocked for security reasons.');
    }
}
?>
