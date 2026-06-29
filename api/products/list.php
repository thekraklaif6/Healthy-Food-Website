<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

$category = trim((string) ($_GET['category'] ?? ''));
$pdo = db();

if ($category === '') {
    $stmt = $pdo->query('
        SELECT p.id, p.name, p.description, p.image_url, p.calories, p.protein, p.price, c.slug AS category_slug
        FROM products p
        INNER JOIN categories c ON c.id = p.category_id
        ORDER BY p.id ASC
    ');
    respond(['items' => $stmt->fetchAll()]);
}

$stmt = $pdo->prepare('
    SELECT p.id, p.name, p.description, p.image_url, p.calories, p.protein, p.price, c.slug AS category_slug
    FROM products p
    INNER JOIN categories c ON c.id = p.category_id
    WHERE c.slug = ?
    ORDER BY p.id ASC
');
$stmt->execute([$category]);
respond(['items' => $stmt->fetchAll()]);
