<?php require_once __DIR__ . '/layout.php'; ?>
<?php
ob_start();
?>
<div style="max-width: 450px; margin: 40px auto;">
    <div class="card">
        <h2 style="text-align: center; margin-bottom: 24px;">Login to SecureInv</h2>

        <?php if (isset($error) && $error): ?>
            <div class="alert alert-danger">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['timeout'])): ?>
            <div class="alert alert-danger">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Session expired due to inactivity. Please log in again.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['compromised'])): ?>
            <div class="alert alert-danger">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                Security validation failed. Please log in again.
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php?page=login">
            <!-- SECURE: Include CSRF Token -->
            <?php echo CSRFMiddleware::getTokenField(); ?>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="admin@example.com" required autocomplete="email">
            </div>

            <div class="form-group" style="margin-bottom: 24px;">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required autocomplete="current-password">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px;">Sign In</button>
        </form>

        <div style="margin-top: 24px; text-align: center; border-top: 1px solid var(--border-color); padding-top: 20px;">
            <p style="font-size: 14px; color: var(--text-muted);">
                Don't have an account? <a href="index.php?page=register" style="color: var(--accent-primary); text-decoration: none; font-weight: 500;">Register here</a>
            </p>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
renderLayout('Login', $content);
?>
