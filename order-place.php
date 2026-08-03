<?php
require_once __DIR__ . '/config/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('index.php');

if (!csrf_verify($_POST['csrf'] ?? '')) {
    flash('error', 'Invalid request. Please try again.');
    redirect('checkout.php');
}

$cart = get_cart();
if (empty($cart)) redirect('cart.php');

$subtotal = cart_subtotal();
$shipping = $subtotal >= 5000 ? 0 : (float)setting('shipping_fee', 320);
$total = $subtotal + $shipping;

$customer_id = is_logged_in() ? $_SESSION['customer_id'] : null;

try {
    db()->beginTransaction();

    $stmt = db()->prepare("INSERT INTO orders (customer_id, customer_name, customer_email, customer_phone, shipping_address, city, subtotal, shipping_fee, total, status, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?)");
    $stmt->execute([
        $customer_id,
        e($_POST['customer_name']),
        e($_POST['customer_email']),
        e($_POST['customer_phone']),
        e($_POST['shipping_address']),
        e($_POST['city']),
        $subtotal,
        $shipping,
        $total,
        e($_POST['notes'] ?? ''),
    ]);
    $order_id = db()->lastInsertId();

    $item_stmt = db()->prepare("INSERT INTO order_items (order_id, product_id, product_name, price, quantity, image) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($cart as $item) {
        $item_stmt->execute([
            $order_id,
            $item['id'],
            $item['name'],
            $item['price'],
            $item['quantity'],
            $item['image'],
        ]);
        // decrement stock
        db()->prepare("UPDATE products SET stock = stock - ? WHERE id = ?")->execute([$item['quantity'], $item['id']]);
    }

    db()->commit();
} catch (Exception $ex) {
    db()->rollBack();
    flash('error', 'Something went wrong placing your order. Please try again.');
    redirect('checkout.php');
}

clear_cart();

$pageTitle = 'Order Confirmed';
include __DIR__ . '/includes/header.php';
?>

<div class="breadcrumb container">
    <a href="index.php">Home</a><span class="sep">/</span>
    <span>Order Confirmed</span>
</div>

<section class="section">
    <div class="container" style="max-width:640px">
        <div class="form-card text-center" style="box-shadow:var(--shadow-md)">
            <div style="font-size:4rem;margin-bottom:1rem">✅</div>
            <h1>Thank You for Your Order!</h1>
            <p class="text-muted mt-2">Your order has been placed successfully. We'll contact you on WhatsApp shortly to confirm.</p>

            <div class="alert alert-info mt-4 text-center" style="text-align:left">
                <p><strong>Order ID:</strong> #<?= $order_id ?></p>
                <p><strong>Total:</strong> <?= money($total) ?> (Cash on Delivery)</p>
                <p><strong>Shipping to:</strong> <?= e($_POST['shipping_address']) ?>, <?= e($_POST['city']) ?></p>
                <p><strong>Phone:</strong> <?= e($_POST['customer_phone']) ?></p>
            </div>

            <div class="mt-3">
                <a href="https://wa.me/<?= e(preg_replace('/\D/', '', setting('whatsapp'))) ?>?text=<?= urlencode("Hi! I just placed order #$order_id on BeadCraft Store. Please confirm.") ?>" class="btn btn-accent btn-lg" target="_blank">Confirm on WhatsApp</a>
            </div>
            <p class="text-muted mt-2" style="font-size:0.85rem">Save your Order ID for tracking. You can view your orders in your account.</p>

            <div class="mt-4">
                <a href="collections.php" class="btn btn-outline">Continue Shopping</a>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
