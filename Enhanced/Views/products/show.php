<?php require_once __DIR__ . '/../layout.php'; ?>
<?php
ob_start();
?>
<div style="max-width: 800px; margin: 0 auto;">
    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="padding: 30px; border-bottom: 1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center;">
            <h2>Product Details</h2>
            <a href="index.php?page=products" class="btn" style="background:var(--bg-tertiary); color:var(--text-main); padding: 8px 16px; font-size:13px;">Back to Inventory</a>
        </div>

        <div style="padding: 30px;">
            <table style="margin-bottom: 30px;">
                <tr>
                    <th style="width: 200px;">Property</th>
                    <th>Value</th>
                </tr>
                <tr>
                    <td><strong>Product ID</strong></td>
                    <td><?php echo htmlspecialchars($product['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
                <tr>
                    <td><strong>Product Name</strong></td>
                    <td style="font-weight: 600; font-size: 16px; color:#a5b4fc;"><?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
                <tr>
                    <td><strong>Description</strong></td>
                    <td style="white-space: pre-line; color: var(--text-muted);"><?php echo htmlspecialchars($product['description'] ?: 'No description provided.', ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
                <tr>
                    <td><strong>Price</strong></td>
                    <td>RM <?php echo number_format((float)$product['price'], 2); ?></td>
                </tr>
                <tr>
                    <td><strong>Quantity in Stock</strong></td>
                    <td><?php echo htmlspecialchars($product['quantity'], ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
                <tr>
                    <td><strong>Added By</strong></td>
                    <td>
                        <span class="user-badge" style="font-size:12px;">
                            <?php echo htmlspecialchars($product['username'] ?: 'System', ENT_QUOTES, 'UTF-8'); ?>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td><strong>Created At</strong></td>
                    <td><?php echo htmlspecialchars($product['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
                <?php if ($product['image']): ?>
                <tr>
                    <td><strong>Product Image</strong></td>
                    <td>
                        <img src="uploads/<?php echo htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8'); ?>" class="product-image" style="max-width: 300px; max-height: 300px; border-radius: 12px; margin-top: 10px;">
                    </td>
                </tr>
                <?php endif; ?>
            </table>

            <div style="display: flex; gap: 12px; justify-content: flex-end; border-top: 1px solid var(--border-color); padding-top: 25px;">
                <!-- SECURE: Only show edit/delete if user has permissions -->
                <?php if ($_SESSION['role'] === 'admin' || $_SESSION['user_id'] == $product['user_id']): ?>
                    <a href="index.php?page=products&action=edit&id=<?php echo htmlspecialchars($product['id'], ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-warning">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        Edit Product
                    </a>
                    
                    <!-- SECURE: Use POST for state-modifying action (Delete) and include CSRF token -->
                    <form method="POST" action="index.php?page=products&action=delete&id=<?php echo htmlspecialchars($product['id'], ENT_QUOTES, 'UTF-8'); ?>" onsubmit="return confirm('Are you sure you want to delete this product?')">
                        <?php echo CSRFMiddleware::getTokenField(); ?>
                        <button type="submit" class="btn btn-danger">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                            Delete Product
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
renderLayout('Product Details', $content);
?>
