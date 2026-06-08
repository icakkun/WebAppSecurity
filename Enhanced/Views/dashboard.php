<?php require_once __DIR__ . '/layout.php'; ?>
<?php
ob_start();
?>
<div style="margin-bottom: 30px;">
    <h1>Dashboard</h1>
    <!-- SECURE: Output encoding via htmlspecialchars -->
    <p style="color: var(--text-muted); font-size: 16px;">
        Welcome back, <strong style="color: var(--text-main);"><?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?></strong>!
    </p>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
    <div class="card">
        <h3 style="display: flex; align-items: center; gap: 8px; margin-bottom: 15px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--accent-primary);"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
            System Status
        </h3>
        <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 10px;">
            Role: <span class="user-badge" style="background:rgba(245, 158, 11, 0.1); color:var(--accent-warning); border-color:rgba(245, 158, 11, 0.2);"><?php echo htmlspecialchars($_SESSION['role'], ENT_QUOTES, 'UTF-8'); ?></span>
        </p>
        <p style="color: var(--text-muted); font-size: 14px;">
            Session Status: <span style="color: var(--accent-success); font-weight: 600;">Active & Hardened</span>
        </p>
    </div>

    <div class="card">
        <h3 style="display: flex; align-items: center; gap: 8px; margin-bottom: 15px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--accent-success);"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 14 14"/></svg>
            Session Info
        </h3>
        <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 10px;">
            IP Address: <code style="background: var(--bg-tertiary); padding: 2px 6px; border-radius: 4px;"><?php echo htmlspecialchars($_SESSION['ip_address'], ENT_QUOTES, 'UTF-8'); ?></code>
        </p>
        <p style="color: var(--text-muted); font-size: 14px;">
            Timeout: <span style="font-weight: 500;">30 minutes inactivity limit</span>
        </p>
    </div>
</div>

<div class="card" style="margin-top: 24px;">
    <h2>Quick Actions</h2>
    <div style="display: flex; gap: 12px; flex-wrap: wrap; margin-top: 15px;">
        <a href="index.php?page=products" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
            View Products
        </a>
        <a href="index.php?page=products&action=create" class="btn btn-success">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add New Product
        </a>
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="index.php?page=admin" class="btn btn-warning">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.1a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
                Admin Panel
            </a>
        <?php endif; ?>
    </div>
</div>
<?php
$content = ob_get_clean();
renderLayout('Dashboard', $content);
?>
