<?php require_once __DIR__ . '/layout.php'; ?>
<?php
ob_start();
?>
<div class="card">
    <h1>Dashboard</h1>
    <!-- INSECURE: Username displayed without encoding -->
    <p>Welcome back, <?php echo $_SESSION['username']; ?>!</p>
    <p>Role: <?php echo $_SESSION['role']; ?></p>
</div>
<div class="card">
    <h2>Quick Actions</h2>
    <a href="index.php?page=products" class="btn btn-primary">View Products</a>
    <a href="index.php?page=products&action=create" class="btn btn-success">Add New Product</a>
    <a href="index.php?page=admin" class="btn btn-warning">Admin Panel</a>
</div>
<?php
$content = ob_get_clean();
renderLayout('Dashboard', $content);
?>
