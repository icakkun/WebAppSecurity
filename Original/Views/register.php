<?php require_once __DIR__ . '/layout.php'; ?>
<?php
ob_start();
?>
<div class="card">
    <h2>Register</h2>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <!-- INSECURE: No CSRF token, no client-side validation -->
    <form method="POST" action="index.php?page=register">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="text" name="email">
        </div>
        <div class="form-group">
            <label>Password</label>
            <!-- INSECURE: No password policy enforced -->
            <input type="password" name="password">
        </div>
        <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password">
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
    </form>
    <p style="margin-top: 15px;">Already have an account? <a href="index.php?page=login">Login here</a></p>
</div>
<?php
$content = ob_get_clean();
renderLayout('Register', $content);
?>
