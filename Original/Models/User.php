<?php
require_once __DIR__ . '/Database.php';

class User {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // INSECURE: No input validation, SQL injection possible
    public function login($email, $password) {
        // INSECURE: String concatenation - vulnerable to SQL injection
        $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
        return $this->db->fetchOne($sql);
    }

    // INSECURE: Password stored as plain text
    public function register($username, $email, $password) {
        // INSECURE: No validation, no password hashing, SQL injection possible
        $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";
        $this->db->query($sql);
        return $this->db->getLastId();
    }

    public function getAllUsers() {
        $sql = "SELECT * FROM users";
        return $this->db->fetchAll($sql);
    }

    public function getUserById($id) {
        // INSECURE: SQL injection possible
        $sql = "SELECT * FROM users WHERE id = $id";
        return $this->db->fetchOne($sql);
    }

    public function deleteUser($id) {
        // INSECURE: No authorization check, SQL injection possible
        $sql = "DELETE FROM users WHERE id = $id";
        $this->db->query($sql);
    }
}
?>
