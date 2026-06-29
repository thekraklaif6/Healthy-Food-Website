<?php
require_once __DIR__ . '/bootstrap.php';

$pdo = db();

$meals = [
    ['Classic Oatmeal', 'breakfast', 'Standard', 'All', 350, 12, 58, 7],
    ['Spinach Omelet', 'breakfast', 'Standard', 'All', 280, 18, 4, 20],
    ['Protein Smoothie', 'breakfast', 'Standard', 'All', 320, 25, 40, 5],
    ['Avocado Toast', 'breakfast', 'Standard', 'All', 290, 8, 30, 15],
    ['Tofu Scramble', 'breakfast', 'Vegan', 'All', 260, 16, 18, 14],
    ['Vegan Pancakes', 'breakfast', 'Vegan', 'All', 340, 10, 52, 10],
    ['Keto Coffee', 'breakfast', 'Keto', 'All', 220, 2, 3, 22],
    ['Egg Muffins', 'breakfast', 'Keto', 'All', 310, 20, 4, 24],
    ['Grilled Chicken & Rice', 'lunch', 'Standard', 'All', 520, 42, 58, 8],
    ['Tuna Salad Bowl', 'lunch', 'Standard', 'All', 380, 32, 20, 18],
    ['Lentil Stew', 'lunch', 'Standard', 'All', 360, 20, 55, 6],
    ['Turkey Sandwich', 'lunch', 'Standard', 'All', 440, 35, 40, 14],
    ['Vegan Buddha Bowl', 'lunch', 'Vegan', 'All', 410, 16, 60, 12],
    ['Quinoa & Black Beans', 'lunch', 'Vegan', 'All', 390, 18, 62, 8],
    ['Grilled Salmon Salad', 'lunch', 'Keto', 'All', 450, 38, 8, 30],
    ['Chicken Caesar Wrap', 'lunch', 'Keto', 'All', 480, 40, 10, 32],
    ['Baked Salmon & Veggies', 'dinner', 'Standard', 'All', 480, 38, 22, 24],
    ['Lean Beef Stir-fry', 'dinner', 'Standard', 'All', 510, 40, 35, 20],
    ['Zucchini Pasta', 'dinner', 'Standard', 'All', 340, 18, 28, 16],
    ['Grilled Tofu', 'dinner', 'Standard', 'All', 360, 28, 20, 18],
    ['Vegan Chili', 'dinner', 'Vegan', 'All', 380, 18, 50, 12],
    ['Stuffed Bell Peppers', 'dinner', 'Vegan', 'All', 350, 14, 48, 14],
    ['Steak with Butter', 'dinner', 'Keto', 'All', 550, 42, 4, 40],
    ['Keto Meatballs', 'dinner', 'Keto', 'All', 490, 36, 8, 34],
];

$insert = $pdo->prepare("INSERT IGNORE INTO meals (name, type, diet_type, goal, calories, protein_g, carbs_g, fats_g) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$count = 0;
foreach ($meals as $m) {
    try {
        $insert->execute($m);
        if ($insert->rowCount() > 0) $count++;
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
echo "Inserted $count meals";
