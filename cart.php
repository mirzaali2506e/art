<?php
require_once __DIR__ . '/config/functions.php';
$pageTitle = 'Shopping Cart';
include __DIR__ . '/includes/header.php';
?>

<div class="breadcrumb container">
    <a href="index.php">Home</a><span class="sep">/</span>
    <span>Shopping Cart</span>
</div>

<section class="section">
    <div class="container">
        <h1 class="mb-4">Shopping Cart</h1>

        <div id="cart-empty" class="empty-state" style="display:none">
            <div class="icon">🛒</div>
            <h2>Your cart is empty</h2>
            <p>Looks like you haven't added anything yet. Let's fix that!</p>
            <a href="collections.php" class="btn btn-primary mt-3">Start Shopping</a>
        </div>

        <div id="cart-content" style="display:none">
            <div class="grid cart-layout" style="gap:2rem;align-items:start">
                <div>
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="hide-mobile">Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="cart-body"></tbody>
                    </table>
                    <div class="mt-3">
                        <a href="collections.php" class="btn btn-outline">← Continue Shopping</a>
                        <button id="clear-cart" class="btn btn-ghost">Clear Cart</button>
                    </div>
                </div>

                <div class="order-summary" style="position:sticky;top:100px">
                    <h3>Order Summary</h3>
                    <div class="summary-row"><span>Subtotal</span><span id="cart-subtotal">PKR 0</span></div>
                    <div class="summary-row"><span>Shipping</span><span id="cart-shipping">PKR 0</span></div>
                    <div class="summary-row total"><span>Total</span><span id="cart-total">PKR 0</span></div>
                    <a href="checkout.php" class="btn btn-primary btn-block btn-lg mt-2">Proceed to Checkout</a>
                    <p class="text-muted text-center mt-2" style="font-size:0.85rem">Secure checkout · WhatsApp support available</p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
var CART_KEY = 'tooba_cart';
function getCart() {
    try { return JSON.parse(localStorage.getItem(CART_KEY) || '{}'); }
    catch(e) { return {}; }
}
function saveCart(cart) { localStorage.setItem(CART_KEY, JSON.stringify(cart)); }
function fmt(n) { return 'PKR ' + Math.round(n).toLocaleString(); }

function renderCart() {
    var cart = getCart();
    var items = Object.values(cart);
    var empty = items.length === 0;
    document.getElementById('cart-empty').style.display = empty ? '' : 'none';
    document.getElementById('cart-content').style.display = empty ? 'none' : '';

    if (empty) return;

    var tbody = document.getElementById('cart-body');
    tbody.innerHTML = '';
    var subtotal = 0;

    items.forEach(function(item) {
        var lineTotal = item.price * item.quantity;
        subtotal += lineTotal;
        var tr = document.createElement('tr');
        tr.innerHTML =
            '<td><div class="cart-product">' +
                '<img src="' + (item.image || '') + '" alt="' + (item.name || '') + '" style="width:60px;height:60px;object-fit:cover;border-radius:8px">' +
                '<a href="collections.php">' + (item.name || '') + '</a>' +
            '</div></td>' +
            '<td class="hide-mobile">' + fmt(item.price) + '</td>' +
            '<td><div class="qty-input">' +
                '<button class="qty-dec" data-id="' + item.id + '">−</button>' +
                '<input type="text" value="' + item.quantity + '" readonly>' +
                '<button class="qty-inc" data-id="' + item.id + '">+</button>' +
            '</div></td>' +
            '<td><strong>' + fmt(lineTotal) + '</strong></td>' +
            '<td><button class="btn btn-ghost btn-sm remove-btn" data-id="' + item.id + '" style="color:var(--error-500)">✕</button></td>';
        tbody.appendChild(tr);
    });

    var shipping = subtotal >= 5000 ? 0 : 320;
    document.getElementById('cart-subtotal').textContent = fmt(subtotal);
    document.getElementById('cart-shipping').textContent = shipping === 0 ? 'Free' : fmt(shipping);
    document.getElementById('cart-total').textContent = fmt(subtotal + shipping);

    tbody.querySelectorAll('.qty-inc').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = btn.dataset.id;
            var c = getCart();
            if (c[id]) { c[id].quantity++; saveCart(c); renderCart(); }
        });
    });
    tbody.querySelectorAll('.qty-dec').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = btn.dataset.id;
            var c = getCart();
            if (c[id]) {
                c[id].quantity--;
                if (c[id].quantity <= 0) delete c[id];
                saveCart(c); renderCart();
            }
        });
    });
    tbody.querySelectorAll('.remove-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = btn.dataset.id;
            var c = getCart();
            delete c[id];
            saveCart(c); renderCart();
        });
    });
}

document.getElementById('clear-cart').addEventListener('click', function() {
    localStorage.removeItem(CART_KEY);
    renderCart();
});

renderCart();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
