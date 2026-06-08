<?php require_once __DIR__ . '/../layout.php'; ?>
<?php
ob_start();
?>
<h1>Products</h1>

<!-- Search Bar -->
<div class="search-bar">
    <form method="GET" action="index.php" style="display:flex; gap:10px; width:100%;">
        <input type="hidden" name="page" value="products">
        <!-- INSECURE: Search value echoed without encoding -->
        <input type="text" name="search" placeholder="Search products..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
        <button type="submit" class="btn btn-primary">Search</button>
    </form>
</div>

<a href="index.php?page=products&action=create" class="btn btn-success" style="margin-bottom:20px;display:inline-block;">Add New Product</a>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price (RM)</th>
                <th>Quantity</th>
                <th>Added By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo $product['id']; ?></td>
                    <!-- INSECURE: Output not encoded - XSS vulnerable -->
                    <td><?php echo $product['name']; ?></td>
                    <td><?php echo $product['price']; ?></td>
                    <td><?php echo $product['quantity']; ?></td>
                    <td><?php echo $product['username']; ?></td>
                    <td class="actions">
                        <a href="index.php?page=products&action=show&id=<?php echo $product['id']; ?>" class="btn btn-primary">View</a>
                        <a href="index.php?page=products&action=edit&id=<?php echo $product['id']; ?>" class="btn btn-warning">Edit</a>
                        <!-- INSECURE: Delete via GET request, no CSRF -->
                        <a href="index.php?page=products&action=delete&id=<?php echo $product['id']; ?>" class="btn btn-danger" onclick="return confirm('Delete this product?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6">No products found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php
$content = ob_get_clean();
renderLayout('Products', $content);
?>
