<?php
/**
 * Core helper functions
 */

require_once __DIR__ . '/database.php';

$sessionLifetime = 60 * 60 * 24 * 30; // 30 days
session_set_cookie_params([
    'lifetime' => $sessionLifetime,
    'path'     => '/',
    'httponly' => true,
    'samesite' => 'Lax',
]);
ini_set('session.gc_maxlifetime', $sessionLifetime);
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
        try {
            $rows = db()->query('SELECT skey, sval FROM settings')->fetchAll();
            foreach ($rows as $row) $cache[$row['skey']] = $row['sval'];
        } catch (Throwable $e) {
            error_log('settings load failed: ' . $e->getMessage());
        }
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
    try { return db()->query('SELECT * FROM categories ORDER BY sort_order, name')->fetchAll(); }
    catch (Throwable $e) { return []; }
}

function get_featured_categories() {
    try { return db()->query('SELECT * FROM categories WHERE is_featured = 1 ORDER BY sort_order, name')->fetchAll(); }
    catch (Throwable $e) { return []; }
}

function get_featured_products($limit = 8) {
    try {
        $stmt = db()->prepare('SELECT * FROM products WHERE is_featured = 1 AND is_active = 1 ORDER BY created_at DESC LIMIT ?');
        $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (Throwable $e) { return []; }
}

function get_latest_products($limit = 8) {
    try {
        $stmt = db()->prepare('SELECT * FROM products WHERE is_active = 1 ORDER BY created_at DESC LIMIT ?');
        $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (Throwable $e) { return []; }
}

function get_products_by_category($category_id) {
    try {
        $stmt = db()->prepare('SELECT * FROM products WHERE category_id = ? AND is_active = 1 ORDER BY created_at DESC');
        $stmt->execute([$category_id]);
        return $stmt->fetchAll();
    } catch (Throwable $e) { return []; }
}

function get_product($slug) {
    try {
        $stmt = db()->prepare('SELECT * FROM products WHERE slug = ? LIMIT 1');
        $stmt->execute([$slug]);
        return $stmt->fetch();
    } catch (Throwable $e) { return false; }
}

function get_category($slug) {
    try {
        $stmt = db()->prepare('SELECT * FROM categories WHERE slug = ? LIMIT 1');
        $stmt->execute([$slug]);
        return $stmt->fetch();
    } catch (Throwable $e) { return false; }
}

function get_product_reviews($product_id) {
    try {
        $stmt = db()->prepare('SELECT * FROM reviews WHERE product_id = ? AND is_approved = 1 ORDER BY created_at DESC');
        $stmt->execute([$product_id]);
        return $stmt->fetchAll();
    } catch (Throwable $e) { return []; }
}

function get_avg_rating($product_id) {
    try {
        $stmt = db()->prepare('SELECT AVG(rating) AS avg, COUNT(*) AS cnt FROM reviews WHERE product_id = ? AND is_approved = 1');
        $stmt->execute([$product_id]);
        $row = $stmt->fetch();
        return ['avg' => round($row['avg'] ?? 0, 2), 'count' => (int)($row['cnt'] ?? 0)];
    } catch (Throwable $e) { return ['avg' => 0, 'count' => 0]; }
}

function get_all_reviews() {
    try { return db()->query('SELECT r.*, p.name AS product_name FROM reviews r JOIN products p ON r.product_id = p.id ORDER BY r.created_at DESC')->fetchAll(); }
    catch (Throwable $e) { return []; }
}

function get_cart() {
    if (is_logged_in()) {
        try {
            $stmt = db()->prepare('SELECT * FROM cart_items WHERE customer_id = ?');
            $stmt->execute([$_SESSION['customer_id']]);
            $cart = [];
            foreach ($stmt->fetchAll() as $row) {
                $cart[$row['product_id']] = [
                    'id'       => $row['product_id'],
                    'name'     => $row['product_name'],
                    'price'    => $row['price'],
                    'image'    => $row['image'],
                    'quantity' => $row['quantity'],
                ];
            }
            return $cart;
        } catch (Throwable $e) { return []; }
    }
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

    if (is_logged_in()) {
        $check = db()->prepare('SELECT id, quantity FROM cart_items WHERE customer_id = ? AND product_id = ?');
        $check->execute([$_SESSION['customer_id'], $product_id]);
        $existing = $check->fetch();
        if ($existing) {
            db()->prepare('UPDATE cart_items SET quantity = ?, price = ?, image = ? WHERE id = ?')
                ->execute([$existing['quantity'] + $quantity, $price, $product['image'], $existing['id']]);
        } else {
            db()->prepare('INSERT INTO cart_items (customer_id, product_id, product_name, price, image, quantity) VALUES (?, ?, ?, ?, ?, ?)')
                ->execute([$_SESSION['customer_id'], $product_id, $product['name'], $price, $product['image'], $quantity]);
        }
    } else {
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
    }
    return true;
}

function update_cart_qty($product_id, $quantity) {
    if (is_logged_in()) {
        if ($quantity <= 0) {
            db()->prepare('DELETE FROM cart_items WHERE customer_id = ? AND product_id = ?')
                ->execute([$_SESSION['customer_id'], $product_id]);
        } else {
            db()->prepare('UPDATE cart_items SET quantity = ? WHERE customer_id = ? AND product_id = ?')
                ->execute([$quantity, $_SESSION['customer_id'], $product_id]);
        }
    } else {
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
}

function remove_from_cart($product_id) {
    if (is_logged_in()) {
        db()->prepare('DELETE FROM cart_items WHERE customer_id = ? AND product_id = ?')
            ->execute([$_SESSION['customer_id'], $product_id]);
    } else {
        $cart = get_cart();
        unset($cart[$product_id]);
        $_SESSION['cart'] = $cart;
    }
}

function clear_cart() {
    if (is_logged_in()) {
        db()->prepare('DELETE FROM cart_items WHERE customer_id = ?')
            ->execute([$_SESSION['customer_id']]);
    }
    unset($_SESSION['cart']);
}

function merge_session_cart_to_db() {
    if (!is_logged_in()) return;
    $sessionCart = $_SESSION['cart'] ?? [];
    if (empty($sessionCart)) return;

    foreach ($sessionCart as $pid => $item) {
        $check = db()->prepare('SELECT id, quantity FROM cart_items WHERE customer_id = ? AND product_id = ?');
        $check->execute([$_SESSION['customer_id'], $pid]);
        $existing = $check->fetch();
        if ($existing) {
            db()->prepare('UPDATE cart_items SET quantity = ? WHERE id = ?')
                ->execute([$existing['quantity'] + $item['quantity'], $existing['id']]);
        } else {
            db()->prepare('INSERT INTO cart_items (customer_id, product_id, product_name, price, image, quantity) VALUES (?, ?, ?, ?, ?, ?)')
                ->execute([$_SESSION['customer_id'], $pid, $item['name'], $item['price'], $item['image'], $item['quantity']]);
        }
    }
    unset($_SESSION['cart']);
}

function is_logged_in() {
    return isset($_SESSION['customer_id']);
}

function current_customer() {
    if (!is_logged_in()) return null;
    try {
        $stmt = db()->prepare('SELECT * FROM customers WHERE id = ?');
        $stmt->execute([$_SESSION['customer_id']]);
        return $stmt->fetch();
    } catch (Throwable $e) { return null; }
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

function placeholder_image($text = 'Tooba Art', $w = 600, $h = 600) {
    return 'https://placehold.co/' . $w . 'x' . $h . '/f5efe6/b08968?text=' . urlencode($text);
}

function handle_image_upload($fieldName, $existingUrl = '') {
    if (empty($_FILES[$fieldName]['name']) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
        return trim($_POST[$fieldName . '_url'] ?? '') ?: $existingUrl;
    }

    $file = $_FILES[$fieldName];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, $allowedTypes)) {
        flash('error', 'Invalid image format. Use JPG, PNG, WEBP, or GIF.');
        return $existingUrl;
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        flash('error', 'Image too large. Maximum 5MB.');
        return $existingUrl;
    }

    $ext = match ($mimeType) {
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    };
    $filename = 'products/' . date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
    $targetPath = __DIR__ . '/../assets/uploads/' . $filename;
    if (!is_dir(dirname($targetPath))) mkdir(dirname($targetPath), 0775, true);

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return 'assets/uploads/' . $filename;
    }

    flash('error', 'Failed to save image.');
    return $existingUrl;
}
