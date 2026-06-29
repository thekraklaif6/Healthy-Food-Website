<?php
// api/meal-plans/generate.php

// bootstrap يضبط الـ session بنفس إعدادات login
// فتصبح $_SESSION['user_id'] متاحة هون
require_once __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(['success' => false, 'message' => 'Method not allowed'], 405);
}

$data     = json_input(); // من bootstrap.php
$goal     = isset($data['goal'])         ? trim($data['goal'])     : 'Healthy Living';
$diet     = isset($data['dietType'])     ? trim($data['dietType']) : 'Standard';
$weight   = isset($data['weightKg'])     ? (float)$data['weightKg']  : 70;
$height   = isset($data['heightCm'])     ? (float)$data['heightCm']  : 170;
$activity = isset($data['activityLevel'])? (float)$data['activityLevel'] : 1.5;
$selected = isset($data['selectedMeals'])? $data['selectedMeals']   : [];
// selectedMeals = ['Monday'=>['breakfast'=>id,'lunch'=>id,'dinner'=>id], ...]

$allowed_goals = ['Weight Loss','Muscle Gain','Healthy Living'];
$allowed_diets = ['Standard','Vegan','Keto'];
if (!in_array($goal, $allowed_goals)) $goal = 'Healthy Living';
if (!in_array($diet, $allowed_diets)) $diet = 'Standard';

// ---- حساب السعرات (Harris-Benedict) ----
$bmr = 10 * $weight + 6.25 * $height - 5 * 25 + 5; // افتراض عمر 25
$tdee = round($bmr * $activity);

switch ($goal) {
    case 'Weight Loss':  $targetCal = $tdee - 500; break;
    case 'Muscle Gain':  $targetCal = $tdee + 300; break;
    default:             $targetCal = $tdee;
}

// نسب الماكروز حسب الهدف
switch ($goal) {
    case 'Weight Loss':
        $prot = round($targetCal * 0.35 / 4);
        $carb = round($targetCal * 0.35 / 4);
        $fat  = round($targetCal * 0.30 / 9);
        break;
    case 'Muscle Gain':
        $prot = round($targetCal * 0.30 / 4);
        $carb = round($targetCal * 0.45 / 4);
        $fat  = round($targetCal * 0.25 / 9);
        break;
    default:
        $prot = round($targetCal * 0.25 / 4);
        $carb = round($targetCal * 0.45 / 4);
        $fat  = round($targetCal * 0.30 / 9);
}

try {
    $pdo  = db();
    $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
    $plan = [];
    $usedMealIds   = [];
    $shoppingItems = [];

    foreach ($days as $day) {
        $dayMeals = [];

        foreach (['breakfast','lunch','dinner'] as $type) {
            // إذا المستخدم اختار وجبة معينة لهاليوم
            $manualId = $selected[$day][$type] ?? null;

            if ($manualId) {
                $stmt = $pdo->prepare("SELECT * FROM meals WHERE id = ? AND is_active = 1");
                $stmt->execute([$manualId]);
                $meal = $stmt->fetch();
            } else {
                // اختيار عشوائي من الوجبات المناسبة
                // نتجنب تكرار نفس الوجبة في اليوم
                $excludeIds = empty($usedMealIds) ? [0] : $usedMealIds;
                $placeholders = implode(',', array_fill(0, count($excludeIds), '?'));

                $stmt = $pdo->prepare("
                    SELECT * FROM meals
                    WHERE type = ?
                      AND diet_type = ?
                      AND (goal = ? OR goal = 'All')
                      AND is_active = 1
                      AND id NOT IN ($placeholders)
                    ORDER BY RAND()
                    LIMIT 1
                ");
                $stmt->execute(array_merge([$type, $diet, $goal], $excludeIds));
                $meal = $stmt->fetch();

                // إذا ما لقى وجبة جديدة، خذ أي وجبة
                if (!$meal) {
                    $stmt = $pdo->prepare("
                        SELECT * FROM meals
                        WHERE type = ? AND diet_type = ? AND (goal = ? OR goal = 'All') AND is_active = 1
                        ORDER BY RAND() LIMIT 1
                    ");
                    $stmt->execute([$type, $diet, $goal]);
                    $meal = $stmt->fetch();
                }
            }

            if ($meal) {
                $dayMeals[$type] = $meal;
                $usedMealIds[] = $meal['id'];

                // جلب مواد التسوق الخاصة بهاي الوجبة
                $shopStmt = $pdo->prepare("SELECT item FROM meal_shopping_items WHERE meal_id = ?");
                $shopStmt->execute([$meal['id']]);
                foreach ($shopStmt->fetchAll() as $row) {
                    if (!in_array($row['item'], $shoppingItems)) {
                        $shoppingItems[] = $row['item'];
                    }
                }
            }
        }

        $plan[] = [
            'day'       => $day,
            'breakfast' => $dayMeals['breakfast']['name']  ?? '-',
            'lunch'     => $dayMeals['lunch']['name']      ?? '-',
            'dinner'    => $dayMeals['dinner']['name']     ?? '-',
            'b_id'      => $dayMeals['breakfast']['id']    ?? null,
            'l_id'      => $dayMeals['lunch']['id']        ?? null,
            'd_id'      => $dayMeals['dinner']['id']       ?? null,
        ];
    }

    respond([
        'plan' => [
            'days'         => $plan,
            'shoppingList' => $shoppingItems,
            'macros'       => [
                'calories' => $targetCal,
                'protein'  => $prot,
                'carbs'    => $carb,
                'fats'     => $fat
            ]
        ]
    ]);

} catch (Exception $e) {
    respond(['success' => false, 'message' => $e->getMessage()], 500);
}