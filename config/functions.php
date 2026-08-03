<?php
/**
 * Core helper functions
 */

require_once __DIR__ . '/database.php';

session_start();

function redirect($path) {
    header('Location: ' . $path);
    exit;
}

function e($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function setting($key, $default = '') {
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        $rows = db()->query('SELECT skey, sval FROM settings')->fetchAll();
        foreach ($rows as $row) $cache[$row['skey']] = $row['sval'];
    }
    return $cache[$key] ?? $default;
}

function money($amount) {
    return CURRENCY . ' ' . number_format((float)$amount, 0);
}

function current_price($product) {
    return (!empty($product['sale_price']) && $product['sale_price'] > 0)
        ? $product['sale_price'] : $product['price'];
}

function is_on_sale($product) {
    return !empty($product['sale_price']) && $product['sale_price'] > 0 && $product['sale_price'] < $product['price'];
}

function discount_percent($product) {
    if (!is_on_sale($product)) return 0;
    return round((1 - $product['sale_price'] / $product['price']) * 100);
}

function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    return strtolower($text) ?: 'n-a';
}

function get_categories() {
    return db()->query('SELECT * FROM categories ORDER BY sort_order, name')->fetchAll();
}

function get_featured_categories() {
    return db()->query('SELECT * FROM categories WHERE is_featured = 1 ORDER BY sort_order, name')->fetchAll();
}

function get_featured_products($limit = 8) {
    $stmt = db()->prepare('SELECT * FROM products WHERE is_featured = 1 AND is_active = 1 ORDER BY created_at DESC LIMIT ?');
    $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function get_latest_products($limit = 8) {
    $stmt = db()->prepare('SELECT * FROM products WHERE is_active = 1 ORDER BY created_at DESC LIMIT ?');
    $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function get_products_by_category($category_id) {
    $stmt = db()->prepare('SELECT * FROM products WHERE category_id = ? AND is_active = 1 ORDER BY created_at DESC');
    $stmt->execute([$category_id]);
    return $stmt->fetchAll();
}

function get_product($slug) {
    $stmt = db()->prepare('SELECT * FROM products WHERE slug = ? LIMIT 1');
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

function get_category($slug) {
    $stmt = db()->prepare('SELECT * FROM categories WHERE slug = ? LIMIT 1');
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

function get_product_reviews($product_id) {
    $stmt = db()->prepare('SELECT * FROM reviews WHERE product_id = ? AND is_approved = 1 ORDER BY created_at DESC');
    $stmt->execute([$product_id]);
    return $stmt->fetchAll();
}

function get_avg_rating($product_id) {
    $stmt = db()->prepare('SELECT AVG(rating) AS avg, COUNT(*) AS cnt FROM reviews WHERE product_id = ? AND is_approved = 1');
    $stmt->execute([$product_id]);
    $row = $stmt->fetch();
    return ['avg' => round($row['avg'] ?? 0, 2), 'count' => (int)($row['cnt'] ?? 0)];
}

function get_all_reviews() {
    return db()->query('SELECT r.*, p.name AS product_name FROM reviews r JOIN products p ON r.product_id = p.id ORDER BY r.created_at DESC')->fetchAll();
}

function get_cart() {
    return $_SESSION['cart'] ?? [];
}

function cart_count() {
    $cart = get_cart();
    $count = 0;
    foreach ($cart as $item) $count += $item['quantity'];
    return $count;
}

function cart_subtotal() {
    $cart = get_cart();
    $total = 0;
    foreach ($cart as $item) $total += $item['price'] * $item['quantity'];
    return $total;
}

function add_to_cart($product_id, $quantity = 1) {
    $stmt = db()->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
    if (!$product) return false;

    $price = current_price($product);
    $cart = get_cart();

    if (isset($cart[$product_id])) {
        $cart[$product_id]['quantity'] += $quantity;
    } else {
        $cart[$product_id] = [
            'id'       => $product['id'],
            'name'     => $product['name'],
            'price'    => $price,
            'image'    => $product['image'],
            'quantity' => $quantity,
        ];
    }
    $_SESSION['cart'] = $cart;
    return true;
}

function update_cart_qty($product_id, $quantity) {
    $cart = get_cart();
    if (isset($cart[$product_id])) {
        if ($quantity <= 0) {
            unset($cart[$product_id]);
        } else {
            $cart[$product_id]['quantity'] = $quantity;
        }
    }
    $_SESSION['cart'] = $cart;
}

function remove_from_cart($product_id) {
    $cart = get_cart();
    unset($cart[$product_id]);
    $_SESSION['cart'] = $cart;
}

function clear_cart() {
    unset($_SESSION['cart']);
}

function is_logged_in() {
    return isset($_SESSION['customer_id']);
}

function current_customer() {
    if (!is_logged_in()) return null;
    $stmt = db()->prepare('SELECT * FROM customers WHERE id = ?');
    $stmt->execute([$_SESSION['customer_id']]);
    return $stmt->fetch();
}

function is_admin() {
    return isset($_SESSION['admin_id']);
}

function require_admin() {
    if (!is_admin()) redirect('login.php');
}

function flash($key, $value = null) {
    if ($value === null) {
        $msg = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    $_SESSION['flash'][$key] = $value;
}

function csrf_token() {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}

function csrf_verify($token) {
    return !empty($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
}

function asset($path) {
    return SITE_URL . '/assets/' . $path;
}

function url($path) {
    return SITE_URL . '/' . ltrim($path, '/');
}

function placeholder_image($text = 'BeadCraft', $w = 600, $h = 600) {
    return 'https://placehold.co/' . $w . 'x' . $h . '/f5efe6/b08968?text=' . urlencode($text);
}
