<?php require_once __DIR__ . '/../layout.php'; ?>
<?php
ob_start();
?>
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 24px;">
    <h1>Products Inventory</h1>
    <a href="index.php?page=products&action=create" class="btn btn-success">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add Product
    </a>
</div>

<!-- Search Bar -->
<div class="search-bar card" style="padding: 15px; margin-bottom: 24px;">
    <form method="GET" action="index.php" style="display:flex; gap:12px; width:100%; align-items:center;">
        <input type="hidden" name="page" value="products">
        <div style="position:relative; flex:1; display:flex; align-items:center;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position:absolute; left:12px; color:var(--text-muted);"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <!-- SECURE: Output encoding on search term -->
            <input type="text" name="search" class="form-control" style="padding-left: 40px;" placeholder="Search products by name or description..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8') : ''; ?>">
        </div>
        <button type="submit" class="btn btn-primary">Search</button>
        <?php if (isset($_GET['search']) && $_GET['search'] !== ''): ?>
            <a href="index.php?page=products" class="btn btn-danger" style="background:var(--bg-tertiary); box-shadow:none; color:var(--text-main);">Clear</a>
        <?php endif; ?>
    </form>
</div>

<div class="card" style="padding: 0; overflow-x: auto;">
    <table>
        <thead>
            <tr>
                <th style="width: 80px;">ID</th>
                <th>Product Name</th>
                <th>Price (RM)</th>
                <th>Quantity</th>
                <th>Added By</th>
                <th style="width: 260px; text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <!-- SECURE: Output encoded to prevent XSS -->
                    <td style="font-weight: 600;"><?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td>RM <?php echo number_format((float)$product['price'], 2); ?></td>
                    <td>
                        <?php if ($product['quantity'] <= 5): ?>
                            <span style="color: var(--accent-danger); font-weight:700;"><?php echo htmlspecialchars($product['quantity'], ENT_QUOTES, 'UTF-8'); ?> (Low Stock)</span>
                        <?php else: ?>
                            <?php echo htmlspecialchars($product['quantity'], ENT_QUOTES, 'UTF-8'); ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="user-badge" style="background:rgba(255,255,255,0.03); color:var(--text-muted); border-color:var(--border-color); font-size:12px; padding: 4px 8px;">
                            <?php echo htmlspecialchars($product['username'] ?: 'System', ENT_QUOTES, 'UTF-8'); ?>
                        </span>
                    </td>
                    <td style="text-align: right;">
                        <div class="actions" style="justify-content: flex-end;">
                            <a href="index.php?page=products&action=show&id=<?php echo htmlspecialchars($product['id'], ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary" style="padding: 6px 12px; font-size:12px; background:var(--bg-tertiary); border:1px solid var(--border-color); box-shadow:none;">View</a>
                            
                            <!-- SECURE: Only show edit/delete if user has permissions -->
                            <?php if ($_SESSION['role'] === 'admin' || $_SESSION['user_id'] == $product['user_id']): ?>
                                <a href="index.php?page=products&action=edit&id=<?php echo htmlspecialchars($product['id'], ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-warning" style="padding: 6px 12px; font-size:12px;">Edit</a>
                                
                                <!-- SECURE: Use POST for state-modifying action (Delete) and include CSRF token -->
                                <form method="POST" action="index.php?page=products&action=delete&id=<?php echo htmlspecialchars($product['id'], ENT_QUOTES, 'UTF-8'); ?>" onsubmit="return confirm('Are you sure you want to delete this product?')">
                                    <?php echo CSRFMiddleware::getTokenField(); ?>
                                    <button type="submit" class="btn btn-danger" style="padding: 6px 12px; font-size:12px;">Delete</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 40px;">No products found in the inventory.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php
$content = ob_get_clean();
renderLayout('Products Inventory', $content);
?>
