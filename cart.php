<?php
require_once __DIR__ . '/config/functions.php';

$cart = get_cart();
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

        <?php if ($success = flash('success')): ?>
            <div class="alert alert-success"><?= e($success) ?></div>
        <?php endif; ?>
        <?php if ($error = flash('error')): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php endif; ?>

        <?php if (empty($cart)): ?>
            <div class="empty-state">
                <div class="icon">🛒</div>
                <h2>Your cart is empty</h2>
                <p>Looks like you haven't added anything yet. Let's fix that!</p>
                <a href="collections.php" class="btn btn-primary mt-3">Start Shopping</a>
            </div>
        <?php else: ?>
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
                        <tbody>
                            <?php foreach ($cart as $item): ?>
                                <tr>
                                    <td>
                                        <div class="cart-product">
                                            <img src="<?= e($item['image'] ?: placeholder_image($item['name'], 100, 100)) ?>" alt="<?= e($item['name']) ?>">
                                            <a href="collections.php"><?= e($item['name']) ?></a>
                                        </div>
                                    </td>
                                    <td class="hide-mobile"><?= money($item['price']) ?></td>
                                    <td>
                                        <form method="post" action="cart-action.php" style="display:inline-flex">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="product_id" value="<?= (int)$item['id'] ?>">
                                            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                                            <div class="qty-input">
                                                <button type="submit" name="quantity" value="<?= $item['quantity'] - 1 ?>">−</button>
                                                <input type="text" value="<?= $item['quantity'] ?>" readonly>
                                                <button type="submit" name="quantity" value="<?= $item['quantity'] + 1 ?>">+</button>
                                            </div>
                                        </form>
                                    </td>
                                    <td><strong><?= money($item['price'] * $item['quantity']) ?></strong></td>
                                    <td>
                                        <form method="post" action="cart-action.php" style="display:inline">
                                            <input type="hidden" name="action" value="remove">
                                            <input type="hidden" name="product_id" value="<?= (int)$item['id'] ?>">
                                            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                                            <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--error-500)">✕</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="mt-3">
                        <a href="collections.php" class="btn btn-outline">← Continue Shopping</a>
                        <form method="post" action="cart-action.php" style="display:inline">
                            <input type="hidden" name="action" value="clear">
                            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                            <button type="submit" class="btn btn-ghost">Clear Cart</button>
                        </form>
                    </div>
                </div>

                <div class="order-summary" style="position:sticky;top:100px">
                    <h3>Order Summary</h3>
                    <div class="summary-row"><span>Subtotal</span><span><?= money(cart_subtotal()) ?></span></div>
                    <?php $shipping = cart_subtotal() >= 5000 ? 0 : (float)setting('shipping_fee', 320); ?>
                    <div class="summary-row"><span>Shipping</span><span><?= $shipping == 0 ? 'Free' : money($shipping) ?></span></div>
                    <div class="summary-row total"><span>Total</span><span><?= money(cart_subtotal() + $shipping) ?></span></div>
                    <a href="checkout.php" class="btn btn-primary btn-block btn-lg mt-2">Proceed to Checkout</a>
                    <p class="text-muted text-center mt-2" style="font-size:0.85rem">Secure checkout · WhatsApp support available</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
