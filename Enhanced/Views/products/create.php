<?php require_once __DIR__ . '/../layout.php'; ?>
<?php
ob_start();
?>
<div style="max-width: 700px; margin: 0 auto;">
    <div class="card">
        <h2>Add New Product</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger" style="flex-direction: column; align-items: flex-start; gap: 6px;">
                <div style="display:flex; align-items:center; gap: 8px; font-weight:600;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    Validation Errors:
                </div>
                <ul style="margin-left: 26px; font-size: 13px;">
                    <?php foreach ($errors as $field => $err): ?>
                        <li><?php echo htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- SECURE: CSRF protection, restricted file upload -->
        <form method="POST" action="index.php?page=products&action=store" enctype="multipart/form-data">
            <!-- SECURE: Include CSRF Token -->
            <?php echo CSRFMiddleware::getTokenField(); ?>

            <div class="form-group">
                <label for="name">Product Name</label>
                <!-- SECURE: HTML5 input validation -->
                <input type="text" id="name" name="name" class="form-control" 
                       required maxlength="100" 
                       value="<?php echo isset($name) ? htmlspecialchars($name, ENT_QUOTES, 'UTF-8') : ''; ?>"
                       placeholder="Enter product name">
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" 
                          placeholder="Enter product description"><?php echo isset($description) ? htmlspecialchars($description, ENT_QUOTES, 'UTF-8') : ''; ?></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label for="price">Price (RM)</label>
                    <input type="number" id="price" name="price" class="form-control" 
                           required min="0.01" step="0.01"
                           value="<?php echo isset($price) ? htmlspecialchars($price, ENT_QUOTES, 'UTF-8') : ''; ?>"
                           placeholder="0.00">
                </div>

                <div class="form-group">
                    <label for="quantity">Quantity</label>
                    <input type="number" id="quantity" name="quantity" class="form-control" 
                           required min="0" step="1"
                           value="<?php echo isset($quantity) ? htmlspecialchars($quantity, ENT_QUOTES, 'UTF-8') : ''; ?>"
                           placeholder="0">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 24px;">
                <label for="image">Product Image (JPG, PNG, GIF - Max 2MB)</label>
                <!-- SECURE: Accept attribute to limit client-side file selection -->
                <input type="file" id="image" name="image" class="form-control" accept="image/jpeg, image/png, image/gif">
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <a href="index.php?page=products" class="btn" style="background:var(--bg-tertiary); color:var(--text-main);">Cancel</a>
                <button type="submit" class="btn btn-success">Save Product</button>
            </div>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
renderLayout('Add Product', $content);
?>
