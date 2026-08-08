<?php
require_once __DIR__ . '/../config/functions.php';
$cartCount  = cart_count();
$whatsapp   = setting('whatsapp');
$categories = get_categories();
$pageSlug   = $activePage ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? SITE_NAME) ?> — <?= e(SITE_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='6' fill='%231c2128'/%3E%3Ctext x='16' y='23' font-family='Georgia,serif' font-size='18' font-weight='bold' fill='%23c9a44e' text-anchor='middle'%3ET%3C/text%3E%3C/svg%3E">
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

<header class="header" id="siteHeader">
    <div class="container header-inner">
        <a href="index.php" class="logo" aria-label="Tooba Art Collection">
            <span class="logo-mark">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="12" cy="6" r="2.5" stroke="#c9a44e"/>
                    <circle cx="7" cy="12" r="2.5" stroke="#c9a44e"/>
                    <circle cx="17" cy="12" r="2.5" stroke="#c9a44e"/>
                    <circle cx="12" cy="18" r="2.5" stroke="#c9a44e"/>
                    <path d="M12 8.5v7M9.5 12h5" stroke="#c9a44e" stroke-width="1"/>
                </svg>
            </span>
            Tooba<span> Art</span>
        </a>
        <nav class="nav" id="mainNav">
            <div class="nav-close-bar">
                <span class="nav-close-title">Menu</span>
                <button class="nav-close-btn" onclick="document.getElementById('mainNav').classList.remove('open')" aria-label="Close menu">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <a href="index.php" class="<?= $pageSlug === 'home' ? 'active' : '' ?>">Home</a>
            <a href="collections.php" class="<?= $pageSlug === 'collections' ? 'active' : '' ?>">Collections</a>
            <?php if (!empty($categories)): ?>
            <div class="nav-dropdown nav-desktop-only">
                <button class="nav-dropdown-trigger <?= $pageSlug === 'category' ? 'active' : '' ?>" type="button">
                    Categories
                    <svg width="12" height="8" viewBox="0 0 12 8" fill="none" stroke="currentColor" stroke-width="1.8" style="margin-left:0.3rem"><path d="M1 1l5 5 5-5"/></svg>
                </button>
                <div class="nav-dropdown-menu">
                    <?php foreach ($categories as $cat):
                        $count = db()->query('SELECT COUNT(*) FROM products WHERE category_id='.(int)$cat['id'].' AND is_active=1')->fetchColumn();
                    ?>
                        <a href="category.php?slug=<?= e($cat['slug']) ?>">
                            <?= e($cat['name']) ?>
                            <span class="count"><?= $count ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            <a href="about.php" class="<?= $pageSlug === 'about' ? 'active' : '' ?>">About</a>
            <a href="contact.php" class="<?= $pageSlug === 'contact' ? 'active' : '' ?>">Contact</a>
            <div class="nav-mobile-auth">
                <?php if (is_admin()): ?>
                    <a href="admin/dashboard.php" class="btn btn-gold btn-block">Open Admin Panel</a>
                    <a href="admin/logout.php" class="btn btn-outline btn-block" style="margin-top:0.5rem">Admin Logout</a>
                <?php elseif (is_logged_in()): ?>
                    <a href="account.php" class="btn btn-gold btn-block">My Account</a>
                    <a href="logout.php" class="btn btn-outline btn-block" style="margin-top:0.5rem">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-gold btn-block">Sign In</a>
                    <a href="register.php" class="btn btn-outline btn-block" style="margin-top:0.5rem">Register</a>
                <?php endif; ?>
            </div>
        </nav>
        <div class="header-actions">
            <a href="cart.php" class="icon-btn" aria-label="Cart">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                <?php if ($cartCount > 0): ?><span class="cart-count"><?= $cartCount ?></span><?php endif; ?>
            </a>
            <button class="icon-btn mobile-toggle" onclick="document.getElementById('mainNav').classList.toggle('open')" aria-label="Menu">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
        </div>
    </div>
</header>

<main>
