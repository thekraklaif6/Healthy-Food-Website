<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

$userId = require_auth();
$pdo = db();
$status = trim((string) ($_GET['status'] ?? ''));

if ($status === '') {
    $ordersStmt = $pdo->prepare('
        SELECT id, status, total_price, created_at
        FROM orders
        WHERE user_id = ?
        ORDER BY id DESC
    ');
    $ordersStmt->execute([$userId]);
} else {
    $ordersStmt = $pdo->prepare('
        SELECT id, status, total_price, created_at
        FROM orders
        WHERE user_id = ? AND status = ?
        ORDER BY id DESC
    ');
    $ordersStmt->execute([$userId, $status]);
}
$orders = $ordersStmt->fetchAll();

if (!$orders) {
    respond(['orders' => []]);
}

$orderIds = array_map(static fn($o) => (int) $o['id'], $orders);
$placeholders = implode(',', array_fill(0, count($orderIds), '?'));

$itemsStmt = $pdo->prepare("
    SELECT oi.order_id, oi.quantity, oi.unit_price, p.name
    FROM order_items oi
    INNER JOIN products p ON p.id = oi.product_id
    WHERE oi.order_id IN ($placeholders)
    ORDER BY oi.id ASC
");
$itemsStmt->execute($orderIds);
$items = $itemsStmt->fetchAll();

$itemsByOrder = [];
foreach ($items as $item) {
    $orderId = (int) $item['order_id'];
    if (!isset($itemsByOrder[$orderId])) {
        $itemsByOrder[$orderId] = [];
    }
    $itemsByOrder[$orderId][] = [
        'name' => $item['name'],
        'quantity' => (int) $item['quantity'],
        'unit_price' => $item['unit_price'],
    ];
}

foreach ($orders as &$order) {
    $id = (int) $order['id'];
    $order['items'] = $itemsByOrder[$id] ?? [];
}

respond(['orders' => $orders]);
