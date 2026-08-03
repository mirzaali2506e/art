<?php
require_once __DIR__ . '/../config/functions.php';
require_admin();

$stats = [
    'products'  => (int)db()->query("SELECT COUNT(*) FROM products")->fetchColumn(),
    'orders'    => (int)db()->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
    'pending'   => (int)db()->query("SELECT COUNT(*) FROM orders WHERE status='pending'")->fetchColumn(),
    'customers' => (int)db()->query("SELECT COUNT(*) FROM customers")->fetchColumn(),
    'revenue'   => (float)db()->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status != 'cancelled'")->fetchColumn(),
    'reviews'   => (int)db()->query("SELECT COUNT(*) FROM reviews WHERE is_approved=0")->fetchColumn(),
];

$recentOrders = db()->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5")->fetchAll();

$pageTitle = 'Dashboard';
$activePage = 'dashboard';
include __DIR__ . '/includes/header.php';
?>

<div class="admin-header">
    <h1>Dashboard</h1>
    <span class="text-muted">Welcome back, <?= e($_SESSION['admin_name'] ?? 'Admin') ?></span>
</div>

<div class="grid grid-3" style="margin-bottom:2rem">
    <div class="stat-card">
        <div class="label">Total Revenue</div>
        <div class="value"><?= money($stats['revenue']) ?></div>
    </div>
    <div class="stat-card">
        <div class="label">Total Orders</div>
        <div class="value"><?= $stats['orders'] ?></div>
    </div>
    <div class="stat-card">
        <div class="label">Pending Orders</div>
        <div class="value" style="color:var(--warning-500)"><?= $stats['pending'] ?></div>
    </div>
    <div class="stat-card">
        <div class="label">Products</div>
        <div class="value"><?= $stats['products'] ?></div>
    </div>
    <div class="stat-card">
        <div class="label">Customers</div>
        <div class="value"><?= $stats['customers'] ?></div>
    </div>
    <div class="stat-card">
        <div class="label">Pending Reviews</div>
        <div class="value" style="color:var(--warning-500)"><?= $stats['reviews'] ?></div>
    </div>
</div>

<h2 class="mb-3">Recent Orders</h2>
<?php if (empty($recentOrders)): ?>
    <p class="text-muted">No orders yet.</p>
<?php else: ?>
    <table class="admin-table">
        <thead>
            <tr><th>Order ID</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th><th></th></tr>
        </thead>
        <tbody>
            <?php foreach ($recentOrders as $o): ?>
                <tr>
                    <td>#<?= $o['id'] ?></td>
                    <td><?= e($o['customer_name']) ?><br><small class="text-muted"><?= e($o['customer_phone']) ?></small></td>
                    <td><?= money($o['total']) ?></td>
                    <td><span class="badge <?= $o['status'] == 'delivered' ? 'badge-new' : ($o['status'] == 'cancelled' ? 'badge-sale' : 'badge-stock') ?>"><?= ucfirst($o['status']) ?></span></td>
                    <td><?= date('M j, Y', strtotime($o['created_at'])) ?></td>
                    <td><a href="orders.php?action=view&id=<?= $o['id'] ?>" class="btn btn-ghost btn-sm">View</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
