<?php
require_once __DIR__ . '/../Models/User.php';

class AdminController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    // INSECURE: No authorization check - any logged-in user can access
    public function index() {
        $users = $this->userModel->getAllUsers();
        require_once __DIR__ . '/../Views/admin/users.php';
    }

    // INSECURE: No authorization check, no CSRF token
    public function deleteUser($id) {
        $this->userModel->deleteUser($id);
        header('Location: index.php?page=admin');
        exit;
    }
}
?>
