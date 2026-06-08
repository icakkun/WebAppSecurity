<?php
// INSECURE: Basic session with no security configuration
session_start();

require_once __DIR__ . '/Controllers/AuthController.php';
require_once __DIR__ . '/Controllers/ProductController.php';
require_once __DIR__ . '/Controllers/AdminController.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'login';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';
$id = isset($_GET['id']) ? $_GET['id'] : null;

// INSECURE: No authentication check for protected routes
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
        // INSECURE: No auth check
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
        // INSECURE: No authorization check
        $controller = new AdminController();
        if ($action === 'delete' && $id) {
            $controller->deleteUser($id);
        } else {
            $controller->index();
        }
        break;

    default:
        // INSECURE: No proper 404 handling
        echo "Page not found";
}
?>
