<?php
require_admin();

$customers = db()->query("SELECT c.*, (SELECT COUNT(*) FROM orders WHERE customer_id = c.id) AS order_count FROM customers c ORDER BY c.created_at DESC")->fetchAll();

$pageTitle = 'Customers';
$activePage = 'customers';
include __DIR__ . '/includes/header.php';
?>

<div class="admin-header">
    <h1>Customers</h1>
</div>

<table class="admin-table">
    <thead>
        <tr><th>Name</th><th>Email</th><th>Phone</th><th>City</th><th>Orders</th><th>Joined</th></tr>
    </thead>
    <tbody>
        <?php foreach ($customers as $c): ?>
            <tr>
                <td><?= e($c['name']) ?></td>
                <td><?= e($c['email']) ?></td>
                <td><?= e($c['phone'] ?: '—') ?></td>
                <td><?= e($c['city'] ?: '—') ?></td>
                <td><?= $c['order_count'] ?></td>
                <td><?= date('M j, Y', strtotime($c['created_at'])) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($customers)): ?>
            <tr><td colspan="6" class="text-muted text-center" style="padding:2rem">No customers registered yet.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include __DIR__ . '/includes/footer.php'; ?>
