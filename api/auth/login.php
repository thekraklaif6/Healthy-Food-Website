<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

$data = json_input();
$email = strtolower(trim((string) ($data['email'] ?? '')));
$password = (string) ($data['password'] ?? '');

if ($email === '' || $password === '') {
    respond(['message' => 'email and password are required'], 400);
}

$pdo = db();
$stmt = $pdo->prepare('SELECT id, name, email, password_hash FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, (string) $user['password_hash'])) {
    respond(['message' => 'Invalid credentials'], 401);
}

$_SESSION['user_id'] = (int) $user['id'];

respond([
    'id' => (int) $user['id'],
    'name' => $user['name'],
    'email' => $user['email'],
]);
