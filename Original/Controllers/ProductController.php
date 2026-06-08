<?php
require_once __DIR__ . '/../Models/Product.php';

class ProductController {
    private $productModel;

    public function __construct() {
        $this->productModel = new Product();
    }

    public function index() {
        // INSECURE: Search input not sanitized
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        if ($search) {
            $products = $this->productModel->searchProducts($search);
        } else {
            $products = $this->productModel->getAllProducts();
        }
        require_once __DIR__ . '/../Views/products/index.php';
    }

    public function show($id) {
        $product = $this->productModel->getProductById($id);
        if (!$product) {
            die("Product not found");
        }
        require_once __DIR__ . '/../Views/products/show.php';
    }

    public function create() {
        require_once __DIR__ . '/../Views/products/create.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // INSECURE: No input validation, no CSRF token check
            $name = $_POST['name'];
            $description = $_POST['description'];
            $price = $_POST['price'];
            $quantity = $_POST['quantity'];
            $image = '';

            // INSECURE: No file type validation, no size check
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $uploadDir = __DIR__ . '/../uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                // INSECURE: Using original filename (path traversal possible)
                $image = $_FILES['image']['name'];
                move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $image);
            }

            $this->productModel->createProduct($name, $description, $price, $quantity, $image, $_SESSION['user_id']);
            header('Location: index.php?page=products');
            exit;
        }
    }

    public function edit($id) {
        // INSECURE: No ownership check
        $product = $this->productModel->getProductById($id);
        if (!$product) {
            die("Product not found");
        }
        require_once __DIR__ . '/../Views/products/edit.php';
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // INSECURE: No validation, no CSRF, no ownership check
            $name = $_POST['name'];
            $description = $_POST['description'];
            $price = $_POST['price'];
            $quantity = $_POST['quantity'];
            $image = '';

            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $uploadDir = __DIR__ . '/../uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $image = $_FILES['image']['name'];
                move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $image);
            }

            $this->productModel->updateProduct($id, $name, $description, $price, $quantity, $image);
            header('Location: index.php?page=products&action=show&id=' . $id);
            exit;
        }
    }

    public function delete($id) {
        // INSECURE: No CSRF check, no ownership check
        $this->productModel->deleteProduct($id);
        header('Location: index.php?page=products');
        exit;
    }
}
?>
