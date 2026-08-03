<?php
require_once __DIR__ . '/config/functions.php';

if (!is_logged_in()) redirect('login.php');

$customer = current_customer();

$stmt = db()->prepare("SELECT o.*, GROUP_CONCAT(CONCAT(oi.product_name, ' ×', oi.quantity) SEPARATOR ', ') AS items FROM orders o LEFT JOIN order_items oi ON o.id = oi.order_id WHERE o.customer_id = ? GROUP BY o.id ORDER BY o.created_at DESC");
$stmt->execute([$customer['id']]);
$orders = $stmt->fetchAll();

$totalOrders = count($orders);
$totalSpent = 0;
$pendingCount = 0;
foreach ($orders as $o) {
    if ($o['status'] != 'cancelled') $totalSpent += $o['total'];
    if ($o['status'] == 'pending' || $o['status'] == 'processing') $pendingCount++;
}
$initial = strtoupper(substr($customer['name'], 0, 1));

$pageTitle = 'My Account';
include __DIR__ . '/includes/header.php';
?>

<div class="breadcrumb container">
    <a href="index.php">Home</a><span class="sep">/</span>
    <span>My Account</span>
</div>

<!-- Account Hero -->
<section class="account-hero">
    <div class="container">
        <div class="flex" style="gap:1.5rem;align-items:center;flex-wrap:wrap">
            <div class="account-avatar"><?= e($initial) ?></div>
            <div>
                <h1><?= e($customer['name']) ?></h1>
                <p><?= e($customer['email']) ?></p>
            </div>
            <div style="margin-left:auto">
                <a href="logout.php" class="btn btn-outline btn-sm" style="border-color:var(--primary-300);color:var(--primary-200)">Sign Out</a>
            </div>
        </div>
    </div>
</section>

<section class="section" style="padding-top:0">
    <div class="container">
        <?php if ($success = flash('success')): ?>
            <div class="alert alert-success"><?= e($success) ?></div>
        <?php endif; ?>

        <!-- Stats -->
        <div class="account-stats">
            <div class="account-stat-card">
                <div class="icon">📦</div>
                <div class="num"><?= $totalOrders ?></div>
                <div class="label">Total Orders</div>
            </div>
            <div class="account-stat-card">
                <div class="icon">💰</div>
                <div class="num"><?= money($totalSpent) ?></div>
                <div class="label">Total Spent</div>
            </div>
            <div class="account-stat-card">
                <div class="icon">⏳</div>
                <div class="num"><?= $pendingCount ?></div>
                <div class="label">In Progress</div>
            </div>
            <div class="account-stat-card">
                <div class="icon">✅</div>
                <div class="num"><?= count(array_filter($orders, fn($o) => $o['status'] == 'delivered')) ?></div>
                <div class="label">Delivered</div>
            </div>
        </div>

        <div class="grid" style="grid-template-columns:1fr;gap:2rem;align-items:start">
            <!-- Profile -->
            <div class="account-card">
                <h2>Profile Information</h2>
                <div class="account-info-row">
                    <span class="key">Name</span>
                    <span class="val"><?= e($customer['name']) ?></span>
                </div>
                <div class="account-info-row">
                    <span class="key">Email</span>
                    <span class="val"><?= e($customer['email']) ?></span>
                </div>
                <div class="account-info-row">
                    <span class="key">Phone</span>
                    <span class="val"><?= e($customer['phone'] ?: 'Not provided') ?></span>
                </div>
                <div class="account-info-row">
                    <span class="key">Address</span>
                    <span class="val"><?= e($customer['address'] ?: 'Not provided') ?></span>
                </div>
                <div class="account-info-row">
                    <span class="key">City</span>
                    <span class="val"><?= e($customer['city'] ?: 'Not provided') ?></span>
                </div>
                <div class="account-info-row">
                    <span class="key">Member Since</span>
                    <span class="val"><?= date('M j, Y', strtotime($customer['created_at'])) ?></span>
                </div>
            </div>

            <!-- Order History -->
            <div>
                <h2 class="mb-3">Order History</h2>
                <?php if (empty($orders)): ?>
                    <div class="empty-state">
                        <div class="icon">📦</div>
                        <h3>No orders yet</h3>
                        <p>You haven't placed any orders. Start exploring our collection!</p>
                        <a href="collections.php" class="btn btn-primary mt-3">Start Shopping</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($orders as $o): 
                        $statusColors = [
                            'pending' => 'badge-stock',
                            'processing' => 'badge-stock',
                            'shipped' => 'badge-new',
                            'delivered' => 'badge-new',
                            'cancelled' => 'badge-sale',
                        ];
                    ?>
                        <div class="order-card">
                            <div class="flex-between mb-2">
                                <strong>Order #<?= $o['id'] ?></strong>
                                <span class="badge <?= $statusColors[$o['status']] ?? 'badge-stock' ?>"><?= ucfirst($o['status']) ?></span>
                            </div>
                            <p class="text-muted" style="font-size:0.85rem;line-height:1.5"><?= e($o['items']) ?></p>
                            <div class="flex-between mt-2">
                                <span class="text-muted" style="font-size:0.82rem"><?= date('M j, Y', strtotime($o['created_at'])) ?></span>
                                <strong style="color:var(--primary-700)"><?= money($o['total']) ?></strong>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
