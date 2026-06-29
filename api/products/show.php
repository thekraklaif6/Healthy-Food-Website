<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    respond(['message' => 'Invalid id'], 400);
}

$pdo = db();
$stmt = $pdo->prepare('
    SELECT p.id, p.name, p.description, p.image_url, p.calories, p.protein, p.price, c.slug AS category_slug
    FROM products p
    INNER JOIN categories c ON c.id = p.category_id
    WHERE p.id = ?
    LIMIT 1
');
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) {
    respond(['message' => 'Product not found'], 404);
}

respond($item);
