<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

$userId = require_auth();
$data = json_input();
$itemId = (int) ($data['itemId'] ?? 0);

if ($itemId <= 0) {
    respond(['message' => 'Invalid itemId'], 400);
}

$pdo = db();
$stmt = $pdo->prepare('
    DELETE ci FROM cart_items ci
    INNER JOIN carts c ON c.id = ci.cart_id
    WHERE ci.id = ? AND c.user_id = ?
');
$stmt->execute([$itemId, $userId]);

if ($stmt->rowCount() === 0) {
    respond(['message' => 'Cart item not found'], 404);
}

respond(['message' => 'Item removed from cart']);
