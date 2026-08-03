<?php
require_once __DIR__ . '/config/functions.php';

$cart = get_cart();

// Buy now support
if (isset($_GET['buy_now']) && !empty($_GET['buy_now'])) {
    add_to_cart((int)$_GET['buy_now'], 1);
    $cart = get_cart();
}

if (empty($cart)) redirect('cart.php');

$customer = current_customer();
$subtotal = cart_subtotal();
$shipping = $subtotal >= 5000 ? 0 : (float)setting('shipping_fee', 320);
$total = $subtotal + $shipping;

$pageTitle = 'Checkout';
include __DIR__ . '/includes/header.php';
?>

<div class="breadcrumb container">
    <a href="index.php">Home</a><span class="sep">/</span>
    <a href="cart.php">Cart</a><span class="sep">/</span>
    <span>Checkout</span>
</div>

<section class="section">
    <div class="container">
        <h1 class="mb-4">Checkout</h1>

        <?php if ($error = flash('error')): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="post" action="order-place.php" class="grid" style="grid-template-columns:2fr 1fr;gap:2rem;align-items:start">
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            <div class="form-card" style="max-width:none;box-shadow:var(--shadow-sm)">
                <h3 class="mb-3">Shipping Details</h3>
                <div class="grid grid-2">
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" name="customer_name" class="form-control" required value="<?= e($customer['name'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Phone Number *</label>
                        <input type="tel" name="customer_phone" class="form-control" required value="<?= e($customer['phone'] ?? '') ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="customer_email" class="form-control" required value="<?= e($customer['email'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Shipping Address *</label>
                    <textarea name="shipping_address" class="form-control" rows="3" required placeholder="House #, Street, Area"><?= e($customer['address'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>City *</label>
                    <input type="text" name="city" class="form-control" required value="<?= e($customer['city'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Order Notes (optional)</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Any special instructions..."></textarea>
                </div>

                <h3 class="mb-2 mt-4">Payment Method</h3>
                <div class="alert alert-info">
                    <strong>Cash on Delivery</strong> — Pay when your order arrives. We'll contact you on WhatsApp to confirm.
                </div>
            </div>

            <div class="order-summary" style="position:sticky;top:100px">
                <h3>Your Order</h3>
                <?php foreach ($cart as $item): ?>
                    <div class="summary-row">
                        <span><?= e($item['name']) ?> × <?= $item['quantity'] ?></span>
                        <span><?= money($item['price'] * $item['quantity']) ?></span>
                    </div>
                <?php endforeach; ?>
                <div class="summary-row"><span>Subtotal</span><span><?= money($subtotal) ?></span></div>
                <div class="summary-row"><span>Shipping</span><span><?= $shipping == 0 ? 'Free' : money($shipping) ?></span></div>
                <div class="summary-row total"><span>Total</span><span><?= money($total) ?></span></div>
                <button type="submit" class="btn btn-primary btn-block btn-lg mt-2">Complete Order</button>
                <p class="text-muted text-center mt-2" style="font-size:0.85rem">By placing this order you agree to our terms.</p>
            </div>
        </form>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
