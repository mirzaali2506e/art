<?php
require_once __DIR__ . '/../config/functions.php';
$cartCount  = cart_count();
$whatsapp   = setting('whatsapp');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? SITE_NAME) ?> — <?= e(SITE_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>
<div class="topbar">
    <div class="container">
        <span>Free shipping on orders over PKR 5,000 · WhatsApp: <?= e($whatsapp) ?></span>
        <span>
            <?php if (is_admin()): ?>
                <a href="admin/dashboard.php" style="color:var(--gold-400);font-weight:600">Admin Panel</a> · <a href="admin/logout.php">Admin Logout</a>
            <?php elseif (is_logged_in()): ?>
                <a href="account.php">My Account</a> · <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Sign In</a> · <a href="register.php">Register</a>
            <?php endif; ?>
        </span>
    </div>
</div>

<header class="header">
    <div class="container header-inner">
        <a href="index.php" class="logo">Tooba<span> Art</span></a>
        <nav class="nav" id="mainNav">
            <div class="nav-close-bar">
                <span class="nav-close-title">Menu</span>
                <button class="nav-close-btn" onclick="document.getElementById('mainNav').classList.remove('open')" aria-label="Close menu">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <a href="index.php" class="<?= ($activePage ?? '') === 'home' ? 'active' : '' ?>">Home</a>
            <a href="collections.php" class="<?= ($activePage ?? '') === 'collections' ? 'active' : '' ?>">Collections</a>
            <a href="about.php" class="<?= ($activePage ?? '') === 'about' ? 'active' : '' ?>">About</a>
            <a href="contact.php" class="<?= ($activePage ?? '') === 'contact' ? 'active' : '' ?>">Contact</a>
            <div class="nav-mobile-auth">
                <?php if (is_admin()): ?>
                    <a href="admin/dashboard.php" class="btn btn-primary btn-block">Open Admin Panel</a>
                    <a href="admin/logout.php" class="btn btn-outline btn-block" style="margin-top:0.5rem">Admin Logout</a>
                <?php elseif (is_logged_in()): ?>
                    <a href="account.php" class="btn btn-primary btn-block">My Account</a>
                    <a href="logout.php" class="btn btn-outline btn-block" style="margin-top:0.5rem">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary btn-block">Sign In</a>
                    <a href="register.php" class="btn btn-outline btn-block" style="margin-top:0.5rem">Register</a>
                <?php endif; ?>
            </div>
        </nav>
        <div class="header-actions">
            <a href="cart.php" class="icon-btn" aria-label="Cart">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                <?php if ($cartCount > 0): ?><span class="cart-count"><?= $cartCount ?></span><?php endif; ?>
            </a>
            <button class="icon-btn mobile-toggle" onclick="document.getElementById('mainNav').classList.toggle('open')" aria-label="Menu">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
        </div>
    </div>
</header>
<main>
