<?php
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf'] ?? '')) {
        flash('error', 'Invalid request.');
        redirect('settings.php');
    }
    $keys = ['site_name', 'whatsapp', 'email', 'address', 'shipping_fee', 'currency', 'instagram', 'facebook'];
    foreach ($keys as $key) {
        if (isset($_POST[$key])) {
            $stmt = db()->prepare("UPDATE settings SET sval = ? WHERE skey = ?");
            $stmt->execute([$_POST[$key], $key]);
        }
    }
    // Update password if provided
    if (!empty($_POST['new_password'])) {
        $hash = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        db()->prepare("UPDATE admin_users SET password_hash = ? WHERE id = ?")->execute([$hash, $_SESSION['admin_id']]);
        flash('success', 'Settings updated and password changed.');
    } else {
        flash('success', 'Settings updated successfully.');
    }
    redirect('settings.php');
}

$settings = [];
$rows = db()->query("SELECT skey, sval FROM settings")->fetchAll();
foreach ($rows as $row) $settings[$row['skey']] = $row['sval'];

$pageTitle = 'Settings';
$activePage = 'settings';
include __DIR__ . '/includes/header.php';
?>

<div class="admin-header">
    <h1>Store Settings</h1>
</div>

<?php if ($success = flash('success')): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>

<form method="post" class="form-card" style="max-width:none;box-shadow:var(--shadow-sm);background:var(--neutral-0)">
    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
    <h3 class="mb-3">Store Information</h3>
    <div class="grid grid-2">
        <div class="form-group">
            <label>Store Name</label>
            <input type="text" name="site_name" class="form-control" value="<?= e($settings['site_name'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Currency</label>
            <input type="text" name="currency" class="form-control" value="<?= e($settings['currency'] ?? 'PKR') ?>">
        </div>
        <div class="form-group">
            <label>WhatsApp Number</label>
            <input type="text" name="whatsapp" class="form-control" value="<?= e($settings['whatsapp'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="<?= e($settings['email'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Shipping Fee (per kg)</label>
            <input type="number" name="shipping_fee" class="form-control" value="<?= e($settings['shipping_fee'] ?? '320') ?>">
        </div>
        <div class="form-group">
            <label>Address</label>
            <input type="text" name="address" class="form-control" value="<?= e($settings['address'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Instagram URL</label>
            <input type="text" name="instagram" class="form-control" value="<?= e($settings['instagram'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Facebook URL</label>
            <input type="text" name="facebook" class="form-control" value="<?= e($settings['facebook'] ?? '') ?>">
        </div>
    </div>

    <h3 class="mt-4 mb-3">Change Admin Password</h3>
    <div class="form-group">
        <label>New Password (leave blank to keep current)</label>
        <input type="password" name="new_password" class="form-control" placeholder="Enter new password">
    </div>

    <button type="submit" class="btn btn-primary btn-lg">Save Settings</button>
</form>

<?php include __DIR__ . '/includes/footer.php'; ?>
