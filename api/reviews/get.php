<?php
// api/reviews/get.php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../db.php';

try {
    $stmt = db()->prepare("
        SELECT id, name, rating, comment, created_at
        FROM reviews
        WHERE is_approved = 1
        ORDER BY created_at DESC
        LIMIT 20
    ");
    $stmt->execute();
    $reviews = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'reviews' => $reviews
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}