<?php
require_once __DIR__ . '/config/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('index.php');

if (!csrf_verify($_POST['csrf'] ?? '')) {
    flash('error', 'Invalid request. Please try again.');
    redirect('cart.php');
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'add':
        add_to_cart((int)$_POST['product_id'], max(1, (int)($_POST['quantity'] ?? 1)));
        flash('success', 'Product added to cart!');
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        redirect($referer ?: ($_POST['redirect'] ?? 'index.php'));
        break;

    case 'update':
        update_cart_qty((int)$_POST['product_id'], max(0, (int)$_POST['quantity']));
        redirect('cart.php');
        break;

    case 'remove':
        remove_from_cart((int)$_POST['product_id']);
        flash('success', 'Item removed from cart.');
        redirect('cart.php');
        break;

    case 'clear':
        clear_cart();
        redirect('cart.php');
        break;

    default:
        redirect('cart.php');
}
