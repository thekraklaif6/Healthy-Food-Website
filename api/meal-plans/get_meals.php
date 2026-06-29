<?php
// api/meal-plans/get_meals.php

require_once __DIR__ . '/../bootstrap.php';

$diet = isset($_GET['diet']) ? trim($_GET['diet']) : 'Standard';
$goal = isset($_GET['goal']) ? trim($_GET['goal'])  : 'All';

$allowed_diets = ['Standard', 'Vegan', 'Keto'];
$allowed_goals = ['Weight Loss', 'Muscle Gain', 'Healthy Living', 'All'];

if (!in_array($diet, $allowed_diets)) $diet = 'Standard';
if (!in_array($goal, $allowed_goals)) $goal = 'All';

try {
    $pdo = db();

    $result = ['breakfast' => [], 'lunch' => [], 'dinner' => []];

    foreach (['breakfast', 'lunch', 'dinner'] as $type) {
        $stmt = $pdo->prepare("
            SELECT id, name, calories, protein_g, carbs_g, fats_g
            FROM meals
            WHERE type = ?
              AND diet_type = ?
              AND (goal = ? OR goal = 'All')
              AND is_active = 1
            ORDER BY RAND()
        ");
        $stmt->execute([$type, $diet, $goal]);
        $result[$type] = $stmt->fetchAll();
    }

    echo json_encode(['success' => true, 'meals' => $result]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}