<?php
require_once __DIR__ . '/../config/functions.php';
require_admin();

$action = $_GET['action'] ?? 'list';
$editId = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf'] ?? '')) {
        flash('error', 'Invalid request.');
        redirect('products.php');
    }
    $name = trim($_POST['name'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $price = (float)($_POST['price'] ?? 0);
    $salePrice = !empty($_POST['sale_price']) ? (float)$_POST['sale_price'] : null;
    $stock = (int)($_POST['stock'] ?? 0);
    $image = handle_image_upload('image_file', trim($_POST['image_url'] ?? ''));
    $description = trim($_POST['description'] ?? '');
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    $slug = slugify($name);

    // Ensure unique slug
    $suffix = '';
    while (true) {
        $check = db()->prepare("SELECT id FROM products WHERE slug = ?" . ($editId ? " AND id != ?" : ""));
        $check->execute($editId ? [$slug . $suffix, $editId] : [$slug . $suffix]);
        if (!$check->fetch()) break;
        $suffix = $suffix === '' ? '-2' : (intval(trim($suffix, '-')) + 1);
        $suffix = '-' . $suffix;
    }
    $slug = $slug . $suffix;

    if ($action === 'create' || $action === 'new') {
        db()->prepare("INSERT INTO products (category_id, name, slug, description, price, sale_price, stock, image, is_featured, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")
            ->execute([$categoryId, $name, $slug, $description, $price, $salePrice, $stock, $image, $isFeatured, $isActive]);
        flash('success', 'Product created successfully.');
    } else {
        db()->prepare("UPDATE products SET category_id=?, name=?, slug=?, description=?, price=?, sale_price=?, stock=?, image=?, is_featured=?, is_active=? WHERE id=?")
            ->execute([$categoryId, $name, $slug, $description, $price, $salePrice, $stock, $image, $isFeatured, $isActive, $editId]);
        flash('success', 'Product updated successfully.');
    }
    redirect('products.php');
}

if ($action === 'delete' && $editId) {
    db()->prepare("DELETE FROM products WHERE id = ?")->execute([$editId]);
    flash('success', 'Product deleted.');
    redirect('products.php');
}

$categories = get_categories();
$search = trim($_GET['search'] ?? '');
if ($search) {
    $stmt = db()->prepare("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.name LIKE ? ORDER BY p.created_at DESC");
    $stmt->execute(["%$search%"]);
} else {
    $stmt = db()->query("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC");
}
$products = $stmt->fetchAll();

$editProduct = null;
if (($action === 'edit' || $action === 'new') && $editId) {
    $stmt = db()->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$editId]);
    $editProduct = $stmt->fetch();
}

$pageTitle = 'Products';
$activePage = 'products';
include __DIR__ . '/includes/header.php';
?>

<div class="admin-header">
    <h1><?= $action === 'new' ? 'Add Product' : ($action === 'edit' ? 'Edit Product' : 'Products') ?></h1>
    <?php if ($action === 'list'): ?>
        <a href="products.php?action=new" class="btn btn-primary">+ Add New Product</a>
    <?php else: ?>
        <a href="products.php" class="btn btn-outline">← Back to Products</a>
    <?php endif; ?>
</div>

<?php if ($success = flash('success')): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<?php if ($error = flash('error')): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

<?php if ($action === 'new' || ($action === 'edit' && $editProduct)): ?>
    <form method="post" action="products.php?action=<?= $action === 'edit' ? 'edit&id='.$editId : 'create' ?>" class="form-card" style="max-width:none;box-shadow:var(--shadow-sm);background:var(--neutral-0)" enctype="multipart/form-data">
        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
        <div class="grid grid-2">
            <div class="form-group">
                <label>Product Name *</label>
                <input type="text" name="name" class="form-control" required value="<?= e($editProduct['name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Category *</label>
                <select name="category_id" class="form-control" required>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= ($editProduct['category_id'] ?? 0) == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="3"><?= e($editProduct['description'] ?? '') ?></textarea>
        </div>
        <div class="grid grid-3">
            <div class="form-group">
                <label>Price (PKR) *</label>
                <input type="number" name="price" class="form-control" step="0.01" required value="<?= e($editProduct['price'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Sale Price (optional)</label>
                <input type="number" name="sale_price" class="form-control" step="0.01" value="<?= e($editProduct['sale_price'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Stock</label>
                <input type="number" name="stock" class="form-control" value="<?= e($editProduct['stock'] ?? 0) ?>">
            </div>
        </div>
        <div class="form-group">
            <label>Product Image</label>
            <div class="image-upload-area" id="productImgPreview" style="<?= !empty($editProduct['image']) ? '' : 'display:none' ?>">
                <img src="<?= e($editProduct['image'] ?? '') ?>" alt="Preview" id="productImgPreviewImg">
            </div>
            <div class="image-upload-buttons">
                <label class="btn btn-outline btn-sm file-upload-label">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    Upload from Device
                    <input type="file" name="image_file" accept="image/jpeg,image/png,image/webp,image/gif" onchange="previewImage(this)" style="display:none">
                </label>
            </div>
            <input type="text" name="image_url" class="form-control" placeholder="Or paste an image URL (optional)" value="<?= e($editProduct['image'] ?? '') ?>" style="margin-top:0.5rem">
            <small class="text-muted">Upload a photo from your device or paste a URL. Max 5MB (JPG, PNG, WEBP, GIF).</small>
        </div>
        <div class="flex gap-2" style="margin-bottom:1rem">
            <label><input type="checkbox" name="is_featured" <?= !empty($editProduct['is_featured']) ? 'checked' : '' ?>> Featured Product</label>
            <label><input type="checkbox" name="is_active" <?= ($editProduct['is_active'] ?? 1) ? 'checked' : '' ?>> Active (visible in store)</label>
        </div>
        <button type="submit" class="btn btn-primary btn-lg"><?= $action === 'edit' ? 'Update Product' : 'Create Product' ?></button>
    </form>
<?php else: ?>
    <form method="get" class="mb-3 flex gap-2">
        <input type="text" name="search" class="form-control" placeholder="Search products..." value="<?= e($search) ?>">
        <button type="submit" class="btn btn-outline">Search</button>
        <?php if ($search): ?><a href="products.php" class="btn btn-ghost">Clear</a><?php endif; ?>
    </form>

    <table class="admin-table">
        <thead>
            <tr><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td><img src="<?= e($p['image'] ?: placeholder_image($p['name'], 40, 40)) ?>" alt="" style="width:40px;height:40px;border-radius:6px;object-fit:cover"></td>
                    <td><?= e($p['name']) ?></td>
                    <td><?= e($p['category_name'] ?? '—') ?></td>
                    <td><?= money(current_price($p)) ?></td>
                    <td><?= $p['stock'] ?></td>
                    <td>
                        <?php if ($p['is_active']): ?><span class="badge badge-new">Active</span><?php else: ?><span class="badge badge-stock">Hidden</span><?php endif; ?>
                        <?php if ($p['is_featured']): ?><span class="badge badge-sale">Featured</span><?php endif; ?>
                    </td>
                    <td>
                        <a href="products.php?action=edit&id=<?= $p['id'] ?>" class="btn btn-ghost btn-sm">Edit</a>
                        <a href="products.php?action=delete&id=<?= $p['id'] ?>" class="btn btn-ghost btn-sm" style="color:var(--error-500)" onclick="return confirm('Delete this product?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($products)): ?>
                <tr><td colspan="7" class="text-muted text-center" style="padding:2rem">No products found. <a href="products.php?action=new">Add your first product</a></td></tr>
            <?php endif; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
