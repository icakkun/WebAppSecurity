<?php require_once __DIR__ . '/../layout.php'; ?>
<?php
ob_start();
?>
<div class="card">
    <h2>Product Details</h2>
    <!-- INSECURE: All output without encoding -->
    <table>
        <tr><th>ID</th><td><?php echo $product['id']; ?></td></tr>
        <tr><th>Name</th><td><?php echo $product['name']; ?></td></tr>
        <tr><th>Description</th><td><?php echo $product['description']; ?></td></tr>
        <tr><th>Price</th><td>RM <?php echo $product['price']; ?></td></tr>
        <tr><th>Quantity</th><td><?php echo $product['quantity']; ?></td></tr>
        <tr><th>Added By</th><td><?php echo $product['username']; ?></td></tr>
        <tr><th>Created At</th><td><?php echo $product['created_at']; ?></td></tr>
        <?php if ($product['image']): ?>
        <tr><th>Image</th><td><img src="uploads/<?php echo $product['image']; ?>" class="product-image"></td></tr>
        <?php endif; ?>
    </table>
    <div style="margin-top: 20px;">
        <a href="index.php?page=products&action=edit&id=<?php echo $product['id']; ?>" class="btn btn-warning">Edit</a>
        <a href="index.php?page=products&action=delete&id=<?php echo $product['id']; ?>" class="btn btn-danger" onclick="return confirm('Delete?')">Delete</a>
        <a href="index.php?page=products" class="btn btn-primary">Back to List</a>
    </div>
</div>
<?php
$content = ob_get_clean();
renderLayout('Product Details', $content);
?>
