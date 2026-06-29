<?php
require_once __DIR__ . '/../bootstrap.php';

$pdo = db();
$price = isset($_GET['price']) ? round((float)$_GET['price'], 2) : 49.99;

$stmt = $pdo->query("SELECT id FROM products WHERE name = 'Custom Meal Plan' LIMIT 1");
$existing = $stmt->fetch();

if ($existing) {
    $pdo->prepare("UPDATE products SET price = ?, description = 'Personalized meal plan with 7 days of breakfast, lunch, and dinner.' WHERE id = ?")->execute([$price, $existing['id']]);
    respond(['id' => (int)$existing['id']]);
}

$insert = $pdo->prepare("
    INSERT INTO products (category_id, name, description, calories, protein, price, image_url)
    VALUES (2, 'Custom Meal Plan', 'Personalized meal plan with 7 days of breakfast, lunch, and dinner.', 0, 0, ?, 'images/default-food.png')
");
$insert->execute([$price]);
respond(['id' => (int)$pdo->lastInsertId()]);
