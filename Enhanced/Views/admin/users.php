<?php require_once __DIR__ . '/../layout.php'; ?>
<?php
ob_start();
?>
<div style="margin-bottom: 30px;">
    <h1>Admin Panel - User Management</h1>
    <p style="color: var(--text-muted);">Manage registered application users. You cannot delete your own active administrator account.</p>
</div>

<div class="card" style="padding: 0; overflow-x: auto;">
    <table>
        <thead>
            <tr>
                <th style="width: 80px;">ID</th>
                <th>Username</th>
                <th>Email Address</th>
                <th>Role</th>
                <th>Created At</th>
                <th style="width: 150px; text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td style="font-weight: 600;"><?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td>
                    <?php if ($user['role'] === 'admin'): ?>
                        <span class="user-badge" style="background:rgba(245, 158, 11, 0.1); color:var(--accent-warning); border-color:rgba(245, 158, 11, 0.2);">admin</span>
                    <?php else: ?>
                        <span class="user-badge" style="background:rgba(79, 70, 229, 0.05); color:#a5b4fc; border-color:rgba(79, 70, 229, 0.15);">user</span>
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($user['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td style="text-align: right;">
                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                        <!-- SECURE: POST-based deletion with CSRF -->
                        <form method="POST" action="index.php?page=admin&action=delete&id=<?php echo htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8'); ?>" onsubmit="return confirm('Are you sure you want to delete user: <?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?>?')">
                            <?php echo CSRFMiddleware::getTokenField(); ?>
                            <button type="submit" class="btn btn-danger" style="padding: 6px 12px; font-size:12px;">Delete</button>
                        </form>
                    <?php else: ?>
                        <span style="font-size: 12px; color: var(--text-muted); font-style: italic;">Current Account</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
$content = ob_get_clean();
renderLayout('User Management', $content);
?>
