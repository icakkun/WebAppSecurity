<?php
// INSECURE: No security headers set
function renderLayout($title, $content) {
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - Inventory System</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        nav { background: #333; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        nav a { color: white; text-decoration: none; margin-left: 20px; }
        nav a:hover { text-decoration: underline; }
        .nav-brand { font-size: 20px; font-weight: bold; }
        .container { max-width: 1100px; margin: 30px auto; padding: 0 20px; }
        .card { background: white; border-radius: 5px; padding: 25px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .btn { display: inline-block; padding: 8px 20px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 14px; color: white; }
        .btn-primary { background: #007bff; }
        .btn-danger { background: #dc3545; }
        .btn-success { background: #28a745; }
        .btn-warning { background: #ffc107; color: #333; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
        .form-group textarea { height: 100px; resize: vertical; }
        table { width: 100%; border-collapse: collapse; }
        table th, table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        table th { background: #f8f9fa; }
        .alert { padding: 12px 20px; border-radius: 4px; margin-bottom: 20px; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .search-bar { display: flex; gap: 10px; margin-bottom: 20px; }
        .search-bar input { flex: 1; }
        h1, h2 { margin-bottom: 20px; color: #333; }
        .product-image { max-width: 200px; max-height: 200px; }
        .actions a { margin-right: 10px; }
    </style>
</head>
<body>
    <nav>
        <div class=\"nav-brand\">Inventory System</div>
        <div>
            <a href=\"index.php?page=dashboard\">Dashboard</a>
            <a href=\"index.php?page=products\">Products</a>
            <!-- INSECURE: Admin link visible to all users -->
            <a href=\"index.php?page=admin\">Admin Panel</a>
            <?php if ($username): ?>
                <span style=\"margin-left:20px;\">Welcome, <?php echo $username; ?></span>
                <a href=\"index.php?page=logout\">Logout</a>
            <?php else: ?>
                <a href=\"index.php?page=login\">Login</a>
                <a href=\"index.php?page=register\">Register</a>
            <?php endif; ?>
        </div>
    </nav>
    <div class=\"container\">
        <?php echo $content; ?>
    </div>
</body>
</html>
<?php
}
?>
