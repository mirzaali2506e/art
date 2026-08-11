<?php
require_once __DIR__ . '/config/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('index.php');

if (!csrf_verify($_POST['csrf'] ?? '')) {
    flash('error', 'Invalid request. Please try again.');
    redirect('checkout.php');
}

// Parse cart from the hidden JSON field (client-side localStorage cart)
$cartItems = [];
if (!empty($_POST['cart_json'])) {
    $decoded = json_decode($_POST['cart_json'], true);
    if (is_array($decoded)) $cartItems = $decoded;
}
if (empty($cartItems)) redirect('cart.php');

$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += (float)$item['price'] * (int)$item['quantity'];
}
$shipping = $subtotal >= 5000 ? 0 : 320;
$total = $subtotal + $shipping;

$orderId = 'ORD-' . date('ymd') . '-' . rand(1000, 9999);

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
                <p><strong>Order ID:</strong> #<?= e($orderId) ?></p>
                <p><strong>Total:</strong> <?= money($total) ?> (Cash on Delivery)</p>
                <p><strong>Shipping to:</strong> <?= e($_POST['shipping_address']) ?>, <?= e($_POST['city']) ?></p>
                <p><strong>Phone:</strong> <?= e($_POST['customer_phone']) ?></p>
            </div>

            <div class="mt-3">
                <a href="https://wa.me/<?= e(preg_replace('/\D/', '', setting('whatsapp', '923000000000'))) ?>?text=<?= urlencode("Hi! I just placed order #$orderId on Tooba Art Collection. Please confirm.") ?>" class="btn btn-accent btn-lg" target="_blank">Confirm on WhatsApp</a>
            </div>
            <p class="text-muted mt-2" style="font-size:0.85rem">Save your Order ID for tracking.</p>

            <div class="mt-4">
                <a href="collections.php" class="btn btn-outline">Continue Shopping</a>
            </div>
        </div>
    </div>
</section>

<script>
// Clear the cart from localStorage after successful order
localStorage.removeItem('tooba_cart');
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
