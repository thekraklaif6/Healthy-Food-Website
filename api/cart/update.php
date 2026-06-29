<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

$userId = require_auth();
$data = json_input();
$itemId = (int) ($data['itemId'] ?? 0);
$quantity = (int) ($data['quantity'] ?? 1);

if ($itemId <= 0 || $quantity <= 0) {
    respond(['message' => 'itemId and quantity must be valid'], 400);
}

$pdo = db();
$stmt = $pdo->prepare('
    UPDATE cart_items ci
    INNER JOIN carts c ON c.id = ci.cart_id
    SET ci.quantity = ?
    WHERE ci.id = ? AND c.user_id = ?
');
$stmt->execute([$quantity, $itemId, $userId]);

if ($stmt->rowCount() === 0) {
    respond(['message' => 'Cart item not found'], 404);
}

respond(['message' => 'Cart item updated']);
