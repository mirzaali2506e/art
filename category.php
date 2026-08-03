<?php
require_once __DIR__ . '/config/functions.php';

$slug = $_GET['slug'] ?? '';
$category = get_category($slug);

if (!$category) {
    http_response_code(404);
    $pageTitle = 'Category Not Found';
    include __DIR__ . '/includes/header.php';
    echo '<div class="container"><div class="empty-state"><div class="icon">🔍</div><h2>Category Not Found</h2><p>The category you\'re looking for doesn\'t exist.</p><a href="collections.php" class="btn btn-primary mt-3">Browse Collections</a></div></div>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

$products = get_products_by_category($category['id']);
$pageTitle = $category['name'];
include __DIR__ . '/includes/header.php';
?>

<div class="breadcrumb container">
    <a href="index.php">Home</a><span class="sep">/</span>
    <a href="collections.php">Collections</a><span class="sep">/</span>
    <span><?= e($category['name']) ?></span>
</div>

<section class="section">
    <div class="container">
        <div class="section-head" style="text-align:left;margin-bottom:2rem">
            <h2><?= e($category['name']) ?></h2>
            <?php if ($category['description']): ?>
                <p><?= e($category['description']) ?></p>
            <?php endif; ?>
            <p class="text-muted mt-1"><?= count($products) ?> product(s) found</p>
        </div>

        <?php if (empty($products)): ?>
            <div class="empty-state">
                <div class="icon">📦</div>
                <h3>No products in this collection yet</h3>
                <p>Check back soon — we're constantly adding new items!</p>
                <a href="collections.php" class="btn btn-primary mt-3">Browse Other Collections</a>
            </div>
        <?php else: ?>
            <div class="grid grid-4">
                <?php foreach ($products as $p): ?>
                    <?php include __DIR__ . '/includes/product-card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
