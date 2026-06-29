<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

$data = json_input();
$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$phone = trim($data['phone'] ?? '');
$date = trim($data['date'] ?? '');
$time = trim($data['time'] ?? '');
$guests = (int) ($data['guests'] ?? 2);
$message = trim($data['message'] ?? '');

if (!$name || !$email || !$date || !$time) {
    respond(['message' => 'Missing required fields'], 400);
}

$pdo = db();

// Ensure table has correct columns
try {
    $pdo->query("SELECT reservation_date FROM reservations LIMIT 1");
} catch (Exception $e) {
    $pdo->exec("ALTER TABLE reservations ADD COLUMN reservation_date DATE NULL AFTER phone");
}
try {
    $pdo->query("SELECT reservation_time FROM reservations LIMIT 1");
} catch (Exception $e) {
    $pdo->exec("ALTER TABLE reservations ADD COLUMN reservation_time TIME NULL AFTER reservation_date");
}

$userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;

$stmt = $pdo->prepare('INSERT INTO reservations (user_id, name, email, phone, reservation_date, reservation_time, guests, message, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
$stmt->execute([$userId, $name, $email, $phone, $date, $time, $guests, $message, 'pending']);

respond(['message' => 'Reservation received', 'id' => (int) $pdo->lastInsertId()], 201);
