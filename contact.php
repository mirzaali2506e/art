<?php
require_once __DIR__ . '/config/functions.php';

$wa = setting('whatsapp');
$email = setting('email');
$addr = setting('address');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf'] ?? '')) {
        flash('error', 'Invalid request.');
        redirect('contact.php');
    }
    // Store message in settings table as a simple contact log (or email in real app)
    $name = e($_POST['name']);
    $email_in = e($_POST['email']);
    $msg = e($_POST['message']);
    db()->prepare("INSERT INTO reviews (product_id, name, rating, comment, is_approved) VALUES (0, ?, 5, ?, 0)")
        ->execute(["Contact: $name ($email_in)", $msg]);
    flash('success', 'Thank you! Your message has been received. We\'ll get back to you on WhatsApp or email shortly.');
    redirect('contact.php');
}

$pageTitle = 'Contact Us';
$activePage = 'contact';
include __DIR__ . '/includes/header.php';
?>

<div class="breadcrumb container">
    <a href="index.php">Home</a><span class="sep">/</span>
    <span>Contact</span>
</div>

<section class="section">
    <div class="container">
        <div class="section-head">
            <h1>Get in Touch</h1>
            <p>We'd love to hear from you — questions, custom orders, or just say hi!</p>
            <div class="line"></div>
        </div>

        <div class="grid grid-2" style="gap:3rem;align-items:start">
            <div>
                <h3 class="mb-3">Contact Information</h3>
                <div style="font-size:1.05rem;line-height:2">
                    <p><strong>WhatsApp:</strong> <a href="https://wa.me/<?= e(preg_replace('/\D/', '', $wa)) ?>" target="_blank"><?= e($wa) ?></a></p>
                    <p><strong>Email:</strong> <a href="mailto:<?= e($email) ?>"><?= e($email) ?></a></p>
                    <p><strong>Address:</strong> <?= e($addr) ?></p>
                    <p><strong>Hours:</strong> Mon - Sat, 10am - 8pm</p>
                </div>

                <div class="mt-4">
                    <a href="https://wa.me/<?= e(preg_replace('/\D/', '', $wa)) ?>" class="btn btn-accent btn-lg" target="_blank">Chat on WhatsApp</a>
                </div>
            </div>

            <div class="form-card" style="max-width:none">
                <h3 class="mb-3">Send a Message</h3>
                <?php if ($success = flash('success')): ?>
                    <div class="alert alert-success"><?= e($success) ?></div>
                <?php endif; ?>
                <form method="post">
                    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                    <div class="form-group">
                        <label>Your Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Message</label>
                        <textarea name="message" class="form-control" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
