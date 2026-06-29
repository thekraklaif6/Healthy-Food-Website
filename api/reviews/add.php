<?php
// api/reviews/add.php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/../db.php';

$data    = json_decode(file_get_contents('php://input'), true);
$name    = isset($data['name'])    ? trim($data['name'])    : '';
$rating  = isset($data['rating'])  ? (int)$data['rating']   : 5;
$comment = isset($data['comment']) ? trim($data['comment'])  : '';

if (empty($name) || empty($comment)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Name and comment are required']);
    exit;
}

if ($rating < 1 || $rating > 5) $rating = 5;

if (mb_strlen($comment) > 500) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Comment too long (max 500 characters)']);
    exit;
}

try {
    $pdo = db();

    // منع التعليق المكرر من نفس الاسم (اختياري)
    // $check = $pdo->prepare("SELECT id FROM reviews WHERE name = ? LIMIT 1");
    // $check->execute([$name]);
    // if ($check->fetch()) {
    //     echo json_encode(['success' => false, 'message' => 'You already submitted a review']);
    //     exit;
    // }

    $stmt = $pdo->prepare("
        INSERT INTO reviews (name, rating, comment, is_approved)
        VALUES (?, ?, ?, 1)
    ");
    $stmt->execute([$name, $rating, $comment]);

    echo json_encode([
        'success' => true,
        'message' => 'Thank you! Your review has been posted.'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}