<?php
require_once __DIR__ . '/../config/functions.php';
require_admin();

$action = $_GET['action'] ?? 'list';
$editId = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf'] ?? '')) {
        flash('error', 'Invalid request.');
        redirect('categories.php');
    }
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $image = handle_image_upload('image_file', trim($_POST['image_url'] ?? ''));
    $parentId = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
    $sortOrder = (int)($_POST['sort_order'] ?? 0);
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    $slug = slugify($name);

    // Ensure unique slug
    $suffix = '';
    while (true) {
        $check = db()->prepare("SELECT id FROM categories WHERE slug = ?" . ($editId ? " AND id != ?" : ""));
        $check->execute($editId ? [$slug . $suffix, $editId] : [$slug . $suffix]);
        if (!$check->fetch()) break;
        $suffix = $suffix === '' ? '-2' : '-' . (intval(trim($suffix, '-')) + 1);
    }
    $slug = $slug . $suffix;

    if ($action === 'create' || $action === 'new') {
        db()->prepare("INSERT INTO categories (parent_id, name, slug, description, image, sort_order, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?)")
            ->execute([$parentId, $name, $slug, $description, $image, $sortOrder, $isFeatured]);
        flash('success', 'Category created.');
    } else {
        db()->prepare("UPDATE categories SET parent_id=?, name=?, slug=?, description=?, image=?, sort_order=?, is_featured=? WHERE id=?")
            ->execute([$parentId, $name, $slug, $description, $image, $sortOrder, $isFeatured, $editId]);
        flash('success', 'Category updated.');
    }
    redirect('categories.php');
}

if ($action === 'delete' && $editId) {
    $count = db()->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
    $count->execute([$editId]);
    if ($count->fetchColumn() > 0) {
        flash('error', 'Cannot delete: products exist in this category. Move them first.');
    } else {
        db()->prepare("DELETE FROM categories WHERE id = ?")->execute([$editId]);
        flash('success', 'Category deleted.');
    }
    redirect('categories.php');
}

$categories = db()->query("SELECT * FROM categories ORDER BY sort_order, name")->fetchAll();
$editCat = null;
if (($action === 'edit' || $action === 'new') && $editId) {
    $stmt = db()->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$editId]);
    $editCat = $stmt->fetch();
}

$pageTitle = 'Categories';
$activePage = 'categories';
include __DIR__ . '/includes/header.php';
?>

<div class="admin-header">
    <h1><?= $action === 'new' ? 'Add Category' : ($action === 'edit' ? 'Edit Category' : 'Categories') ?></h1>
    <?php if ($action === 'list'): ?>
        <a href="categories.php?action=new" class="btn btn-primary">+ Add New Category</a>
    <?php else: ?>
        <a href="categories.php" class="btn btn-outline">← Back to Categories</a>
    <?php endif; ?>
</div>

<?php if ($success = flash('success')): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<?php if ($error = flash('error')): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

<?php if ($action === 'new' || ($action === 'edit' && $editCat)): ?>
    <form method="post" action="categories.php?action=<?= $action === 'edit' ? 'edit&id='.$editId : 'create' ?>" class="form-card" style="max-width:none;box-shadow:var(--shadow-sm);background:var(--neutral-0)" enctype="multipart/form-data">
        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
        <div class="grid grid-2">
            <div class="form-group">
                <label>Category Name *</label>
                <input type="text" name="name" class="form-control" required value="<?= e($editCat['name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Parent Category (optional)</label>
                <select name="parent_id" class="form-control">
                    <option value="">None (top-level)</option>
                    <?php foreach ($categories as $c): ?>
                        <?php if ($editId && $c['id'] == $editId) continue; ?>
                        <option value="<?= $c['id'] ?>" <?= ($editCat['parent_id'] ?? null) == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="2"><?= e($editCat['description'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
            <label>Category Image</label>
            <div class="image-upload-area" id="catImgPreview" style="<?= !empty($editCat['image']) ? '' : 'display:none' ?>">
                <img src="<?= e($editCat['image'] ?? '') ?>" alt="Preview" id="catImgPreviewImg">
            </div>
            <div class="image-upload-buttons">
                <label class="btn btn-outline btn-sm file-upload-label">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    Upload from Device
                    <input type="file" name="image_file" accept="image/jpeg,image/png,image/webp,image/gif" onchange="previewImage(this)" style="display:none">
                </label>
            </div>
            <input type="text" name="image_url" class="form-control" placeholder="Or paste an image URL (optional)" value="<?= e($editCat['image'] ?? '') ?>" style="margin-top:0.5rem">
            <small class="text-muted">Upload from your device or paste a URL. Max 5MB.</small>
        </div>
        <div class="grid grid-2">
            <div class="form-group">
                <label>Sort Order</label>
                <input type="number" name="sort_order" class="form-control" value="<?= e($editCat['sort_order'] ?? 0) ?>">
            </div>
            <div class="form-group">
                <label>&nbsp;</label>
                <label><input type="checkbox" name="is_featured" <?= !empty($editCat['is_featured']) ? 'checked' : '' ?>> Show on homepage</label>
            </div>
        </div>
        <button type="submit" class="btn btn-primary btn-lg"><?= $action === 'edit' ? 'Update Category' : 'Create Category' ?></button>
    </form>
<?php else: ?>
    <table class="admin-table">
        <thead>
            <tr><th>Image</th><th>Name</th><th>Slug</th><th>Products</th><th>Featured</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $c):
                $count = db()->query("SELECT COUNT(*) FROM products WHERE category_id=".(int)$c['id'])->fetchColumn();
            ?>
                <tr>
                    <td><img src="<?= e($c['image'] ?: placeholder_image($c['name'], 40, 40)) ?>" alt="" style="width:40px;height:40px;border-radius:6px;object-fit:cover"></td>
                    <td><?= e($c['name']) ?></td>
                    <td class="text-muted"><?= e($c['slug']) ?></td>
                    <td><?= $count ?></td>
                    <td><?= $c['is_featured'] ? '✓' : '—' ?></td>
                    <td>
                        <a href="categories.php?action=edit&id=<?= $c['id'] ?>" class="btn btn-ghost btn-sm">Edit</a>
                        <a href="categories.php?action=delete&id=<?= $c['id'] ?>" class="btn btn-ghost btn-sm" style="color:var(--error-500)" onclick="return confirm('Delete this category?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($categories)): ?>
                <tr><td colspan="6" class="text-muted text-center" style="padding:2rem">No categories yet. <a href="categories.php?action=new">Add one</a></td></tr>
            <?php endif; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
