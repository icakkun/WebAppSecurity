<?php
class Database {
    private $conn;

    public function __construct() {
        $this->conn = mysqli_connect('localhost', 'root', '', 'inventory_db');
        if (!$this->conn) {
            // INSECURE: Showing detailed error
            die("Database connection failed: " . mysqli_connect_error());
        }
    }

    public function getConnection() {
        return $this->conn;
    }

    // INSECURE: Direct query execution with no prepared statements
    public function query($sql) {
        $result = mysqli_query($this->conn, $sql);
        if (!$result) {
            // INSECURE: Showing SQL errors to user
            die("Query failed: " . mysqli_error($this->conn));
        }
        return $result;
    }

    public function fetchAll($sql) {
        $result = $this->query($sql);
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function fetchOne($sql) {
        $result = $this->query($sql);
        return mysqli_fetch_assoc($result);
    }

    public function escape($value) {
        return mysqli_real_escape_string($this->conn, $value);
    }

    public function getLastId() {
        return mysqli_insert_id($this->conn);
    }
}
?>
