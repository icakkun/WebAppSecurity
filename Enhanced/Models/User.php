<?php
require_once __DIR__ . '/Database.php';

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // SECURE: Prepared statement, fetch user by email only
    public function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email";
        return $this->db->fetchOne($sql, [':email' => $email]);
    }

    // SECURE: Password hashed with bcrypt
    public function register($username, $email, $password) {
        // SECURE: Check if email already exists
        $existing = $this->findByEmail($email);
        if ($existing) {
            return false;
        }

        // SECURE: Hash password with bcrypt
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        $sql = "INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, 'user')";
        $this->db->execute($sql, [
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashedPassword
        ]);
        return $this->db->lastInsertId();
    }

    // SECURE: Verify password with password_verify()
    public function verifyPassword($password, $hashedPassword) {
        return password_verify($password, $hashedPassword);
    }

    public function getAllUsers() {
        $sql = "SELECT id, username, email, role, created_at FROM users";
        return $this->db->fetchAll($sql);
    }

    public function getUserById($id) {
        $sql = "SELECT id, username, email, role, created_at FROM users WHERE id = :id";
        return $this->db->fetchOne($sql, [':id' => (int)$id]);
    }

    public function deleteUser($id) {
        $sql = "DELETE FROM users WHERE id = :id";
        $this->db->execute($sql, [':id' => (int)$id]);
    }
}
?>
