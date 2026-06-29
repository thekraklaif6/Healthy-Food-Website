<?php
require_once __DIR__ . '/bootstrap.php';

$pdo = db();

$pdo->exec("CREATE TABLE IF NOT EXISTS build_options (
  id INT AUTO_INCREMENT PRIMARY KEY,
  TYPE VARCHAR(50) NOT NULL,
  NAME VARCHAR(120) NOT NULL,
  calories INT NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  icon VARCHAR(50) DEFAULT NULL,
  image_url VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

$stmt = $pdo->query("SELECT COUNT(*) FROM build_options");
if ($stmt->fetchColumn() == 0) {
    $pdo->exec("INSERT IGNORE INTO build_options (TYPE, NAME, calories, price, image_url) VALUES
        ('protein', 'Grilled Chicken', 180, 7.00, 'images1/chicken-leg.png'),
        ('protein', 'Lean Beef', 220, 12.00, 'images1/beef.png'),
        ('protein', 'Tofu', 120, 7.00, 'images1/tofu.png'),
        ('protein', 'Salmon', 250, 12.00, 'images1/salmon.png'),
        ('sauce', 'Olive Oil & Herbs', 90, 3.50, 'images1/olive-oil.png'),
        ('sauce', 'Tahini', 110, 2.00, 'images1/tahini.png'),
        ('sauce', 'Greek Yogurt', 60, 4.50, 'images1/yogurt.png'),
        ('sauce', 'Lemon & Garlic', 40, 1.00, 'images1/lemon.png'),
        ('extra', 'Avocado', 80, 2.00, 'images1/avocado.png'),
        ('extra', 'Quinoa', 120, 2.50, 'images1/food.png'),
        ('extra', 'Mixed Vegetables', 50, 2.50, 'images1/Vegetable.png'),
        ('extra', 'Mixed Nuts', 150, 2.50, 'images1/nuts.png')");
}

echo json_encode(['success' => true]);
