<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

$userId = require_auth();
$pdo = db();

$stmt = $pdo->prepare('SELECT id, name, email, created_at FROM users WHERE id = ? LIMIT 1');
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    respond(['message' => 'User not found'], 404);
}

respond($user);
