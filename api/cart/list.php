<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

$userId = require_auth();
$pdo = db();

$cartStmt = $pdo->prepare('SELECT id FROM carts WHERE user_id = ? LIMIT 1');
$cartStmt->execute([$userId]);
$cart = $cartStmt->fetch();

if (!$cart) {
    respond(['items' => [], 'total' => '0.00']);
}

$itemsStmt = $pdo->prepare('
    SELECT ci.id, ci.product_id, ci.quantity, ci.unit_price, p.name, p.image_url
    FROM cart_items ci
    INNER JOIN products p ON p.id = ci.product_id
    WHERE ci.cart_id = ?
    ORDER BY ci.id DESC
');
$itemsStmt->execute([(int) $cart['id']]);
$items = $itemsStmt->fetchAll();

$total = 0.0;
foreach ($items as &$item) {
    $lineTotal = (float) $item['unit_price'] * (int) $item['quantity'];
    $item['line_total'] = number_format($lineTotal, 2, '.', '');
    $total += $lineTotal;
}

respond([
    'items' => $items,
    'total' => number_format($total, 2, '.', ''),
]);
