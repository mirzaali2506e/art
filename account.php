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
$deliveredCount = 0;
foreach ($orders as $o) {
    if ($o['status'] != 'cancelled') $totalSpent += $o['total'];
    if (in_array($o['status'], ['pending', 'processing'])) $pendingCount++;
    if ($o['status'] == 'delivered') $deliveredCount++;
}
$initial = strtoupper(substr($customer['name'], 0, 1));

$statusColors = [
    'pending'    => 'status-pending',
    'processing' => 'status-processing',
    'shipped'    => 'status-shipped',
    'delivered'  => 'status-delivered',
    'cancelled'  => 'status-cancelled',
];

$pageTitle = 'My Account';
include __DIR__ . '/includes/header.php';
?>

<div class="breadcrumb container">
    <a href="index.php">Home</a><span class="sep">/</span>
    <span>My Account</span>
</div>

<!-- Account Hero Banner -->
<section class="account-hero">
    <div class="container">
        <div class="account-hero-inner">
            <div class="account-avatar"><?= e($initial) ?></div>
            <div class="account-hero-text">
                <h1><?= e($customer['name']) ?></h1>
                <p><?= e($customer['email']) ?></p>
                <p class="member-since">Member since <?= date('F Y', strtotime($customer['created_at'])) ?></p>
            </div>
            <a href="logout.php" class="btn btn-outline btn-sm account-logout">Sign Out</a>
        </div>
    </div>
</section>

<section class="section account-body">
    <div class="container">

        <!-- Stats Row -->
        <div class="account-stats">
            <div class="account-stat-card">
                <div class="stat-icon stat-icon-blue">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                </div>
                <div class="stat-text">
                    <div class="num"><?= $totalOrders ?></div>
                    <div class="label">Total Orders</div>
                </div>
            </div>
            <div class="account-stat-card">
                <div class="stat-icon stat-icon-green">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                <div class="stat-text">
                    <div class="num"><?= money($totalSpent) ?></div>
                    <div class="label">Total Spent</div>
                </div>
            </div>
            <div class="account-stat-card">
                <div class="stat-icon stat-icon-amber">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div class="stat-text">
                    <div class="num"><?= $pendingCount ?></div>
                    <div class="label">In Progress</div>
                </div>
            </div>
            <div class="account-stat-card">
                <div class="stat-icon stat-icon-teal">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <div class="stat-text">
                    <div class="num"><?= $deliveredCount ?></div>
                    <div class="label">Delivered</div>
                </div>
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="account-layout">
            <!-- Left: Profile Card -->
            <div class="account-sidebar">
                <div class="account-card">
                    <div class="account-card-header">
                        <h2>Profile</h2>
                    </div>
                    <div class="account-info-list">
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
                    </div>
                </div>

                <a href="collections.php" class="btn btn-primary btn-block account-shop-btn">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                    Continue Shopping
                </a>
            </div>

            <!-- Right: Order History -->
            <div class="account-main">
                <div class="account-main-head">
                    <h2>Order History</h2>
                    <?php if (!empty($orders)): ?>
                        <span class="order-count-badge"><?= $totalOrders ?> order<?= $totalOrders > 1 ? 's' : '' ?></span>
                    <?php endif; ?>
                </div>

                <?php if (empty($orders)): ?>
                    <div class="empty-state account-empty">
                        <div class="empty-icon">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                        </div>
                        <h3>No orders yet</h3>
                        <p>You haven't placed any orders yet. Start exploring our beautiful bead collection!</p>
                        <a href="collections.php" class="btn btn-primary mt-3">Browse Collections</a>
                    </div>
                <?php else: ?>
                    <div class="order-list">
                        <?php foreach ($orders as $o): ?>
                            <div class="order-card">
                                <div class="order-card-top">
                                    <div class="order-id">
                                        <span class="order-hash">#</span><?= (int)$o['id'] ?>
                                    </div>
                                    <span class="order-status <?= $statusColors[$o['status']] ?? 'status-pending' ?>"><?= ucfirst($o['status']) ?></span>
                                </div>
                                <div class="order-items-text"><?= e($o['items'] ?: 'Items') ?></div>
                                <div class="order-card-bottom">
                                    <span class="order-date">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                        <?= date('M j, Y', strtotime($o['created_at'])) ?>
                                    </span>
                                    <span class="order-total"><?= money($o['total']) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
