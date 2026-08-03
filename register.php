<?php
require_once __DIR__ . '/config/functions.php';

if (is_logged_in()) redirect('account.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf'] ?? '')) {
        flash('error', 'Invalid request.');
        redirect('register.php');
    }
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    $errors = [];
    if (empty($name)) $errors[] = 'Name is required.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';

    $check = db()->prepare('SELECT id FROM customers WHERE email = ?');
    $check->execute([$email]);
    if ($check->fetch()) $errors[] = 'An account with this email already exists.';

    if (!empty($errors)) {
        flash('error', implode(' ', $errors));
    } else {
        $stmt = db()->prepare('INSERT INTO customers (name, email, phone, password_hash) VALUES (?, ?, ?, ?)');
        $stmt->execute([$name, $email, $phone, password_hash($password, PASSWORD_DEFAULT)]);
        $_SESSION['customer_id'] = (int)db()->lastInsertId();
        flash('success', 'Welcome to Tooba Art Collection! Your account has been created.');
        redirect('account.php');
    }
}

$pageTitle = 'Create Account';
include __DIR__ . '/includes/header.php';
?>

<div class="section" style="padding-top:3rem;padding-bottom:3rem">
    <div class="form-card">
        <h1 class="text-center mb-3">Create Account</h1>
        <p class="text-muted text-center mb-4">Join Tooba Art Collection to track orders and shop faster.</p>

        <?php if ($error = flash('error')): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" class="form-control" required value="<?= e($_POST['name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" required value="<?= e($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="tel" name="phone" class="form-control" value="<?= e($_POST['phone'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-lg">Create Account</button>
        </form>
        <p class="text-center mt-3 text-muted">Already have an account? <a href="login.php">Sign in</a></p>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
