<?php
$fb = setting('facebook');
$ig = setting('instagram');
$wa  = setting('whatsapp');
$email = setting('email');
$addr  = setting('address');
$cats = get_categories();
?>
</main>

<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div>
                <h4>Tooba Art Collection</h4>
                <p style="color:var(--primary-300);font-size:0.9rem;line-height:1.7;margin-bottom:1rem;max-width:320px">
                    Your one-stop shop for premium beads, charms, bracelet kits, and craft supplies. Quality you can trust, delivered to your door.
                </p>
                <div class="social-links">
                    <a href="<?= e($fb) ?>" aria-label="Facebook"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg></a>
                    <a href="<?= e($ig) ?>" aria-label="Instagram"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg></a>
                    <a href="https://wa.me/<?= e(preg_replace('/\D/', '', $wa)) ?>" aria-label="WhatsApp"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.6 6.32A7.85 7.85 0 0 0 12.05 4a7.94 7.94 0 0 0-6.88 11.9L4 20l4.2-1.1a7.9 7.9 0 0 0 3.84.98h.01a7.94 7.94 0 0 0 5.55-13.56zM12.05 18.5h-.01a6.6 6.6 0 0 1-3.36-.92l-.24-.14-2.5.65.67-2.43-.16-.25a6.6 6.6 0 1 1 5.6 3.09zm3.62-4.94c-.2-.1-1.18-.58-1.36-.65-.18-.06-.32-.1-.45.1-.13.2-.5.65-.62.78-.11.13-.23.15-.43.05-.2-.1-.84-.31-1.6-.99-.59-.53-.99-1.18-1.1-1.38-.12-.2-.01-.31.09-.41.09-.09.2-.23.3-.35.1-.12.13-.2.2-.33.06-.13.03-.25-.02-.35-.05-.1-.45-1.08-.62-1.48-.16-.39-.33-.34-.45-.34l-.39-.01c-.13 0-.35.05-.53.25-.18.2-.7.69-.7 1.67 0 .99.72 1.94.82 2.07.1.13 1.42 2.17 3.44 3.04.48.21.86.33 1.15.42.48.16.93.13 1.28.08.39-.06 1.18-.48 1.35-.95.17-.47.17-.87.12-.95-.05-.08-.18-.13-.38-.23z"/></svg></a>
                </div>
            </div>
            <div>
                <h4>Shop</h4>
                <?php foreach (array_slice($cats, 0, 6) as $cat): ?>
                    <a href="category.php?slug=<?= e($cat['slug']) ?>"><?= e($cat['name']) ?></a>
                <?php endforeach; ?>
            </div>
            <div>
                <h4>Company</h4>
                <a href="about.php">About Us</a>
                <a href="contact.php">Contact</a>
                <a href="collections.php">All Collections</a>
                <a href="cart.php">Shopping Cart</a>
            </div>
            <div>
                <h4>Contact</h4>
                <a href="tel:<?= e($wa) ?>">WhatsApp: <?= e($wa) ?></a>
                <a href="mailto:<?= e($email) ?>"><?= e($email) ?></a>
                <span style="color:var(--primary-300);font-size:0.9rem;display:block;padding:0.25rem 0"><?= e($addr) ?></span>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; <?= date('Y') ?> <?= e(SITE_NAME) ?>. All rights reserved. · <a href="admin/login.php" style="color:var(--primary-400);font-size:0.85rem">Admin Panel</a>
        </div>
    </div>
</footer>

<script>
document.querySelectorAll('.faq-q').forEach(q => {
    q.addEventListener('click', () => q.parentElement.classList.toggle('open'));
});

// Close mobile nav when a link is clicked
document.querySelectorAll('#mainNav a').forEach(a => {
    a.addEventListener('click', () => {
        document.getElementById('mainNav').classList.remove('open');
    });
});

