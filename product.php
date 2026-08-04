<?php
require_once __DIR__ . '/config/functions.php';

$slug = $_GET['slug'] ?? '';
$product = get_product($slug);

if (!$product) {
    http_response_code(404);
    $pageTitle = 'Product Not Found';
    include __DIR__ . '/includes/header.php';
    echo '<div class="container"><div class="empty-state"><div class="icon">🔍</div><h2>Product Not Found</h2><p>The product you\'re looking for doesn\'t exist.</p><a href="collections.php" class="btn btn-primary mt-3">Browse Products</a></div></div>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

$cat = db()->prepare('SELECT name, slug FROM categories WHERE id = ?');
$cat->execute([$product['category_id']]);
$catInfo = $cat->fetch();

$reviews = get_product_reviews($product['id']);
$rating = get_avg_rating($product['id']);

$related = db()->prepare('SELECT * FROM products WHERE category_id = ? AND id != ? AND is_active = 1 ORDER BY RAND() LIMIT 4');
$related->execute([$product['category_id'], $product['id']]);
$relatedProducts = $related->fetchAll();

$pageTitle = $product['name'];
include __DIR__ . '/includes/header.php';
?>

<div class="breadcrumb container">
    <a href="index.php">Home</a><span class="sep">/</span>
    <?php if ($catInfo): ?><a href="category.php?slug=<?= e($catInfo['slug']) ?>"><?= e($catInfo['name']) ?></a><span class="sep">/</span><?php endif; ?>
    <span><?= e($product['name']) ?></span>
</div>

<section class="section">
    <div class="container">
        <div class="product-detail-grid">
            <div class="product-detail-img">
                <img src="<?= e($product['image'] ?: placeholder_image($product['name'])) ?>" alt="<?= e($product['name']) ?>">
            </div>
            <div class="product-detail-info">
                <?php if ($catInfo): ?><span class="cat" style="font-size:0.8rem;color:var(--neutral-400);text-transform:uppercase;letter-spacing:0.05em"><?= e($catInfo['name']) ?></span><?php endif; ?>
                <h1><?= e($product['name']) ?></h1>

                <?php if ($rating['count'] > 0): ?>
                    <div class="stars-display"><?= str_repeat('★', (int)round($rating['avg'])) . str_repeat('☆', 5 - (int)round($rating['avg'])) ?> <?= $rating['avg'] ?> (<?= $rating['count'] ?> reviews)</div>
                <?php endif; ?>

                <div class="price-row">
                    <span class="price"><?= money(current_price($product)) ?></span>
                    <?php if (is_on_sale($product)): ?>
                        <span class="old-price"><?= money($product['price']) ?></span>
                        <span class="badge badge-sale">Save <?= discount_percent($product) ?>%</span>
                    <?php endif; ?>
                </div>

                <div class="description">
                    <p><?= nl2br(e($product['description'] ?: 'Premium quality craft supply, perfect for your creative projects.')) ?></p>
                </div>

                <p class="mb-2">
                    <?php if ($product['stock'] > 0): ?>
                        <span class="text-success">✓ In Stock (<?= $product['stock'] ?> available)</span>
                    <?php else: ?>
                        <span class="text-error">✗ Out of Stock</span>
                    <?php endif; ?>
                </p>

                <?php if ($product['stock'] > 0): ?>
                    <form method="post" action="cart-action.php" class="product-buy-form" data-ajax-cart>
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                        <div class="product-qty-group">
                            <label>Quantity</label>
                            <input type="number" name="quantity" value="1" min="1" max="<?= (int)$product['stock'] ?>" class="form-control">
                        </div>
                        <div class="product-buy-buttons">
                            <button type="submit" class="btn btn-primary btn-lg">Add to Cart</button>
                            <a href="checkout.php?buy_now=<?= (int)$product['id'] ?>" class="btn btn-accent btn-lg">Buy Now</a>
                        </div>
                    </form>
                <?php endif; ?>

                <div class="product-help-bar">
                    <p class="text-muted">
                        <strong>Need help?</strong> WhatsApp us at <?= e(setting('whatsapp')) ?> for product questions or custom orders.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Reviews section -->
<section class="section" style="background:var(--neutral-100)">
    <div class="container">
        <div class="reviews-grid">
            <div>
                <h2 class="mb-3">Customer Reviews</h2>
                <?php if (empty($reviews)): ?>
                    <p class="text-muted">No reviews yet. Be the first to review this product!</p>
                <?php else: ?>
                    <?php foreach ($reviews as $r): ?>
                        <div class="review-card">
                            <div class="flex-between mb-1">
                                <span class="reviewer-name"><?= e($r['name']) ?></span>
                                <span class="review-date"><?= date('M j, Y', strtotime($r['created_at'] ?? 'now')) ?></span>
                            </div>
                            <div class="stars"><?= str_repeat('★', $r['rating']) . str_repeat('☆', 5 - $r['rating']) ?></div>
                            <p class="review-text"><?= e($r['comment']) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div>
                <h3 class="mb-3">Write a Review</h3>
                <form method="post" action="review-submit.php" class="form-card" style="box-shadow:none;background:var(--neutral-0)">
                    <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                    <input type="hidden" name="slug" value="<?= e($product['slug']) ?>">
                    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                    <div class="form-group">
                        <label>Your Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Your Rating</label>
                        <div class="star-rating">
                            <input type="radio" name="rating" value="5" id="star5" required><label for="star5">★</label>
                            <input type="radio" name="rating" value="4" id="star4"><label for="star4">★</label>
                            <input type="radio" name="rating" value="3" id="star3"><label for="star3">★</label>
                            <input type="radio" name="rating" value="2" id="star2"><label for="star2">★</label>
                            <input type="radio" name="rating" value="1" id="star1"><label for="star1">★</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Your Review</label>
                        <textarea name="comment" class="form-control" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Submit Review</button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Related products -->
<?php if (!empty($relatedProducts)): ?>
<section class="section">
    <div class="container">
        <div class="section-head">
            <h2>You May Also Like</h2>
            <div class="line"></div>
        </div>
        <div class="grid grid-4">
            <?php foreach ($relatedProducts as $p): ?>
                <?php include __DIR__ . '/includes/product-card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
