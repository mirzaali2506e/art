<?php
require_once __DIR__ . '/../config/functions.php';
require_admin();

$action = $_GET['action'] ?? 'list';
$orderId = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf'] ?? '')) {
        flash('error', 'Invalid request.');
        redirect('orders.php');
    }
    $status = $_POST['status'] ?? 'pending';
    db()->prepare("UPDATE orders SET status = ? WHERE id = ?")->execute([$status, (int)$_POST['order_id']]);
    flash('success', 'Order status updated.');
    redirect('orders.php');
}

$statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
$filter = $_GET['status'] ?? '';

if ($filter && in_array($filter, $statuses)) {
    $stmt = db()->prepare("SELECT * FROM orders WHERE status = ? ORDER BY created_at DESC");
    $stmt->execute([$filter]);
} else {
    $stmt = db()->query("SELECT * FROM orders ORDER BY created_at DESC");
}
$orders = $stmt->fetchAll();

$viewOrder = null;
$orderItems = [];
if ($action === 'view' && $orderId) {
    $stmt = db()->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$orderId]);
    $viewOrder = $stmt->fetch();
    if ($viewOrder) {
        $stmt = db()->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $stmt->execute([$orderId]);
        $orderItems = $stmt->fetchAll();
    }
}

$pageTitle = 'Orders';
$activePage = 'orders';
include __DIR__ . '/includes/header.php';
?>

<div class="admin-header">
    <h1><?= $action === 'view' ? 'Order #'.$orderId : 'Orders' ?></h1>
    <?php if ($action === 'view'): ?>
        <a href="orders.php" class="btn btn-outline">← Back to Orders</a>
    <?php endif; ?>
</div>

<?php if ($success = flash('success')): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>

<?php if ($action === 'view' && $viewOrder): ?>
    <div class="grid grid-2" style="gap:2rem;align-items:start">
        <div class="form-card" style="max-width:none;box-shadow:var(--shadow-sm)">
            <h3>Customer Details</h3>
            <div class="mt-2" style="line-height:2">
                <p><strong>Name:</strong> <?= e($viewOrder['customer_name']) ?></p>
                <p><strong>Email:</strong> <?= e($viewOrder['customer_email']) ?></p>
                <p><strong>Phone:</strong> <?= e($viewOrder['customer_phone']) ?></p>
                <p><strong>Address:</strong> <?= e($viewOrder['shipping_address']) ?></p>
                <p><strong>City:</strong> <?= e($viewOrder['city']) ?></p>
                <?php if ($viewOrder['notes']): ?><p><strong>Notes:</strong> <?= e($viewOrder['notes']) ?></p><?php endif; ?>
                <p><strong>Date:</strong> <?= date('M j, Y g:i A', strtotime($viewOrder['created_at'])) ?></p>
            </div>
        </div>
        <div class="form-card" style="max-width:none;box-shadow:var(--shadow-sm)">
            <h3>Order Items</h3>
            <table style="width:100%;margin-top:1rem">
                <?php foreach ($orderItems as $item): ?>
                    <tr style="border-bottom:1px solid var(--neutral-100)">
                        <td style="padding:0.5rem 0"><?= e($item['product_name']) ?></td>
                        <td style="text-align:center">×<?= $item['quantity'] ?></td>
                        <td style="text-align:right"><?= money($item['price'] * $item['quantity']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr style="border-top:2px solid var(--neutral-200)">
                    <td colspan="2" style="text-align:right;padding-top:0.5rem"><strong>Subtotal:</strong></td>
                    <td style="text-align:right"><?= money($viewOrder['subtotal']) ?></td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:right"><strong>Shipping:</strong></td>
                    <td style="text-align:right"><?= money($viewOrder['shipping_fee']) ?></td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:right;font-size:1.1rem"><strong>Total:</strong></td>
                    <td style="text-align:right;font-size:1.1rem;color:var(--primary-700)"><strong><?= money($viewOrder['total']) ?></strong></td>
                </tr>
            </table>

            <form method="post" class="mt-4">
                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                <input type="hidden" name="order_id" value="<?= $viewOrder['id'] ?>">
                <div class="form-group">
                    <label>Update Status</label>
                    <select name="status" class="form-control">
                        <?php foreach ($statuses as $s): ?>
                            <option value="<?= $s ?>" <?= $viewOrder['status'] == $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Update Status</button>
                <a href="https://wa.me/<?= e(preg_replace('/\D/', '', $viewOrder['customer_phone'])) ?>?text=<?= urlencode("Hi {$viewOrder['customer_name']}, your Tooba Art Collection order #{$viewOrder['id']} is now {$viewOrder['status']}.") ?>" class="btn btn-accent" target="_blank">WhatsApp Customer</a>
            </form>
        </div>
    </div>
<?php else: ?>
    <div class="mb-3 flex gap-1" style="flex-wrap:wrap">
        <a href="orders.php" class="btn btn-sm <?= !$filter ? 'btn-primary' : 'btn-outline' ?>">All</a>
        <?php foreach ($statuses as $s): ?>
            <a href="orders.php?status=<?= $s ?>" class="btn btn-sm <?= $filter == $s ? 'btn-primary' : 'btn-outline' ?>"><?= ucfirst($s) ?></a>
        <?php endforeach; ?>
    </div>

    <table class="admin-table">
        <thead>
            <tr><th>Order ID</th><th>Customer</th><th>Phone</th><th>Items</th><th>Total</th><th>Status</th><th>Date</th><th></th></tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $o):
                $cnt = db()->prepare("SELECT SUM(quantity) FROM order_items WHERE order_id = ?");
                $cnt->execute([$o['id']]);
                $itemCount = $cnt->fetchColumn();
            ?>
                <tr>
                    <td>#<?= $o['id'] ?></td>
                    <td><?= e($o['customer_name']) ?></td>
                    <td><?= e($o['customer_phone']) ?></td>
                    <td><?= $itemCount ?> item(s)</td>
                    <td><?= money($o['total']) ?></td>
                    <td><span class="badge <?= $o['status'] == 'delivered' ? 'badge-new' : ($o['status'] == 'cancelled' ? 'badge-sale' : 'badge-stock') ?>"><?= ucfirst($o['status']) ?></span></td>
                    <td><?= date('M j, Y', strtotime($o['created_at'])) ?></td>
                    <td><a href="orders.php?action=view&id=<?= $o['id'] ?>" class="btn btn-ghost btn-sm">View</a></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($orders)): ?>
                <tr><td colspan="8" class="text-muted text-center" style="padding:2rem">No orders yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