// Client-side cart using localStorage (no server needed)
var CART_KEY = 'tooba_cart';
function getCart() {
    try { return JSON.parse(localStorage.getItem(CART_KEY) || '{}'); }
    catch(e) { return {}; }
}
function saveCart(cart) {
    localStorage.setItem(CART_KEY, JSON.stringify(cart));
}
function cartCount() {
    var cart = getCart(), n = 0;
    for (var k in cart) n += cart[k].quantity;
    return n;
}
function updateCartBadge() {
    var count = cartCount();
    var badges = document.querySelectorAll('.cart-badge, .cart-count');
    if (count > 0 && badges.length === 0) {
        var cartLink = document.querySelector('a[href="cart.php"]');
        if (cartLink) {
            var badge = document.createElement('span');
            badge.className = 'cart-count';
            badge.textContent = count;
            cartLink.appendChild(badge);
        }
    } else {
        badges.forEach(function(b) {
            b.textContent = count;
            b.style.display = count > 0 ? '' : 'none';
        });
    }
}
function showToast(msg) {
    var toast = document.createElement('div');
    toast.className = 'toast';
    toast.textContent = msg;
    document.body.appendChild(toast);
    requestAnimationFrame(function() { toast.classList.add('show'); });
    setTimeout(function() {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s';
        setTimeout(function() { toast.remove(); }, 300);
    }, 2500);
}

// Add-to-cart forms
document.querySelectorAll('form[data-ajax-cart]').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        var btn = form.querySelector('[type="submit"]');
        var originalText = btn ? btn.innerHTML : '';
        if (btn) { btn.disabled = true; btn.innerHTML = 'Adding...'; }

        var productId = form.querySelector('input[name="product_id"]').value;
        var qtyInput = form.querySelector('input[name="quantity"]');
        var quantity = qtyInput ? Math.max(1, parseInt(qtyInput.value) || 1) : 1;

        // Extract product info from the page
        var productCard = form.closest('.product-card');
        var productForm = form.closest('.product-buy-form');
        var name = '', price = 0, image = '';

        if (productForm) {
            // Detail page
            var h1 = document.querySelector('h1');
            name = h1 ? h1.textContent.trim() : 'Product';
            var priceEl = document.querySelector('.product-price .price, .price');
            if (priceEl) price = parseFloat(priceEl.textContent.replace(/[^0-9.]/g, '')) || 0;
            var imgEl = document.querySelector('.product-image img, .product-detail img');
            if (imgEl) image = imgEl.src;
        } else if (productCard) {
            // Card on listing page
            var h3 = productCard.querySelector('h3');
            name = h3 ? h3.textContent.trim() : 'Product';
            var priceSpan = productCard.querySelector('.price');
            if (priceSpan) price = parseFloat(priceSpan.textContent.replace(/[^0-9.]/g, '')) || 0;
            var img = productCard.querySelector('img');
            if (img) image = img.src;
        }

        var cart = getCart();
        if (cart[productId]) {
            cart[productId].quantity += quantity;
        } else {
            cart[productId] = { id: productId, name: name, price: price, image: image, quantity: quantity };
        }
        saveCart(cart);
        updateCartBadge();
        showToast('Product added to cart!');

        if (btn) { btn.disabled = false; btn.innerHTML = originalText; }
    });
});

// Update badge on page load
updateCartBadge();

// Toast notifications for flash messages
(function() {
    var alerts = document.querySelectorAll('.alert-success, .alert-error');
    alerts.forEach(function(alert) {
        var toast = document.createElement('div');
        toast.className = 'toast';
        if (alert.classList.contains('alert-error')) toast.style.background = 'linear-gradient(135deg, #b83a3a, #8f3d27)';
        toast.textContent = alert.textContent.trim();
        document.body.appendChild(toast);
        alert.style.display = 'none';
        setTimeout(function() {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.3s';
            setTimeout(function() { toast.remove(); }, 300);
        }, 3000);
    });
})();
</script>
</body>
</html>
