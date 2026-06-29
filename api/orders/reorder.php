<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

$userId = require_auth();
$data = json_input();
$orderId = (int) ($data['orderId'] ?? 0);
if ($orderId <= 0) {
    respond(['message' => 'Invalid orderId'], 400);
}

$pdo = db();

$orderStmt = $pdo->prepare('SELECT id FROM orders WHERE id = ? AND user_id = ? LIMIT 1');
$orderStmt->execute([$orderId, $userId]);
if (!$orderStmt->fetch()) {
    respond(['message' => 'Order not found'], 404);
}

$cartStmt = $pdo->prepare('SELECT id FROM carts WHERE user_id = ? LIMIT 1');
$cartStmt->execute([$userId]);
$cart = $cartStmt->fetch();
if (!$cart) {
    $insertCart = $pdo->prepare('INSERT INTO carts (user_id) VALUES (?)');
    $insertCart->execute([$userId]);
    $cartId = (int) $pdo->lastInsertId();
} else {
    $cartId = (int) $cart['id'];
}

$itemsStmt = $pdo->prepare('SELECT product_id, quantity FROM order_items WHERE order_id = ?');
$itemsStmt->execute([$orderId]);
$items = $itemsStmt->fetchAll();
if (!$items) {
    respond(['message' => 'Order has no items'], 400);
}

$checkStmt = $pdo->prepare('SELECT id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ? LIMIT 1');
$updateStmt = $pdo->prepare('UPDATE cart_items SET quantity = ? WHERE id = ?');
$insertStmt = $pdo->prepare('INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?, ?, ?)');

foreach ($items as $item) {
    $productId = (int) $item['product_id'];
    $quantity = (int) $item['quantity'];

    $checkStmt->execute([$cartId, $productId]);
    $existing = $checkStmt->fetch();
    if ($existing) {
        $updateStmt->execute([(int) $existing['quantity'] + $quantity, (int) $existing['id']]);
    } else {
        $insertStmt->execute([$cartId, $productId, $quantity]);
    }
}

respond(['message' => 'Items re-added to cart']);
