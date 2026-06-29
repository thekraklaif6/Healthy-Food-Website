<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

$data = json_input();
$name = trim((string) ($data['name'] ?? ''));
$email = strtolower(trim((string) ($data['email'] ?? '')));
$message = trim((string) ($data['message'] ?? ''));

if ($name === '' || $email === '' || $message === '') {
    respond(['message' => 'name, email, message are required'], 400);
}

$pdo = db();
$stmt = $pdo->prepare('INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)');
$stmt->execute([$name, $email, $message]);

respond(['message' => 'Message sent successfully'], 201);
