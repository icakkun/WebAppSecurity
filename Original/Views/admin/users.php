<?php require_once __DIR__ . '/../layout.php'; ?>
<?php
ob_start();
?>
<!-- INSECURE: No authorization check in the view -->
<div class="card">
    <h1>Admin Panel - User Management</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <!-- INSECURE: Password visible in admin panel -->
                <th>Password</th>
                <th>Role</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo $user['id']; ?></td>
                <td><?php echo $user['username']; ?></td>
                <td><?php echo $user['email']; ?></td>
                <!-- INSECURE: Plain text password displayed -->
                <td><?php echo $user['password']; ?></td>
                <td><?php echo $user['role']; ?></td>
                <td><?php echo $user['created_at']; ?></td>
                <td>
                    <a href="index.php?page=admin&action=delete&id=<?php echo $user['id']; ?>" class="btn btn-danger" onclick="return confirm('Delete user?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
$content = ob_get_clean();
renderLayout('Admin Panel', $content);
?>
