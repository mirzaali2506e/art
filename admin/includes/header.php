<?php
require_once __DIR__ . '/../../config/functions.php';
require_admin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Admin') ?> — BeadCraft Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>
<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="logo">Bead<span>Craft</span></div>
        <nav class="admin-nav">
            <a href="dashboard.php" class="<?= ($activePage ?? '') === 'dashboard' ? 'active' : '' ?>">📊 Dashboard</a>
            <a href="products.php" class="<?= ($activePage ?? '') === 'products' ? 'active' : '' ?>">📦 Products</a>
            <a href="categories.php" class="<?= ($activePage ?? '') === 'categories' ? 'active' : '' ?>">🏷️ Categories</a>
            <a href="orders.php" class="<?= ($activePage ?? '') === 'orders' ? 'active' : '' ?>">🛒 Orders</a>
            <a href="customers.php" class="<?= ($activePage ?? '') === 'customers' ? 'active' : '' ?>">👥 Customers</a>
            <a href="reviews.php" class="<?= ($activePage ?? '') === 'reviews' ? 'active' : '' ?>">⭐ Reviews</a>
            <a href="settings.php" class="<?= ($activePage ?? '') === 'settings' ? 'active' : '' ?>">⚙️ Settings</a>
            <a href="logout.php" style="margin-top:auto;color:var(--accent-400)">🚪 Logout</a>
            <a href="../index.php" style="color:var(--primary-300);font-size:0.85rem">← View Store</a>
        </nav>
    </aside>
    <div class="admin-content">
