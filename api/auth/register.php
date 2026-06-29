<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

$data = json_input();
$name = trim((string) ($data['name'] ?? ''));
$email = strtolower(trim((string) ($data['email'] ?? '')));
$password = (string) ($data['password'] ?? '');

if ($name === '' || $email === '' || $password === '') {
    respond(['message' => 'name, email, password are required'], 400);
}

$pdo = db();
$check = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
$check->execute([$email]);
if ($check->fetch()) {
    respond(['message' => 'Email already exists'], 409);
}

$hash = password_hash($password, PASSWORD_BCRYPT);
$stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)');
$stmt->execute([$name, $email, $hash]);

$id = (int) $pdo->lastInsertId();
$_SESSION['user_id'] = $id;

respond([
    'id' => $id,
    'name' => $name,
    'email' => $email,
], 201);
