<?php
// SECURE: Load security configurations and constants
require_once __DIR__ . '/config/security.php';

// SECURE: Configure secure session cookie settings
ini_set('session.cookie_httponly', 1);     // Prevent access to session ID from JavaScript (XSS mitigation)
ini_set('session.use_only_cookies', 1);    // Prevent session ID from appearing in URLs
ini_set('session.cookie_samesite', 'Strict'); // Strict CSRF protection for cookies
ini_set('display_errors', 0);              // Hide PHP errors in output
ini_set('log_errors', 1);                  // Log PHP errors silently

// If HTTPS is active, set secure flag on cookie
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    ini_set('session.cookie_secure', 1);
} else {
    ini_set('session.cookie_secure', 0); // Default to 0 for local non-SSL dev environments
}

// SECURE: Use a custom session name instead of default PHPSESSID
session_name(SESSION_NAME);
session_start();

// Include all middleware classes
require_once __DIR__ . '/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/Middleware/CSRFMiddleware.php';
require_once __DIR__ . '/Middleware/RateLimiter.php';

// Include controllers
require_once __DIR__ . '/Controllers/AuthController.php';
require_once __DIR__ . '/Controllers/ProductController.php';
require_once __DIR__ . '/Controllers/AdminController.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'login';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';
$id = isset($_GET['id']) ? $_GET['id'] : null;

// Routing Switch
switch ($page) {
    case 'login':
        $controller = new AuthController();
        $controller->login();
        break;

    case 'register':
        $controller = new AuthController();
        $controller->register();
        break;

    case 'logout':
        $controller = new AuthController();
        $controller->logout();
        break;

    case 'dashboard':
        // SECURE: Route protection
        AuthMiddleware::requireLogin();
        require_once __DIR__ . '/Views/dashboard.php';
        break;

    case 'products':
        $controller = new ProductController();
        switch ($action) {
            case 'index':
                $controller->index();
                break;
            case 'show':
                $controller->show($id);
                break;
            case 'create':
                $controller->create();
                break;
            case 'store':
                $controller->store();
                break;
            case 'edit':
                $controller->edit($id);
                break;
            case 'update':
                $controller->update($id);
                break;
            case 'delete':
                $controller->delete($id);
                break;
            default:
                $controller->index();
        }
        break;

    case 'admin':
        $controller = new AdminController();
        if ($action === 'delete' && $id) {
            $controller->deleteUser($id);
        } else {
            $controller->index();
        }
        break;

    default:
        // SECURE: Proper 404 response
        http_response_code(404);
        die("Error 404: Page not found");
}
?>
