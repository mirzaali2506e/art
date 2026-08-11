<?php
require_once __DIR__ . '/config/functions.php';

$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
          || ($_POST['ajax'] ?? '') === '1';

function jsonResponse($data) {
    if (!headers_sent()) {
        header('Content-Type: application/json');
    }
    echo json_encode($data);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('index.php');

if (!csrf_verify($_POST['csrf'] ?? '')) {
    if ($isAjax) jsonResponse(['success' => false, 'message' => 'Invalid request.']);
    flash('error', 'Invalid request. Please try again.');
    redirect('cart.php');
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'add':
        try {
            $ok = add_to_cart((int)$_POST['product_id'], max(1, (int)($_POST['quantity'] ?? 1)));
            $count = cart_count();
        } catch (Throwable $e) {
            error_log('add_to_cart failed: ' . $e->getMessage());
            $ok = false;
            $count = 0;
        }
        if ($isAjax) {
            jsonResponse([
                'success' => $ok !== false,
                'message' => $ok ? 'Product added to cart!' : 'Could not add to cart. Please try again.',
                'cart_count' => $count,
            ]);
        }
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
