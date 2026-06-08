<?php
require_once __DIR__ . '/Database.php';

class Product {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAllProducts() {
        $sql = "SELECT products.*, users.username FROM products LEFT JOIN users ON products.user_id = users.id ORDER BY products.created_at DESC";
        return $this->db->fetchAll($sql);
    }

    public function getProductById($id) {
        // INSECURE: SQL injection possible
        $sql = "SELECT products.*, users.username FROM products LEFT JOIN users ON products.user_id = users.id WHERE products.id = $id";
        return $this->db->fetchOne($sql);
    }

    // INSECURE: No input validation, SQL injection via string concat
    public function createProduct($name, $description, $price, $quantity, $image, $user_id) {
        $sql = "INSERT INTO products (name, description, price, quantity, image, user_id) VALUES ('$name', '$description', $price, $quantity, '$image', $user_id)";
        $this->db->query($sql);
        return $this->db->getLastId();
    }

    public function updateProduct($id, $name, $description, $price, $quantity, $image) {
        if ($image) {
            $sql = "UPDATE products SET name='$name', description='$description', price=$price, quantity=$quantity, image='$image' WHERE id=$id";
        } else {
            $sql = "UPDATE products SET name='$name', description='$description', price=$price, quantity=$quantity WHERE id=$id";
        }
        $this->db->query($sql);
    }

    public function deleteProduct($id) {
        // INSECURE: No ownership check
        $sql = "DELETE FROM products WHERE id = $id";
        $this->db->query($sql);
    }

    // INSECURE: Search vulnerable to SQL injection
    public function searchProducts($keyword) {
        $sql = "SELECT products.*, users.username FROM products LEFT JOIN users ON products.user_id = users.id WHERE products.name LIKE '%$keyword%' OR products.description LIKE '%$keyword%'";
        return $this->db->fetchAll($sql);
    }
}
?>
