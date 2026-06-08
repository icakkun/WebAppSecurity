<?php require_once __DIR__ . '/../layout.php'; ?>
<?php
ob_start();
?>
<div class="card">
    <h2>Edit Product</h2>
    <!-- INSECURE: No CSRF token, values not encoded -->
    <form method="POST" action="index.php?page=products&action=update&id=<?php echo $product['id']; ?>" enctype="multipart/form-data">
        <div class="form-group">
            <label>Product Name</label>
            <input type="text" name="name" value="<?php echo $product['name']; ?>">
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description"><?php echo $product['description']; ?></textarea>
        </div>
        <div class="form-group">
            <label>Price (RM)</label>
            <input type="text" name="price" value="<?php echo $product['price']; ?>">
        </div>
        <div class="form-group">
            <label>Quantity</label>
            <input type="text" name="quantity" value="<?php echo $product['quantity']; ?>">
        </div>
        <div class="form-group">
            <label>Product Image</label>
            <?php if ($product['image']): ?>
                <p>Current: <?php echo $product['image']; ?></p>
            <?php endif; ?>
            <input type="file" name="image">
        </div>
        <button type="submit" class="btn btn-warning">Update Product</button>
        <a href="index.php?page=products" class="btn btn-danger">Cancel</a>
    </form>
</div>
<?php
$content = ob_get_clean();
renderLayout('Edit Product', $content);
?>
