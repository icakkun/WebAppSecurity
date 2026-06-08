<?php require_once __DIR__ . '/layout.php'; ?>
<?php
ob_start();
?>
<div style="max-width: 500px; margin: 40px auto;">
    <div class="card">
        <h2 style="text-align: center; margin-bottom: 24px;">Create an Account</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger" style="flex-direction: column; align-items: flex-start; gap: 6px;">
                <div style="display:flex; align-items:center; gap: 8px; font-weight:600;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    Registration Errors:
                </div>
                <ul style="margin-left: 26px; font-size: 13px;">
                    <?php foreach ($errors as $field => $err): ?>
                        <li><?php echo htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php?page=register" id="registerForm">
            <!-- SECURE: Include CSRF Token -->
            <?php echo CSRFMiddleware::getTokenField(); ?>

            <div class="form-group">
                <label for="username">Username</label>
                <!-- SECURE: Input validation attributes -->
                <input type="text" id="username" name="username" class="form-control" 
                       required minlength="3" maxlength="50" pattern="[a-zA-Z0-9_]+" 
                       title="Only letters, numbers, and underscores are allowed."
                       value="<?php echo isset($username) ? htmlspecialchars($username, ENT_QUOTES, 'UTF-8') : ''; ?>"
                       placeholder="john_doe">
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" 
                       required
                       value="<?php echo isset($email) ? htmlspecialchars($email, ENT_QUOTES, 'UTF-8') : ''; ?>"
                       placeholder="john@example.com">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" 
                       required minlength="8" placeholder="••••••••">
                
                <!-- Password strength indicator -->
                <ul class="req-list" id="password-requirements">
                    <li class="req-item invalid" id="req-length">Min 8 characters</li>
                    <li class="req-item invalid" id="req-upper">At least 1 uppercase</li>
                    <li class="req-item invalid" id="req-lower">At least 1 lowercase</li>
                    <li class="req-item invalid" id="req-number">At least 1 number</li>
                    <li class="req-item invalid" id="req-special">At least 1 special char (!@#$...)</li>
                </ul>
            </div>

            <div class="form-group" style="margin-bottom: 24px;">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" 
                       required placeholder="••••••••">
                <div id="password-match-error" style="color:var(--accent-danger); font-size:12px; margin-top:6px; display:none;">
                    Passwords do not match.
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px;">Sign Up</button>
        </form>

        <div style="margin-top: 24px; text-align: center; border-top: 1px solid var(--border-color); padding-top: 20px;">
            <p style="font-size: 14px; color: var(--text-muted);">
                Already have an account? <a href="index.php?page=login" style="color: var(--accent-primary); text-decoration: none; font-weight: 500;">Login here</a>
            </p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var passwordInput = document.getElementById('password');
    var confirmInput = document.getElementById('confirm_password');
    var matchError = document.getElementById('password-match-error');

    function updateReq(elementId, isValid) {
        var el = document.getElementById(elementId);
        if (isValid) {
            el.classList.remove('invalid');
            el.classList.add('valid');
        } else {
            el.classList.remove('valid');
            el.classList.add('invalid');
        }
    }

    passwordInput.addEventListener('input', function() {
        var val = this.value;
        updateReq('req-length', val.length >= 8);
        updateReq('req-upper', /[A-Z]/.test(val));
        updateReq('req-lower', /[a-z]/.test(val));
        updateReq('req-number', /[0-9]/.test(val));
        updateReq('req-special', /[!@#$%^&*(),.?":{}|<>]/.test(val));
        checkMatch();
    });

    function checkMatch() {
        if (confirmInput.value !== '') {
            if (passwordInput.value !== confirmInput.value) {
                matchError.style.display = 'block';
            } else {
                matchError.style.display = 'none';
            }
        } else {
            matchError.style.display = 'none';
        }
    }

    confirmInput.addEventListener('input', checkMatch);
});
</script>
<?php
$content = ob_get_clean();
renderLayout('Register', $content);
?>
