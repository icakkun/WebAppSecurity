<?php require_once __DIR__ . '/layout.php'; ?>
<?php
ob_start();
?>
<div class="card">
    <h2>Login</h2>
    <?php if (isset($error)): ?>
        <!-- INSECURE: Error message echoed without encoding -->
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <!-- INSECURE: No CSRF token -->
    <form method="POST" action="index.php?page=login">
        <div class="form-group">
            <label>Email</label>
            <!-- INSECURE: No input validation attributes -->
            <input type="text" name="email">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password">
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
    <p style="margin-top: 15px;">Don't have an account? <a href="index.php?page=register">Register here</a></p>
</div>
<?php
$content = ob_get_clean();
renderLayout('Login', $content);
?>
