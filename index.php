<?php
require_once __DIR__ . '/config/functions.php';

$featuredCats = get_featured_categories();
$featuredProducts = get_featured_products(8);
$latestProducts = get_latest_products(4);
$reviews = db()->query("SELECT r.*, p.name AS product_name FROM reviews r JOIN products p ON r.product_id = p.id WHERE r.is_approved = 1 ORDER BY r.created_at DESC LIMIT 3")->fetchAll();
$avgRating = db()->query("SELECT AVG(rating) AS avg, COUNT(*) AS cnt FROM reviews WHERE is_approved = 1")->fetch();

$pageTitle = 'Home';
$activePage = 'home';
include __DIR__ . '/includes/header.php';
?>

<!-- Hero -->
<section class="hero">
    <div class="container hero-grid">
        <div class="animate-in">
            <h1>Craft Your <span class="accent">Story</span> in Beads</h1>
            <p>Premium beads, charms, bracelet kits, and craft supplies — handpicked for makers who care about quality. Build beautiful bracelets that tell your story.</p>
            <div class="hero-actions">
                <a href="collections.php" class="btn btn-primary btn-lg">Shop Collections</a>
                <a href="about.php" class="btn btn-outline btn-lg">Our Story</a>
            </div>
            <div class="hero-stats">
                <div class="hero-stat">
                    <div class="num">500+</div>
                    <div class="label">Products</div>
                </div>
                <div class="hero-stat">
                    <div class="num"><?= (int)$avgRating['cnt'] ?>+</div>
                    <div class="label">Happy Customers</div>
                </div>
                <div class="hero-stat">
                    <div class="num"><?= number_format($avgRating['avg'], 1) ?>★</div>
                    <div class="label">Average Rating</div>
                </div>
            </div>
        </div>
        <div class="hero-image animate-in">
            <img src="<?= placeholder_image('Beautiful Beads Collection', 800, 600) ?>" alt="Tooba Art Collection">
        </div>
    </div>
</section>

<!-- Featured Collections -->
<?php if (!empty($featuredCats)): ?>
<section class="section">
    <div class="container">
        <div class="section-head">
            <h2>Shop by Collection</h2>
            <p>Explore our curated collections for every craft</p>
            <div class="line"></div>
        </div>
        <div class="grid grid-4">
            <?php foreach ($featuredCats as $cat): ?>
                <a href="category.php?slug=<?= e($cat['slug']) ?>" class="cat-card">
                    <img src="<?= e($cat['image'] ?: placeholder_image($cat['name'])) ?>" alt="<?= e($cat['name']) ?>">
                    <div class="cat-card-body">
                        <h3><?= e($cat['name']) ?></h3>
                        <span class="count"><?= db()->query('SELECT COUNT(*) FROM products WHERE category_id='.(int)$cat['id'].' AND is_active=1')->fetchColumn() ?> products</span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Featured Products -->
<?php if (!empty($featuredProducts)): ?>
<section class="section" style="background:var(--neutral-100)">
    <div class="container">
        <div class="section-head">
            <h2>Featured Products</h2>
            <p>Our most-loved beads and accessories</p>
            <div class="line"></div>
        </div>
        <div class="grid grid-4">
            <?php foreach ($featuredProducts as $p): ?>
                <?php include __DIR__ . '/includes/product-card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Latest Products -->
<?php if (!empty($latestProducts)): ?>
<section class="section">
    <div class="container">
        <div class="section-head">
            <h2>New Arrivals</h2>
            <p>Fresh stock, straight to your workshop</p>
            <div class="line"></div>
        </div>
        <div class="grid grid-4">
            <?php foreach ($latestProducts as $p): ?>
                <?php include __DIR__ . '/includes/product-card.php'; ?>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="collections.php" class="btn btn-outline btn-lg">View All Products</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Reviews -->
<?php if (!empty($reviews)): ?>
<section class="section" style="background:var(--primary-50)">
    <div class="container">
        <div class="section-head">
            <h2>Customers Are Saying</h2>
            <p>Rated <?= number_format($avgRating['avg'], 1) ?> ★ by <?= (int)$avgRating['cnt'] ?>+ verified customers</p>
            <div class="line"></div>
        </div>
        <div class="grid grid-3">
            <?php foreach ($reviews as $r): ?>
                <div class="review-card">
                    <div class="stars"><?= str_repeat('★', $r['rating']) . str_repeat('☆', 5 - $r['rating']) ?></div>
                    <div class="name"><?= e($r['name']) ?></div>
                    <p class="comment">"<?= e($r['comment']) ?>"</p>
                    <small class="text-muted">on <?= e($r['product_name']) ?></small>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- FAQ -->
<section class="section">
    <div class="container" style="max-width:760px">
        <div class="section-head">
            <h2>Frequently Asked Questions</h2>
            <p>Everything you need to know before ordering</p>
            <div class="line"></div>
        </div>
        <div class="faq-item open">
            <div class="faq-q">How do I place an order? <span class="arrow">▼</span></div>
            <div class="faq-a">
                Browse our collections, add the items you love to your cart, then click the cart icon in the top right. Review your items, click "Checkout," fill in your shipping details, and click "Complete Order." It's that simple!
            </div>
        </div>
        <div class="faq-item">
            <div class="faq-q">What is your WhatsApp number? <span class="arrow">▼</span></div>
            <div class="faq-a">
                You can reach us on WhatsApp at <strong><?= e(setting('whatsapp')) ?></strong>. We're happy to help with product questions, custom orders, and order tracking.
            </div>
        </div>
        <div class="faq-item">
            <div class="faq-q">What are the shipping charges? <span class="arrow">▼</span></div>
            <div class="faq-a">
                Shipping is charged at <strong><?= money(setting('shipping_fee', 320)) ?> per kg</strong>, with an additional charge for every extra kilogram. Orders over PKR 5,000 qualify for free shipping.
            </div>
        </div>
        <div class="faq-item">
            <div class="faq-q">Do you offer bulk or wholesale pricing? <span class="arrow">▼</span></div>
            <div class="faq-a">
                Yes! For bulk orders and wholesale inquiries, please contact us on WhatsApp and we'll provide you with a custom quote based on your requirements.
            </div>
        </div>
        <div class="faq-item">
            <div class="faq-q">How long does delivery take? <span class="arrow">▼</span></div>
            <div class="faq-a">
                Orders are processed within 1-2 business days. Delivery typically takes 3-5 business days depending on your location within Pakistan.
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="section" style="background:var(--primary-800);color:var(--neutral-0);text-align:center">
    <div class="container">
        <h2 style="color:var(--neutral-0)">Ready to Start Crafting?</h2>
        <p style="color:var(--primary-200);margin:1rem 0 2rem;max-width:500px;margin-left:auto;margin-right:auto">
            Join thousands of happy makers who trust Tooba Art Collection for their creative projects.
        </p>
        <a href="collections.php" class="btn btn-accent btn-lg">Browse All Products</a>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
