<?php
// api/gallery/get.php — جلب صور الغاليري النشطة

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../db.php';

try {
    $stmt = db()->prepare("
        SELECT id, image_url, alt_text
        FROM gallery
        WHERE is_active = 1
        ORDER BY sort_order ASC, id ASC
    ");
    $stmt->execute();
    $images = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'images'  => $images
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}