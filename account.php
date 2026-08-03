<?php
require_once __DIR__ . '/config/functions.php';

if (!is_logged_in()) redirect('login.php');

$customer = current_customer();

$stmt = db()->prepare("SELECT o.*, GROUP_CONCAT(CONCAT(oi.product_name, ' ×', oi.quantity) SEPARATOR ', ') AS items FROM orders o LEFT JOIN order_items oi ON o.id = oi.order_id WHERE o.customer_id = ? GROUP BY o.id ORDER BY o.created_at DESC");
$stmt->execute([$customer['id']]);
$orders = $stmt->fetchAll();

$pageTitle = 'My Account';
include __DIR__ . '/includes/header.php';
?>

<div class="breadcrumb container">
    <a href="index.php">Home</a><span class="sep">/</span>
    <span>My Account</span>
</div>

<section class="section">
    <div class="container">
        <?php if ($success = flash('success')): ?>
            <div class="alert alert-success"><?= e($success) ?></div>
        <?php endif; ?>

        <div class="grid grid-2" style="gap:2rem;align-items:start">
            <div class="form-card" style="max-width:none;box-shadow:var(--shadow-sm)">
                <h2>Profile Information</h2>
                <div class="mt-3">
                    <p><strong>Name:</strong> <?= e($customer['name']) ?></p>
                    <p><strong>Email:</strong> <?= e($customer['email']) ?></p>
                    <p><strong>Phone:</strong> <?= e($customer['phone'] ?: 'Not provided') ?></p>
                    <p><strong>Address:</strong> <?= e($customer['address'] ?: 'Not provided') ?></p>
                    <p><strong>City:</strong> <?= e($customer['city'] ?: 'Not provided') ?></p>
                </div>
                <a href="logout.php" class="btn btn-outline btn-sm mt-3">Sign Out</a>
            </div>

            <div>
                <h2 class="mb-3">Order History</h2>
                <?php if (empty($orders)): ?>
                    <div class="empty-state">
                        <div class="icon">📦</div>
                        <p>You haven't placed any orders yet.</p>
                        <a href="collections.php" class="btn btn-primary mt-2">Start Shopping</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($orders as $o): ?>
                        <div class="order-summary mb-2">
                            <div class="flex-between mb-2">
                                <strong>Order #<?= $o['id'] ?></strong>
                                <span class="badge <?= $o['status'] == 'delivered' ? 'badge-new' : ($o['status'] == 'cancelled' ? 'badge-sale' : 'badge-stock') ?>"><?= ucfirst($o['status']) ?></span>
                            </div>
                            <p class="text-muted" style="font-size:0.85rem"><?= e($o['items']) ?></p>
                            <div class="flex-between mt-2">
                                <span class="text-muted" style="font-size:0.85rem"><?= date('M j, Y', strtotime($o['created_at'])) ?></span>
                                <strong><?= money($o['total']) ?></strong>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
