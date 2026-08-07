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

<div class="admin-stats-grid">
    <div class="admin-stat-card admin-stat-revenue">
        <div class="admin-stat-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
        <div class="admin-stat-label">Total Revenue</div>
        <div class="admin-stat-value"><?= money($stats['revenue']) ?></div>
    </div>
    <div class="admin-stat-card admin-stat-orders">
        <div class="admin-stat-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
        </div>
        <div class="admin-stat-label">Total Orders</div>
        <div class="admin-stat-value"><?= $stats['orders'] ?></div>
    </div>
    <div class="admin-stat-card admin-stat-pending">
        <div class="admin-stat-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div class="admin-stat-label">Pending Orders</div>
        <div class="admin-stat-value"><?= $stats['pending'] ?></div>
    </div>
    <div class="admin-stat-card admin-stat-products">
        <div class="admin-stat-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
        </div>
        <div class="admin-stat-label">Products</div>
        <div class="admin-stat-value"><?= $stats['products'] ?></div>
    </div>
    <div class="admin-stat-card admin-stat-customers">
        <div class="admin-stat-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div class="admin-stat-label">Customers</div>
        <div class="admin-stat-value"><?= $stats['customers'] ?></div>
    </div>
    <div class="admin-stat-card admin-stat-reviews">
        <div class="admin-stat-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
        </div>
        <div class="admin-stat-label">Pending Reviews</div>
        <div class="admin-stat-value"><?= $stats['reviews'] ?></div>
    </div>
</div>

<h2 class="mb-3 mt-4">Recent Orders</h2>
<?php if (empty($recentOrders)): ?>
    <div class="empty-state">
        <p>No orders yet. Orders will appear here once customers start placing them.</p>
    </div>
<?php else: ?>
    <table class="admin-table">
        <thead>
            <tr><th>Order ID</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th><th></th></tr>
        </thead>
        <tbody>
            <?php foreach ($recentOrders as $o): ?>
                <tr>
                    <td><strong>#<?= (int)$o['id'] ?></strong></td>
                    <td><?= e($o['customer_name']) ?><br><small class="text-muted"><?= e($o['customer_phone']) ?></small></td>
                    <td><?= money($o['total']) ?></td>
                    <td><span class="badge <?= $o['status'] == 'delivered' ? 'badge-new' : ($o['status'] == 'cancelled' ? 'badge-sale' : 'badge-featured') ?>"><?= ucfirst($o['status']) ?></span></td>
                    <td><?= date('M j, Y', strtotime($o['created_at'])) ?></td>
                    <td><a href="orders.php?action=view&id=<?= $o['id'] ?>" class="btn btn-ghost btn-sm">View</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
