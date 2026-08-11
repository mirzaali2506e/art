<?php
require_once __DIR__ . '/config/functions.php';

$customer = current_customer();

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
        <div id="buy-now-notice" class="alert alert-info" style="display:none">
            <strong>Direct Purchase:</strong> You're checking out a single item. Your cart is not affected.
        </div>

        <?php if ($error = flash('error')): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php endif; ?>

        <div id="checkout-empty" class="empty-state" style="display:none">
            <div class="icon">🛒</div>
            <h2>Your cart is empty</h2>
            <p>Add some products before checking out.</p>
            <a href="collections.php" class="btn btn-primary mt-3">Start Shopping</a>
        </div>

        <form id="checkout-form" method="post" action="order-place.php" class="grid checkout-layout" style="gap:2rem;align-items:start;display:none">
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            <input type="hidden" name="cart_json" id="cart-json" value="">
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
                <div id="checkout-items"></div>
                <div class="summary-row"><span>Subtotal</span><span id="checkout-subtotal">PKR 0</span></div>
                <div class="summary-row"><span>Shipping</span><span id="checkout-shipping">PKR 0</span></div>
                <div class="summary-row total"><span>Total</span><span id="checkout-total">PKR 0</span></div>
                <button type="submit" class="btn btn-primary btn-block btn-lg mt-2">Complete Order</button>
                <p class="text-muted text-center mt-2" style="font-size:0.85rem">By placing this order you agree to our terms.</p>
            </div>
        </form>
    </div>
</section>

<script>
var CART_KEY = 'tooba_cart';
function getCart() {
    try { return JSON.parse(localStorage.getItem(CART_KEY) || '{}'); }
    catch(e) { return {}; }
}
function fmt(n) { return 'PKR ' + Math.round(n).toLocaleString(); }

var params = new URLSearchParams(window.location.search);
var isBuyNow = params.get('buy_now') === '1';
var items;

if (isBuyNow) {
    try { items = JSON.parse(localStorage.getItem('tooba_buy_now') || '[]'); }
    catch(e) { items = []; }
} else {
    var cart = getCart();
    items = Object.values(cart);
}

var empty = items.length === 0;

document.getElementById('checkout-empty').style.display = empty ? '' : 'none';
document.getElementById('checkout-form').style.display = empty ? 'none' : '';
document.getElementById('buy-now-notice').style.display = isBuyNow ? '' : 'none';

if (!empty) {
    var itemsHtml = '';
    var subtotal = 0;
    items.forEach(function(item) {
        var lineTotal = item.price * item.quantity;
        subtotal += lineTotal;
        itemsHtml += '<div class="summary-row"><span>' + (item.name || '') + ' × ' + item.quantity + '</span><span>' + fmt(lineTotal) + '</span></div>';
    });
    document.getElementById('checkout-items').innerHTML = itemsHtml;
    var shipping = subtotal >= 5000 ? 0 : 320;
    document.getElementById('checkout-subtotal').textContent = fmt(subtotal);
    document.getElementById('checkout-shipping').textContent = shipping === 0 ? 'Free' : fmt(shipping);
    document.getElementById('checkout-total').textContent = fmt(subtotal + shipping);
    document.getElementById('cart-json').value = JSON.stringify(items);
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
