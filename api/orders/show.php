<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

$userId = require_auth();
$orderId = (int) ($_GET['id'] ?? 0);
if ($orderId <= 0) {
    respond(['message' => 'Invalid order id'], 400);
}

$pdo = db();
$orderStmt = $pdo->prepare('
    SELECT id, status, total_price, created_at
    FROM orders
    WHERE id = ? AND user_id = ?
    LIMIT 1
');
$orderStmt->execute([$orderId, $userId]);
$order = $orderStmt->fetch();

if (!$order) {
    respond(['message' => 'Order not found'], 404);
}

$itemsStmt = $pdo->prepare('
    SELECT oi.product_id, oi.quantity, oi.unit_price, p.name
    FROM order_items oi
    INNER JOIN products p ON p.id = oi.product_id
    WHERE oi.order_id = ?
    ORDER BY oi.id ASC
');
$itemsStmt->execute([$orderId]);
$order['items'] = $itemsStmt->fetchAll();

respond($order);
