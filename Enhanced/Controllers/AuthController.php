<?php
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Middleware/InputValidator.php';
require_once __DIR__ . '/../Middleware/RateLimiter.php';
require_once __DIR__ . '/../Middleware/CSRFMiddleware.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function login() {
        if (!isset($_SESSION)) {
            session_start();
        }

        // If already logged in, redirect to dashboard
        if (isset($_SESSION['user_id'])) {
            header('Location: index.php?page=dashboard');
            exit;
        }

        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // CSRF Validation
                CSRFMiddleware::validateToken();

                $email = isset($_POST['email']) ? trim($_POST['email']) : '';
                $password = isset($_POST['password']) ? $_POST['password'] : '';

                // Rate limiting check
                if (RateLimiter::isRateLimited($email)) {
                    $error = "Too many failed login attempts. Please try again after 15 minutes.";
                } else {
                    $user = $this->userModel->findByEmail($email);

                    if ($user && $this->userModel->verifyPassword($password, $user['password'])) {
                        // Clear rate limiting attempts
                        RateLimiter::clearAttempts($email);

                        // Secure session setup
                        session_regenerate_id(true);
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['role'] = $user['role'];
                        $_SESSION['last_activity'] = time();
                        $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
                        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];

                        header('Location: index.php?page=dashboard');
                        exit;
                    } else {
                        // Record failed login attempt
                        RateLimiter::recordAttempt($email);
                        // Generic error message to prevent username enumeration
                        $error = "Invalid email or password";
                    }
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        require_once __DIR__ . '/../Views/login.php';
    }

    public function register() {
        if (!isset($_SESSION)) {
            session_start();
        }

        if (isset($_SESSION['user_id'])) {
            header('Location: index.php?page=dashboard');
            exit;
        }

        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // CSRF Validation
                CSRFMiddleware::validateToken();

                $username = isset($_POST['username']) ? trim($_POST['username']) : '';
                $email = isset($_POST['email']) ? trim($_POST['email']) : '';
                $password = isset($_POST['password']) ? $_POST['password'] : '';
                $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

                // Server-side validation
                $validator = new InputValidator();
                $validator->required('username', $username, 'Username')
                          ->minLength('username', $username, 3, 'Username')
                          ->maxLength('username', $username, 50, 'Username')
                          ->username('username', $username)
                          ->required('email', $email, 'Email')
                          ->email('email', $email)
                          ->required('password', $password, 'Password')
                          ->passwordStrength('password', $password)
                          ->matches('confirm_password', $password, $confirmPassword, 'Passwords');

                if ($validator->passes()) {
                    // Check if email already registered
                    if ($this->userModel->findByEmail($email)) {
                        $errors['email'] = "Email address is already registered.";
                    } else {
                        $userId = $this->userModel->register($username, $email, $password);
                        if ($userId) {
                            // Register success, auto-login
                            session_regenerate_id(true);
                            $_SESSION['user_id'] = $userId;
                            $_SESSION['username'] = $username;
                            $_SESSION['role'] = 'user';
                            $_SESSION['last_activity'] = time();
                            $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
                            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];

                            header('Location: index.php?page=dashboard');
                            exit;
                        } else {
                            $errors['general'] = "Registration failed. Please try again.";
                        }
                    }
                } else {
                    $errors = $validator->getErrors();
                }
            } catch (Exception $e) {
                $errors['general'] = $e->getMessage();
            }
        }
        require_once __DIR__ . '/../Views/register.php';
    }

    public function logout() {
        if (!isset($_SESSION)) {
            session_start();
        }

        // Clean session variables
        $_SESSION = [];

        // Delete session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Destroy session
        session_destroy();

        header('Location: index.php?page=login');
        exit;
    }
}
?>
