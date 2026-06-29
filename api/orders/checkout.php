<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

$userId = require_auth();
$pdo = db();

try {
    $pdo->beginTransaction();

    $cartStmt = $pdo->prepare('SELECT id FROM carts WHERE user_id = ? LIMIT 1');
    $cartStmt->execute([$userId]);
    $cart = $cartStmt->fetch();

    if (!$cart) {
        $pdo->rollBack();
        respond(['message' => 'Cart is empty'], 400);
    }

    $itemsStmt = $pdo->prepare('
        SELECT ci.product_id, ci.quantity, ci.unit_price
        FROM cart_items ci
        WHERE ci.cart_id = ?
    ');
    $itemsStmt->execute([(int) $cart['id']]);
    $items = $itemsStmt->fetchAll();

    if (!$items) {
        $pdo->rollBack();
        respond(['message' => 'Cart is empty'], 400);
    }

    $total = 0.0;
    foreach ($items as $item) {
        $total += (float) $item['unit_price'] * (int) $item['quantity'];
    }

    $orderStmt = $pdo->prepare('INSERT INTO orders (user_id, status, total_price) VALUES (?, ?, ?)');
    $orderStmt->execute([$userId, 'pending', number_format($total, 2, '.', '')]);
    $orderId = (int) $pdo->lastInsertId();

    $itemInsert = $pdo->prepare('
        INSERT INTO order_items (order_id, product_id, quantity, unit_price)
        VALUES (?, ?, ?, ?)
    ');
    foreach ($items as $item) {
        $itemInsert->execute([
            $orderId,
            (int) $item['product_id'],
            (int) $item['quantity'],
            number_format((float) $item['unit_price'], 2, '.', ''),
        ]);
    }

    $clearStmt = $pdo->prepare('DELETE FROM cart_items WHERE cart_id = ?');
    $clearStmt->execute([(int) $cart['id']]);

    $pdo->commit();
    respond([
        'message' => 'Order placed successfully',
        'orderId' => $orderId,
        'total' => number_format($total, 2, '.', ''),
    ], 201);
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    respond(['message' => 'Checkout failed'], 500);
}
