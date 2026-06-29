<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

$userId = require_auth();
$data = json_input();
$productId = (int) ($data['productId'] ?? 0);
$quantity = max(1, (int) ($data['quantity'] ?? 1));

if ($productId <= 0) {
    respond(['message' => 'Invalid productId'], 400);
}

$pdo = db();

// Get product base price
$prodStmt = $pdo->prepare('SELECT price FROM products WHERE id = ?');
$prodStmt->execute([$productId]);
$product = $prodStmt->fetch();
if (!$product) {
    respond(['message' => 'Product not found'], 404);
}
$basePrice = (float) $product['price'];

// Use provided unitPrice or fall back to base price
$unitPrice = isset($data['unitPrice']) ? (float) $data['unitPrice'] : $basePrice;

$cartStmt = $pdo->prepare('SELECT id FROM carts WHERE user_id = ? LIMIT 1');
$cartStmt->execute([$userId]);
$cart = $cartStmt->fetch();

if (!$cart) {
    $newCart = $pdo->prepare('INSERT INTO carts (user_id) VALUES (?)');
    $newCart->execute([$userId]);
    $cartId = (int) $pdo->lastInsertId();
} else {
    $cartId = (int) $cart['id'];
}

// Check for same product AND same unit_price (i.e. same size)
$itemStmt = $pdo->prepare('SELECT id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ? AND unit_price = ? LIMIT 1');
$itemStmt->execute([$cartId, $productId, $unitPrice]);
$item = $itemStmt->fetch();

if ($item) {
    $update = $pdo->prepare('UPDATE cart_items SET quantity = ? WHERE id = ?');
    $update->execute([(int) $item['quantity'] + $quantity, (int) $item['id']]);
} else {
    $insert = $pdo->prepare('INSERT INTO cart_items (cart_id, product_id, unit_price, quantity) VALUES (?, ?, ?, ?)');
    $insert->execute([$cartId, $productId, $unitPrice, $quantity]);
}

respond(['message' => 'Added to cart successfully'], 201);
