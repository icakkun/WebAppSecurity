<?php
require_once __DIR__ . '/../Models/User.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // INSECURE: No input validation
            $email = $_POST['email'];
            $password = $_POST['password'];

            // INSECURE: Plain text password comparison via SQL
            $user = $this->userModel->login($email, $password);

            if ($user) {
                // INSECURE: No session regeneration
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                header('Location: index.php?page=dashboard');
                exit;
            } else {
                $error = "Invalid email or password";
            }
        }
        require_once __DIR__ . '/../Views/login.php';
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // INSECURE: No input validation, no password policy
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];

            // INSECURE: Password stored as plain text
            $userId = $this->userModel->register($username, $email, $password);

            if ($userId) {
                $_SESSION['user_id'] = $userId;
                $_SESSION['username'] = $username;
                $_SESSION['role'] = 'user';
                header('Location: index.php?page=dashboard');
                exit;
            } else {
                $error = "Registration failed";
            }
        }
        require_once __DIR__ . '/../Views/register.php';
    }

    public function logout() {
        session_destroy();
        header('Location: index.php?page=login');
        exit;
    }
}
?>
