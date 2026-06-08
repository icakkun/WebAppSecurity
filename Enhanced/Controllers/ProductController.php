<?php
require_once __DIR__ . '/../Models/Product.php';
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../Middleware/CSRFMiddleware.php';
require_once __DIR__ . '/../Middleware/InputValidator.php';

class ProductController {
    private $productModel;

    public function __construct() {
        $this->productModel = new Product();
    }

    public function index() {
        AuthMiddleware::requireLogin();

        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        if ($search !== '') {
            $products = $this->productModel->searchProducts($search);
        } else {
            $products = $this->productModel->getAllProducts();
        }
        require_once __DIR__ . '/../Views/products/index.php';
    }

    public function show($id) {
        AuthMiddleware::requireLogin();

        // Validate ID is integer
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if ($id === false) {
            http_response_code(400);
            die('Invalid product ID');
        }

        $product = $this->productModel->getProductById($id);
        if (!$product) {
            http_response_code(404);
            die("Product not found");
        }
        require_once __DIR__ . '/../Views/products/show.php';
    }

    public function create() {
        AuthMiddleware::requireLogin();
        require_once __DIR__ . '/../Views/products/create.php';
    }

    public function store() {
        AuthMiddleware::requireLogin();

        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // CSRF Check
                CSRFMiddleware::validateToken();

                $name = isset($_POST['name']) ? trim($_POST['name']) : '';
                $description = isset($_POST['description']) ? trim($_POST['description']) : '';
                $price = isset($_POST['price']) ? trim($_POST['price']) : '';
                $quantity = isset($_POST['quantity']) ? trim($_POST['quantity']) : '';
                $image = '';

                // Input validation
                $validator = new InputValidator();
                $validator->required('name', $name, 'Product Name')
                          ->maxLength('name', $name, 100, 'Product Name')
                          ->required('price', $price, 'Price')
                          ->numeric('price', $price, 'Price')
                          ->positive('price', $price, 'Price')
                          ->required('quantity', $quantity, 'Quantity')
                          ->integer('quantity', $quantity, 'Quantity')
                          ->positive('quantity', $quantity, 'Quantity');

                if (!$validator->passes()) {
                    $errors = $validator->getErrors();
                }

                // File upload verification
                if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                    if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                        $errors['image'] = "File upload failed with error code: " . $_FILES['image']['error'];
                    } else {
                        $uploadedImage = $this->handleFileUpload($_FILES['image']);
                        if ($uploadedImage === false) {
                            $errors['image'] = "Invalid file type (JPG/PNG/GIF allowed), size exceeds 2MB, or upload error.";
                        } else {
                            $image = $uploadedImage;
                        }
                    }
                }

                if (empty($errors)) {
                    $this->productModel->createProduct($name, $description, $price, $quantity, $image, $_SESSION['user_id']);
                    header('Location: index.php?page=products');
                    exit;
                }
            } catch (Exception $e) {
                $errors['general'] = $e->getMessage();
            }
        }
        require_once __DIR__ . '/../Views/products/create.php';
    }

    public function edit($id) {
        AuthMiddleware::requireLogin();

        $id = filter_var($id, FILTER_VALIDATE_INT);
        if ($id === false) {
            http_response_code(400);
            die('Invalid product ID');
        }

        $product = $this->productModel->getProductById($id);
        if (!$product) {
            http_response_code(404);
            die("Product not found");
        }

        // Ownership/Role validation
        AuthMiddleware::requireOwnerOrAdmin($product['user_id']);

        require_once __DIR__ . '/../Views/products/edit.php';
    }

    public function update($id) {
        AuthMiddleware::requireLogin();

        $id = filter_var($id, FILTER_VALIDATE_INT);
        if ($id === false) {
            http_response_code(400);
            die('Invalid product ID');
        }

        $product = $this->productModel->getProductById($id);
        if (!$product) {
            http_response_code(404);
            die("Product not found");
        }

        // Ownership validation
        AuthMiddleware::requireOwnerOrAdmin($product['user_id']);

        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // CSRF Check
                CSRFMiddleware::validateToken();

                $name = isset($_POST['name']) ? trim($_POST['name']) : '';
                $description = isset($_POST['description']) ? trim($_POST['description']) : '';
                $price = isset($_POST['price']) ? trim($_POST['price']) : '';
                $quantity = isset($_POST['quantity']) ? trim($_POST['quantity']) : '';
                $image = $product['image']; // Default to current image

                // Input validation
                $validator = new InputValidator();
                $validator->required('name', $name, 'Product Name')
                          ->maxLength('name', $name, 100, 'Product Name')
                          ->required('price', $price, 'Price')
                          ->numeric('price', $price, 'Price')
                          ->positive('price', $price, 'Price')
                          ->required('quantity', $quantity, 'Quantity')
                          ->integer('quantity', $quantity, 'Quantity')
                          ->positive('quantity', $quantity, 'Quantity');

                if (!$validator->passes()) {
                    $errors = $validator->getErrors();
                }

                // File upload verification
                if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                    if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                        $errors['image'] = "File upload failed with error code: " . $_FILES['image']['error'];
                    } else {
                        $uploadedImage = $this->handleFileUpload($_FILES['image']);
                        if ($uploadedImage === false) {
                            $errors['image'] = "Invalid file type (JPG/PNG/GIF allowed), size exceeds 2MB, or upload error.";
                        } else {
                            // If old image exists, we could delete it, but this is a simple demo
                            $image = $uploadedImage;
                        }
                    }
                }

                if (empty($errors)) {
                    $this->productModel->updateProduct($id, $name, $description, $price, $quantity, $image);
                    header('Location: index.php?page=products&action=show&id=' . $id);
                    exit;
                }
            } catch (Exception $e) {
                $errors['general'] = $e->getMessage();
            }
        }
        require_once __DIR__ . '/../Views/products/edit.php';
    }

    public function delete($id) {
        AuthMiddleware::requireLogin();

        $id = filter_var($id, FILTER_VALIDATE_INT);
        if ($id === false) {
            http_response_code(400);
            die('Invalid product ID');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Error: Delete operations must use POST.');
        }

        try {
            // CSRF Check
            CSRFMiddleware::validateToken();

            $deleted = $this->productModel->deleteProduct($id, $_SESSION['user_id'], $_SESSION['role']);
            if (!$deleted) {
                http_response_code(403);
                die('Access Denied: You do not have permission to delete this product.');
            }

            header('Location: index.php?page=products');
            exit;
        } catch (Exception $e) {
            http_response_code(500);
            die("Delete failed: " . htmlspecialchars($e->getMessage()));
        }
    }

    /**
     * Helper method to securely validate and process file upload
     */
    private function handleFileUpload($file) {
        // Size validation
        if ($file['size'] > UPLOAD_MAX_SIZE) {
            return false;
        }

        // Extension validation (whitelist)
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, ALLOWED_EXTENSIONS)) {
            return false;
        }

        // MIME-type validation using PHP finfo
        if (class_exists('finfo')) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($file['tmp_name']);
        } else {
            // Fallback if finfo is not enabled
            $mimeType = mime_content_type($file['tmp_name']);
        }

        if (!in_array($mimeType, ALLOWED_MIME_TYPES)) {
            return false;
        }

        // Generate random filename to prevent directory traversal and name collisions
        $newFilename = bin2hex(random_bytes(16)) . '.' . $extension;

        $uploadDir = __DIR__ . '/../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Secure copy
        if (move_uploaded_file($file['tmp_name'], $uploadDir . $newFilename)) {
            return $newFilename;
        }

        return false;
    }
}
?>
