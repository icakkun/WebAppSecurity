<?php
require_once __DIR__ . '/../Models/Database.php';

/**
 * Rate Limiter Middleware
 * Prevents brute-force login attacks by tracking and limiting login attempts
 */
class RateLimiter {

    /**
     * Check if the login is rate-limited
     * Returns true if too many attempts have been made
     */
    public static function isRateLimited($email) {
        $db = Database::getInstance();
        $ip = self::getClientIP();
        
        // Clean up old attempts
        $cutoff = date('Y-m-d H:i:s', time() - LOGIN_LOCKOUT_TIME);
        $db->execute(
            "DELETE FROM login_attempts WHERE attempted_at < :cutoff",
            [':cutoff' => $cutoff]
        );

        // Count recent attempts
        $sql = "SELECT COUNT(*) as attempts FROM login_attempts 
                WHERE (email = :email OR ip_address = :ip) 
                AND attempted_at > :cutoff";
        $result = $db->fetchOne($sql, [
            ':email' => $email,
            ':ip' => $ip,
            ':cutoff' => $cutoff
        ]);

        return ($result['attempts'] >= MAX_LOGIN_ATTEMPTS);
    }

    /**
     * Record a failed login attempt
     */
    public static function recordAttempt($email) {
        $db = Database::getInstance();
        $ip = self::getClientIP();
        $sql = "INSERT INTO login_attempts (email, ip_address) VALUES (:email, :ip)";
        $db->execute($sql, [
            ':email' => $email,
            ':ip' => $ip
        ]);
    }

    /**
     * Clear attempts for a specific email/IP after a successful login
     */
    public static function clearAttempts($email) {
        $db = Database::getInstance();
        $ip = self::getClientIP();
        $db->execute(
            "DELETE FROM login_attempts WHERE email = :email OR ip_address = :ip",
            [':email' => $email, ':ip' => $ip]
        );
    }

    /**
     * Helper to get client IP
     */
    private static function getClientIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }
}
?>
