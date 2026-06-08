<?php require_once __DIR__ . '/../layout.php'; ?>
<?php
ob_start();
?>
<div class="card">
    <h2>Add New Product</h2>
    <!-- INSECURE: No CSRF token, no input validation -->
    <form method="POST" action="index.php?page=products&action=store" enctype="multipart/form-data">
        <div class="form-group">
            <label>Product Name</label>
            <input type="text" name="name">
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description"></textarea>
        </div>
        <div class="form-group">
            <label>Price (RM)</label>
            <input type="text" name="price">
        </div>
        <div class="form-group">
            <label>Quantity</label>
            <input type="text" name="quantity">
        </div>
        <div class="form-group">
            <label>Product Image</label>
            <!-- INSECURE: No file type restriction -->
            <input type="file" name="image">
        </div>
        <button type="submit" class="btn btn-success">Add Product</button>
        <a href="index.php?page=products" class="btn btn-danger">Cancel</a>
    </form>
</div>
<?php
$content = ob_get_clean();
renderLayout('Add Product', $content);
?>
