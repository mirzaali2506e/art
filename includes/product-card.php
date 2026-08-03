<?php
$rating = get_avg_rating($p['id']);
$cat = db()->prepare('SELECT name, slug FROM categories WHERE id = ?');
$cat->execute([$p['category_id']]);
$catInfo = $cat->fetch();
?>
<div class="product-card">
    <div class="img-wrap">
        <div class="badges">
            <?php if (is_on_sale($p)): ?><span class="badge badge-sale">-<?= discount_percent($p) ?>%</span><?php endif; ?>
            <?php if ($p['is_featured']): ?><span class="badge badge-new">Featured</span><?php endif; ?>
            <?php if ($p['stock'] <= 0): ?><span class="badge badge-stock">Out of Stock</span><?php endif; ?>
        </div>
        <img src="<?= e($p['image'] ?: placeholder_image($p['name'])) ?>" alt="<?= e($p['name']) ?>">
        <div class="quick-add">
            <form method="post" action="cart-action.php">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                <button type="submit" class="btn btn-primary btn-sm btn-block" <?= $p['stock'] <= 0 ? 'disabled' : '' ?>>Add to Cart</button>
            </form>
        </div>
    </div>
    <div class="product-card-body">
        <?php if ($catInfo): ?><span class="cat"><?= e($catInfo['name']) ?></span><?php endif; ?>
        <h3><a href="product.php?slug=<?= e($p['slug']) ?>" style="color:var(--neutral-900)"><?= e($p['name']) ?></a></h3>
        <div class="price-row">
            <span class="price"><?= money(current_price($p)) ?></span>
            <?php if (is_on_sale($p)): ?><span class="old-price"><?= money($p['price']) ?></span><?php endif; ?>
        </div>
        <?php if ($rating['count'] > 0): ?>
            <div class="stars"><?= str_repeat('★', (int)round($rating['avg'])) . str_repeat('☆', 5 - (int)round($rating['avg'])) ?> (<?= $rating['count'] ?>)</div>
        <?php endif; ?>
    </div>
</div>
