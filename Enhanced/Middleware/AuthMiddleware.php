<?php
/**
 * Authentication and Authorization Middleware
 * Handles session validation, authentication checks, and role-based access control
 */
class AuthMiddleware {

    /**
     * Check if the user is authenticated
     */
    public static function requireLogin() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }

        // SECURE: Check session timeout
        if (isset($_SESSION['last_activity'])) {
            $inactive = time() - $_SESSION['last_activity'];
            if ($inactive > SESSION_LIFETIME) {
                session_unset();
                session_destroy();
                header('Location: index.php?page=login&timeout=1');
                exit;
            }
        }
        $_SESSION['last_activity'] = time();

        // SECURE: Validate session integrity (IP and User-Agent binding)
        if (isset($_SESSION['ip_address']) && $_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR']) {
            self::logoutHijackedSession();
        }
        if (isset($_SESSION['user_agent']) && $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
            self::logoutHijackedSession();
        }
    }

    /**
     * Check if user has admin role
     */
    public static function requireAdmin() {
        self::requireLogin();
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            http_response_code(403);
            die('Access Denied: You do not have permission to access this page.');
        }
    }

    /**
     * Check if current user is the owner of a resource or an admin
     */
    public static function requireOwnerOrAdmin($resourceUserId) {
        self::requireLogin();
        if ($_SESSION['user_id'] != $resourceUserId && $_SESSION['role'] !== 'admin') {
            http_response_code(403);
            die('Access Denied: You do not have permission to modify this resource.');
        }
    }

    /**
     * Check if user is a guest (not logged in)
     */
    public static function isGuest() {
        return !isset($_SESSION['user_id']);
    }

    private static function logoutHijackedSession() {
        session_unset();
        session_destroy();
        header('Location: index.php?page=login&compromised=1');
        exit;
    }
}
?>
