<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

$data = json_input();

$protein = (array) ($data['protein'] ?? []);
$sauce = (array) ($data['sauce'] ?? []);
$extras = isset($data['extras']) && is_array($data['extras']) ? $data['extras'] : [];

if (empty($protein) || empty($sauce)) {
    respond(['message' => 'protein and sauce are required'], 400);
}

$totalCalories = ((int) ($protein['cal'] ?? 0)) + ((int) ($sauce['cal'] ?? 0));
$totalPrice = ((float) ($protein['price'] ?? 0)) + ((float) ($sauce['price'] ?? 0));

$extraNames = [];
foreach ($extras as $extra) {
    $totalCalories += (int) ($extra['cal'] ?? 0);
    $totalPrice += (float) ($extra['price'] ?? 0);
    $extraNames[] = (string) ($extra['name'] ?? 'Extra');
}

$userId = $_SESSION['user_id'] ?? null;
$pdo = db();
$stmt = $pdo->prepare('
    INSERT INTO custom_meals (user_id, protein, sauce, extras_json, calories, total_price)
    VALUES (?, ?, ?, ?, ?, ?)
');
$stmt->execute([
    $userId,
    (string) ($protein['name'] ?? ''),
    (string) ($sauce['name'] ?? ''),
    json_encode($extraNames, JSON_UNESCAPED_UNICODE),
    $totalCalories,
    number_format($totalPrice, 2, '.', ''),
]);

respond([
    'calories' => $totalCalories,
    'price' => number_format($totalPrice, 2, '.', ''),
    'extrasCount' => count($extraNames),
], 201);
