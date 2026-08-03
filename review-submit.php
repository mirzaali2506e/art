<?php
require_once __DIR__ . '/config/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf'] ?? '')) {
        flash('error', 'Invalid request.');
        redirect('product.php?slug=' . urlencode($_POST['slug'] ?? ''));
    }
    $stmt = db()->prepare('INSERT INTO reviews (product_id, name, rating, comment) VALUES (?, ?, ?, ?)');
    $stmt->execute([
        (int)$_POST['product_id'],
        e($_POST['name']),
        (int)$_POST['rating'],
        e($_POST['comment']),
    ]);
    flash('success', 'Thank you! Your review has been submitted and is pending approval.');
    redirect('product.php?slug=' . urlencode($_POST['slug']));
}

redirect('index.php');
