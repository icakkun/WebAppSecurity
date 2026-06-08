<?php
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../Middleware/CSRFMiddleware.php';

class AdminController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    // SECURE: Enforce admin role check
    public function index() {
        AuthMiddleware::requireAdmin();
        $users = $this->userModel->getAllUsers();
        require_once __DIR__ . '/../Views/admin/users.php';
    }

    // SECURE: Prevent deleting own account, enforce POST + CSRF check
    public function deleteUser($id) {
        AuthMiddleware::requireAdmin();

        $id = filter_var($id, FILTER_VALIDATE_INT);
        if ($id === false) {
            http_response_code(400);
            die('Invalid user ID');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Error: Delete operations must use POST.');
        }

        try {
            // CSRF validation
            CSRFMiddleware::validateToken();

            // Self-deletion check
            if ($id == $_SESSION['user_id']) {
                http_response_code(400);
                die('Error: You cannot delete your own administrator account.');
            }

            $this->userModel->deleteUser($id);
            header('Location: index.php?page=admin');
            exit;
        } catch (Exception $e) {
            http_response_code(500);
            die("Delete failed: " . htmlspecialchars($e->getMessage()));
        }
    }
}
?>
