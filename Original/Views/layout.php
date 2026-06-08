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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* ── Reset & Base ── */
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            min-height: 100vh;
            line-height: 1.6;
        }

        /* ── Top Navigation Bar ── */
        nav {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border-bottom: 1px solid rgba(99, 102, 241, 0.15);
            color: #e2e8f0;
            padding: 0 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 64px;
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(12px);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
        }
        .nav-brand {
            font-size: 22px;
            font-weight: 700;
            background: linear-gradient(135deg, #6366f1, #a78bfa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.5px;
        }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        nav a {
            color: #94a3b8;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }
        nav a:hover {
            color: #e2e8f0;
            background: rgba(99, 102, 241, 0.1);
        }
        nav a:active {
            transform: scale(0.97);
        }
        .nav-user {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-left: 12px;
            padding-left: 16px;
            border-left: 1px solid rgba(148, 163, 184, 0.2);
        }
        .nav-user-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.2);
            color: #a78bfa;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
        }
        .nav-user-badge::before {
            content: '';
            display: inline-block;
            width: 8px;
            height: 8px;
            background: #34d399;
            border-radius: 50%;
            box-shadow: 0 0 6px rgba(52, 211, 153, 0.5);
        }
        .nav-auth a {
            font-size: 13px;
            padding: 7px 18px;
        }
        .nav-auth a:last-child {
            background: linear-gradient(135deg, #6366f1, #7c3aed);
            color: white;
            border-radius: 8px;
        }
        .nav-auth a:last-child:hover {
            background: linear-gradient(135deg, #7c3aed, #6366f1);
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
            transform: translateY(-1px);
        }

        /* ── Main Container ── */
        .container {
            max-width: 1200px;
            margin: 32px auto;
            padding: 0 24px;
            animation: fadeInUp 0.4s ease-out;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ── Cards ── */
        .card {
            background: linear-gradient(145deg, #1e293b, #1a2332);
            border: 1px solid rgba(99, 102, 241, 0.08);
            border-radius: 16px;
            padding: 28px;
            margin-bottom: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(255, 255, 255, 0.03);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card:hover {
            border-color: rgba(99, 102, 241, 0.2);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.35), 0 0 0 1px rgba(99, 102, 241, 0.1);
            transform: translateY(-2px);
        }

        /* ── Buttons ── */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 22px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            color: white;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            outline: none;
            letter-spacing: 0.01em;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }
        .btn:active {
            transform: translateY(0) scale(0.98);
        }
        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #7c3aed);
            box-shadow: 0 2px 10px rgba(99, 102, 241, 0.3);
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #7c3aed, #6366f1);
            box-shadow: 0 6px 25px rgba(99, 102, 241, 0.45);
        }
        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            box-shadow: 0 2px 10px rgba(239, 68, 68, 0.25);
        }
        .btn-danger:hover {
            background: linear-gradient(135deg, #dc2626, #ef4444);
            box-shadow: 0 6px 25px rgba(239, 68, 68, 0.4);
        }
        .btn-success {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            box-shadow: 0 2px 10px rgba(34, 197, 94, 0.25);
        }
        .btn-success:hover {
            background: linear-gradient(135deg, #16a34a, #22c55e);
            box-shadow: 0 6px 25px rgba(34, 197, 94, 0.4);
        }
        .btn-warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #1e293b;
            box-shadow: 0 2px 10px rgba(245, 158, 11, 0.25);
        }
        .btn-warning:hover {
            background: linear-gradient(135deg, #d97706, #f59e0b);
            box-shadow: 0 6px 25px rgba(245, 158, 11, 0.4);
        }

        /* ── Forms ── */
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 14px;
            color: #cbd5e1;
            letter-spacing: 0.01em;
        }
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid rgba(148, 163, 184, 0.15);
            border-radius: 10px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            background: rgba(15, 23, 42, 0.6);
            color: #e2e8f0;
            transition: all 0.25s ease;
            outline: none;
        }
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
            background: rgba(15, 23, 42, 0.8);
        }
        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: #475569;
        }
        .form-group textarea {
            height: 120px;
            resize: vertical;
        }
        .form-group select option {
            background: #1e293b;
            color: #e2e8f0;
        }

        /* ── Tables ── */
        table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
        }
        table th, table td {
            padding: 14px 16px;
            text-align: left;
            border-bottom: 1px solid rgba(148, 163, 184, 0.08);
        }
        table th {
            background: rgba(99, 102, 241, 0.06);
            color: #94a3b8;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        table tr {
            transition: background 0.2s ease;
        }
        table tbody tr:hover {
            background: rgba(99, 102, 241, 0.04);
        }
        table td {
            color: #cbd5e1;
            font-size: 14px;
        }

        /* ── Alerts ── */
        .alert {
            padding: 14px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid transparent;
            animation: slideInAlert 0.35s ease-out;
        }
        @keyframes slideInAlert {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #fca5a5;
            border-color: rgba(239, 68, 68, 0.2);
        }
        .alert-danger::before { content: '⚠'; }
        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            color: #86efac;
            border-color: rgba(34, 197, 94, 0.2);
        }
        .alert-success::before { content: '✓'; }

        /* ── Search Bar ── */
        .search-bar {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
        }
        .search-bar input {
            flex: 1;
            padding: 12px 18px;
            border: 1px solid rgba(148, 163, 184, 0.15);
            border-radius: 10px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            background: rgba(15, 23, 42, 0.6);
            color: #e2e8f0;
            transition: all 0.25s ease;
            outline: none;
        }
        .search-bar input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }

        /* ── Headings ── */
        h1, h2 {
            margin-bottom: 20px;
            color: #f1f5f9;
            font-weight: 700;
            letter-spacing: -0.02em;
        }
        h1 { font-size: 28px; }
        h2 { font-size: 22px; }

        /* ── Product Image ── */
        .product-image {
            max-width: 200px;
            max-height: 200px;
            border-radius: 12px;
            border: 1px solid rgba(148, 163, 184, 0.1);
            object-fit: cover;
        }

        /* ── Actions ── */
        .actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .actions a {
            margin-right: 0;
            font-size: 13px;
            padding: 6px 14px;
        }

        /* ── Links ── */
        a {
            color: #818cf8;
            transition: color 0.2s ease;
        }
        a:hover {
            color: #a78bfa;
        }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            nav {
                flex-direction: column;
                height: auto;
                padding: 16px 20px;
                gap: 12px;
            }
            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
                gap: 4px;
            }
            .nav-user {
                margin-left: 0;
                padding-left: 0;
                border-left: none;
                padding-top: 8px;
                border-top: 1px solid rgba(148, 163, 184, 0.1);
            }
            .container {
                margin: 20px auto;
                padding: 0 16px;
            }
            .card { padding: 20px; border-radius: 12px; }
            h1 { font-size: 22px; }
            h2 { font-size: 18px; }
            .search-bar {
                flex-direction: column;
            }
            table { font-size: 13px; }
            table th, table td { padding: 10px 12px; }
        }

        /* ── Footer accent line ── */
        body::after {
            content: '';
            display: block;
            height: 3px;
            background: linear-gradient(90deg, transparent, #6366f1, #a78bfa, #6366f1, transparent);
            margin-top: 60px;
        }
    </style>
</head>
<body>
    <nav>
        <div class="nav-brand">&#9670; Inventory System</div>
        <div class="nav-links">
            <a href="index.php?page=dashboard">Dashboard</a>
            <a href="index.php?page=products">Products</a>
            <!-- INSECURE: Admin link visible to all users -->
            <a href="index.php?page=admin">Admin Panel</a>
            <?php if ($username): ?>
                <div class="nav-user">
                    <span class="nav-user-badge"><?php echo $username; ?></span>
                    <a href="index.php?page=logout">Logout</a>
                </div>
            <?php else: ?>
                <div class="nav-auth">
                    <a href="index.php?page=login">Login</a>
                    <a href="index.php?page=register">Register</a>
                </div>
            <?php endif; ?>
        </div>
    </nav>
    <div class="container">
        <?php echo $content; ?>
    </div>
</body>
</html>
<?php
}
?>
