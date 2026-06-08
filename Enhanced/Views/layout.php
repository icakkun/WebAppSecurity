<?php
// SECURE: Enforce security headers
if (!headers_sent()) {
    header("Content-Security-Policy: " . CSP_HEADER);
    header("X-Content-Type-Options: nosniff");
    header("X-Frame-Options: DENY");
    header("X-XSS-Protection: 1; mode=block");
    header("Referrer-Policy: strict-origin-when-cross-origin");
}

function renderLayout($title, $content) {
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
    $role = isset($_SESSION['role']) ? $_SESSION['role'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?> - Secure Inventory System</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #0b0f19;
            --bg-secondary: #161c2d;
            --bg-tertiary: #1f293d;
            --accent-primary: #4f46e5;
            --accent-primary-hover: #4338ca;
            --accent-success: #10b981;
            --accent-success-hover: #059669;
            --accent-danger: #ef4444;
            --accent-danger-hover: #dc2626;
            --accent-warning: #f59e0b;
            --accent-warning-hover: #d97706;
            --text-main: #f3f4f6;
            --text-muted: #9ca3af;
            --border-color: rgba(255, 255, 255, 0.08);
            --font-display: 'Outfit', sans-serif;
            --font-body: 'Plus Jakarta Sans', sans-serif;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-premium: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-body);
            background: var(--bg-primary);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.6;
        }

        header {
            background: rgba(22, 28, 45, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        nav {
            max-width: 1200px;
            margin: 0 auto;
            padding: 15px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-brand {
            font-family: var(--font-display);
            font-size: 22px;
            font-weight: 700;
            background: linear-gradient(135deg, #a5b4fc 0%, #6366f1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-decoration: none;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .nav-links a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.2s ease, transform 0.2s ease;
            position: relative;
        }

        .nav-links a:hover {
            color: var(--text-main);
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -4px;
            left: 0;
            background-color: var(--accent-primary);
            transition: width 0.2s ease;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .user-badge {
            background: rgba(79, 70, 229, 0.1);
            color: #a5b4fc;
            padding: 6px 12px;
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 600;
            border: 1px solid rgba(79, 70, 229, 0.2);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .container {
            flex: 1;
            max-width: 1200px;
            width: 100%;
            margin: 40px auto;
            padding: 0 24px;
        }

        .card {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 24px;
            box-shadow: var(--shadow-premium);
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            border-color: rgba(99, 102, 241, 0.25);
        }

        h1, h2, h3, h4 {
            font-family: var(--font-display);
            color: var(--text-main);
            font-weight: 700;
            letter-spacing: -0.5px;
            margin-bottom: 20px;
        }

        h1 { font-size: 32px; }
        h2 { font-size: 24px; }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-family: var(--font-body);
            font-weight: 600;
            font-size: 14px;
            padding: 10px 24px;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            outline: none;
            gap: 8px;
        }

        .btn-primary {
            background: var(--accent-primary);
            color: white;
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.3);
        }

        .btn-primary:hover {
            background: var(--accent-primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
        }

        .btn-success {
            background: var(--accent-success);
            color: white;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3);
        }

        .btn-success:hover {
            background: var(--accent-success-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        }

        .btn-danger {
            background: var(--accent-danger);
            color: white;
            box-shadow: 0 4px 14px rgba(239, 68, 68, 0.3);
        }

        .btn-danger:hover {
            background: var(--accent-danger-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
        }

        .btn-warning {
            background: var(--accent-warning);
            color: #0b0f19;
            box-shadow: 0 4px 14px rgba(245, 158, 11, 0.3);
        }

        .btn-warning:hover {
            background: var(--accent-warning-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
        }

        .btn:active {
            transform: translateY(1px);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-muted);
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            background: var(--bg-tertiary);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-main);
            font-family: var(--font-body);
            font-size: 14px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
        }

        textarea.form-control {
            height: 120px;
            resize: vertical;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th, table td {
            padding: 16px 20px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
            font-size: 14px;
        }

        table th {
            background: rgba(255, 255, 255, 0.02);
            color: var(--text-muted);
            font-weight: 600;
            font-family: var(--font-display);
        }

        table tr:last-child td {
            border-bottom: none;
        }

        table tr:hover td {
            background: rgba(255, 255, 255, 0.01);
        }

        .alert {
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 14px;
            font-weight: 500;
            border: 1px solid transparent;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #fca5a5;
            border-color: rgba(239, 68, 68, 0.2);
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: #6ee7b7;
            border-color: rgba(16, 185, 129, 0.2);
        }

        .search-bar {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
        }

        .search-bar input {
            flex: 1;
        }

        .product-image {
            max-width: 120px;
            max-height: 120px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid var(--border-color);
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        .actions form {
            display: inline;
        }

        footer {
            background: var(--bg-secondary);
            border-top: 1px solid var(--border-color);
            padding: 24px;
            text-align: center;
            font-size: 13px;
            color: var(--text-muted);
            margin-top: auto;
        }

        footer a {
            color: var(--text-main);
            text-decoration: none;
        }
        
        /* Password policy indicator styles */
        .req-list {
            list-style: none;
            margin-top: 8px;
            font-size: 12px;
            color: var(--text-muted);
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px;
        }
        .req-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .req-item.valid {
            color: var(--accent-success);
        }
        .req-item.invalid::before {
            content: '○';
        }
        .req-item.valid::before {
            content: '●';
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <a href="index.php?page=dashboard" class="nav-brand">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#6366f1;"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                SecureInv
            </a>
            <div class="nav-links">
                <a href="index.php?page=dashboard">Dashboard</a>
                <a href="index.php?page=products">Products</a>
                <?php if ($role === 'admin'): ?>
                    <a href="index.php?page=admin">Admin Panel</a>
                <?php endif; ?>
                <?php if ($username): ?>
                    <span class="user-badge">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        <?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                    <a href="index.php?page=logout" class="btn btn-danger" style="padding: 6px 14px; font-size:12px;">Logout</a>
                <?php else: ?>
                    <a href="index.php?page=login">Login</a>
                    <a href="index.php?page=register">Register</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>
    <main class="container">
        <?php echo $content; ?>
    </main>
    <footer>
        <p>&copy; <?php echo date('Y'); ?> SecureInv System. Hardened Web Application. All rights reserved.</p>
    </footer>
</body>
</html>
<?php
}
?>
