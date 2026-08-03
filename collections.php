<?php
require_once __DIR__ . '/config/functions.php';

$categories = get_categories();
$pageTitle = 'All Collections';
$activePage = 'collections';
include __DIR__ . '/includes/header.php';
?>

<div class="breadcrumb container">
    <a href="index.php">Home</a><span class="sep">/</span>
    <span>Collections</span>
</div>

<section class="section">
    <div class="container">
        <div class="section-head">
            <h2>All Collections</h2>
            <p>Browse every category of beads, charms, and craft supplies</p>
            <div class="line"></div>
        </div>
        <div class="grid grid-3">
            <?php foreach ($categories as $cat):
                $count = db()->query('SELECT COUNT(*) FROM products WHERE category_id='.(int)$cat['id'].' AND is_active=1')->fetchColumn();
            ?>
                <a href="category.php?slug=<?= e($cat['slug']) ?>" class="cat-card">
                    <div class="img-wrap">
                        <img src="<?= e($cat['image'] ?: placeholder_image($cat['name'])) ?>" alt="<?= e($cat['name']) ?>">
                    </div>
                    <div class="cat-card-body">
                        <h3><?= e($cat['name']) ?></h3>
                        <span class="count"><?= $count ?> products</span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
