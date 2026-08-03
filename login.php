<?php
require_once __DIR__ . '/config/functions.php';

if (is_logged_in()) redirect('account.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf'] ?? '')) {
        flash('error', 'Invalid request.');
        redirect('login.php');
    }
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = db()->prepare('SELECT * FROM customers WHERE email = ?');
    $stmt->execute([$email]);
    $customer = $stmt->fetch();

    if ($customer && password_verify($password, $customer['password_hash'])) {
        $_SESSION['customer_id'] = $customer['id'];
        redirect('account.php');
    } else {
        flash('error', 'Invalid email or password.');
    }
}

$pageTitle = 'Sign In';
include __DIR__ . '/includes/header.php';
?>

<div class="section" style="padding-top:3rem;padding-bottom:3rem">
    <div class="form-card">
        <h1 class="text-center mb-3">Welcome Back</h1>
        <p class="text-muted text-center mb-4">Sign in to track your orders and shop faster.</p>

        <?php if ($error = flash('error')): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-lg">Sign In</button>
        </form>
        <p class="text-center mt-3 text-muted">Don't have an account? <a href="register.php">Create one</a></p>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
