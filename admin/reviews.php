<?php
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf'] ?? '')) {
        flash('error', 'Invalid request.');
        redirect('reviews.php');
    }
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['review_id'] ?? 0);

    if ($action === 'approve') {
        db()->prepare("UPDATE reviews SET is_approved = 1 WHERE id = ?")->execute([$id]);
        flash('success', 'Review approved.');
    } elseif ($action === 'unapprove') {
        db()->prepare("UPDATE reviews SET is_approved = 0 WHERE id = ?")->execute([$id]);
        flash('success', 'Review hidden.');
    } elseif ($action === 'delete') {
        db()->prepare("DELETE FROM reviews WHERE id = ?")->execute([$id]);
        flash('success', 'Review deleted.');
    }
    redirect('reviews.php');
}

$reviews = get_all_reviews();

$pageTitle = 'Reviews';
$activePage = 'reviews';
include __DIR__ . '/includes/header.php';
?>

<div class="admin-header">
    <h1>Reviews</h1>
</div>

<?php if ($success = flash('success')): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>

<table class="admin-table">
    <thead>
        <tr><th>Product</th><th>Reviewer</th><th>Rating</th><th>Comment</th><th>Status</th><th>Actions</th></tr>
    </thead>
    <tbody>
        <?php foreach ($reviews as $r): ?>
            <tr>
                <td><?= e($r['product_name']) ?></td>
                <td><?= e($r['name']) ?></td>
                <td><?= str_repeat('★', $r['rating']) ?></td>
                <td style="max-width:300px"><?= e(mb_strimwidth($r['comment'], 0, 100, '...')) ?></td>
                <td><?= $r['is_approved'] ? '<span class="badge badge-new">Approved</span>' : '<span class="badge badge-stock">Pending</span>' ?></td>
                <td>
                    <form method="post" style="display:inline">
                        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                        <input type="hidden" name="review_id" value="<?= $r['id'] ?>">
                        <?php if ($r['is_approved']): ?>
                            <button type="submit" name="action" value="unapprove" class="btn btn-ghost btn-sm">Hide</button>
                        <?php else: ?>
                            <button type="submit" name="action" value="approve" class="btn btn-primary btn-sm">Approve</button>
                        <?php endif; ?>
                        <button type="submit" name="action" value="delete" class="btn btn-ghost btn-sm" style="color:var(--error-500)" onclick="return confirm('Delete this review?')">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($reviews)): ?>
            <tr><td colspan="6" class="text-muted text-center" style="padding:2rem">No reviews yet.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include __DIR__ . '/includes/footer.php'; ?>
