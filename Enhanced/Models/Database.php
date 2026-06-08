<?php
class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        try {
            $config = require __DIR__ . '/../config/database.php';
            $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
            
            // SECURE: Using PDO with prepared statements
            $this->pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
        } catch (PDOException $e) {
            // SECURE: Log the error, don't expose details to users
            error_log('Database connection error: ' . $e->getMessage());
            die('A database error occurred. Please try again later.');
        }
    }

    // Singleton pattern to reuse connection
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }

    // SECURE: Prepared statement execution
    public function prepare($sql) {
        return $this->pdo->prepare($sql);
    }

    // SECURE: Execute with bound parameters
    public function execute($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log('Query error: ' . $e->getMessage());
            throw new Exception('A database error occurred.');
        }
    }

    public function fetchAll($sql, $params = []) {
        $stmt = $this->execute($sql, $params);
        return $stmt->fetchAll();
    }

    public function fetchOne($sql, $params = []) {
        $stmt = $this->execute($sql, $params);
        return $stmt->fetch();
    }

    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }

    // Prevent cloning
    private function __clone() {}
}
?>
