<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

$pdo = db();
$stmt = $pdo->query('
    SELECT p.id, p.name, p.description, p.image_url, p.calories, p.protein, p.price, c.slug AS category_slug
    FROM products p
    INNER JOIN categories c ON c.id = p.category_id
    ORDER BY RAND()
    LIMIT 6
');

$items = $stmt->fetchAll();

foreach ($items as &$item) {
    $item['price'] = (float) $item['price'];
    $item['calories'] = (int) $item['calories'];
    $item['protein'] = (int) $item['protein'];
}

respond(['items' => $items]);