<?php
require_once __DIR__ . '/Database.php';

class Product {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAllProducts() {
        $sql = "SELECT products.*, users.username FROM products 
                LEFT JOIN users ON products.user_id = users.id 
                ORDER BY products.created_at DESC";
        return $this->db->fetchAll($sql);
    }

    // SECURE: Parameterized query
    public function getProductById($id) {
        $sql = "SELECT products.*, users.username FROM products 
                LEFT JOIN users ON products.user_id = users.id 
                WHERE products.id = :id";
        return $this->db->fetchOne($sql, [':id' => (int)$id]);
    }

    // SECURE: All inputs bound as parameters
    public function createProduct($name, $description, $price, $quantity, $image, $user_id) {
        $sql = "INSERT INTO products (name, description, price, quantity, image, user_id) 
                VALUES (:name, :description, :price, :quantity, :image, :user_id)";
        $this->db->execute($sql, [
            ':name' => $name,
            ':description' => $description,
            ':price' => (float)$price,
            ':quantity' => (int)$quantity,
            ':image' => $image,
            ':user_id' => (int)$user_id
        ]);
        return $this->db->lastInsertId();
    }

    public function updateProduct($id, $name, $description, $price, $quantity, $image = null) {
        if ($image) {
            $sql = "UPDATE products SET name=:name, description=:description, 
                    price=:price, quantity=:quantity, image=:image WHERE id=:id";
            $params = [
                ':name' => $name,
                ':description' => $description,
                ':price' => (float)$price,
                ':quantity' => (int)$quantity,
                ':image' => $image,
                ':id' => (int)$id
            ];
        } else {
            $sql = "UPDATE products SET name=:name, description=:description, 
                    price=:price, quantity=:quantity WHERE id=:id";
            $params = [
                ':name' => $name,
                ':description' => $description,
                ':price' => (float)$price,
                ':quantity' => (int)$quantity,
                ':id' => (int)$id
            ];
        }
        $this->db->execute($sql, $params);
    }

    // SECURE: Ownership check before delete
    public function deleteProduct($id, $userId, $userRole) {
        $product = $this->getProductById($id);
        if (!$product) {
            return false;
        }
        // Only owner or admin can delete
        if ($product['user_id'] != $userId && $userRole !== 'admin') {
            return false;
        }
        $sql = "DELETE FROM products WHERE id = :id";
        $this->db->execute($sql, [':id' => (int)$id]);
        return true;
    }

    // SECURE: Parameterized search query
    public function searchProducts($keyword) {
        $sql = "SELECT products.*, users.username FROM products 
                LEFT JOIN users ON products.user_id = users.id 
                WHERE products.name LIKE :keyword OR products.description LIKE :keyword";
        return $this->db->fetchAll($sql, [':keyword' => '%' . $keyword . '%']);
    }
}
?>
