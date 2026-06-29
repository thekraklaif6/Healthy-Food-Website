<?php
require_once __DIR__ . '/bootstrap.php';

$pdo = db();
$output = [];

// 1. Add ingredients column to products table if missing
try {
    $pdo->query("SELECT ingredients FROM products LIMIT 1");
} catch (Exception $e) {
    $pdo->exec("ALTER TABLE products ADD COLUMN ingredients VARCHAR(300) NOT NULL DEFAULT '' AFTER created_at");
    $output[] = "Added 'ingredients' column to products table";
}

// 2. Create reviews table
$pdo->exec("CREATE TABLE IF NOT EXISTS reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  rating TINYINT NOT NULL DEFAULT 5 CHECK (rating BETWEEN 1 AND 5),
  comment TEXT NOT NULL,
  is_approved TINYINT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
$output[] = "Created reviews table";

// 3. Insert reviews seed data if missing
$stmt = $pdo->query("SELECT COUNT(*) FROM reviews");
if ($stmt->fetchColumn() == 0) {
    $pdo->exec("INSERT INTO reviews (name, rating, comment, is_approved) VALUES
        ('Mary Lukach', 5, 'Amazing food and fast delivery!', 1),
        ('John Smith', 5, 'Best healthy meals I have tried.', 1),
        ('Sara Lee', 5, 'Super tasty and quick service!', 1),
        ('Ahmed Hassan', 4, 'Great variety and fresh ingredients.', 1),
        ('Emma Wilson', 5, 'The meal planner helped me stay on track!', 1)");
    $output[] = "Inserted reviews seed data";
}

// 4. Create meals table
$pdo->exec("CREATE TABLE IF NOT EXISTS meals (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(180) NOT NULL,
  type ENUM('breakfast','lunch','dinner') NOT NULL,
  diet_type VARCHAR(80) NOT NULL,
  goal VARCHAR(80) NOT NULL DEFAULT 'All',
  calories INT NULL,
  protein_g INT NULL,
  carbs_g INT NULL,
  fats_g INT NULL,
  is_active TINYINT NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
$output[] = "Created meals table";

// 5. Add unit_price column to cart_items if missing
try {
    $pdo->query("SELECT unit_price FROM cart_items LIMIT 1");
} catch (Exception $e) {
    $pdo->exec("ALTER TABLE cart_items ADD COLUMN unit_price DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER product_id");
    // Backfill unit_price from products table for any existing rows
    $pdo->exec("UPDATE cart_items ci INNER JOIN products p ON p.id = ci.product_id SET ci.unit_price = p.price WHERE ci.unit_price = 0");
    $output[] = "Added 'unit_price' column to cart_items table";
}

// Update unique key to include unit_price (so same product with different size = separate items)
try {
    $pdo->query("SELECT 1 FROM cart_items WHERE 1=0 GROUP BY cart_id, product_id, unit_price");
    $pdo->exec("ALTER TABLE cart_items DROP FOREIGN KEY fk_cart_items_cart");
    $pdo->exec("ALTER TABLE cart_items DROP INDEX uniq_cart_product");
    $pdo->exec("ALTER TABLE cart_items ADD UNIQUE KEY uniq_cart_product_unit (cart_id, product_id, unit_price)");
    $pdo->exec("ALTER TABLE cart_items ADD CONSTRAINT fk_cart_items_cart FOREIGN KEY (cart_id) REFERENCES carts(id)");
    $output[] = "Updated unique key to (cart_id, product_id, unit_price)";
} catch (Exception $e) {
    // May already be updated
}

// 6. Insert meals seed data if empty
$stmt = $pdo->query("SELECT COUNT(*) FROM meals");
if ($stmt->fetchColumn() == 0) {
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
    foreach ($meals as $m) {
        $insert->execute($m);
    }
    $output[] = "Inserted meals seed data";
}

// 6. Create meal_shopping_items table
$pdo->exec("CREATE TABLE IF NOT EXISTS meal_shopping_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  meal_id INT NOT NULL,
  item VARCHAR(255) NOT NULL,
  CONSTRAINT fk_shopping_meal FOREIGN KEY (meal_id) REFERENCES meals(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
$output[] = "Created meal_shopping_items table";

// 7. Insert missing drink/wrap products that might not exist
$productsToAdd = [
    [2, 'Classic Tomato Soup', 'Rich and creamy tomato soup made with fresh ingredients and a touch of herbs.', 180, 6, 12.40, 'images/image35.jpg', ''],
    [2, 'Grilled Chicken Plate', 'Juicy grilled chicken served with creamy mashed potatoes and fresh roasted vegetables.', 450, 35, 22.40, 'images/image36.jpg', ''],
    [2, 'Croissant Sandwich', 'Flaky croissant filled with fresh veggies and rich, savory flavors.', 320, 12, 22.40, 'images/image38.jpg', ''],
    [4, 'Creamy Cheesecake', 'Smooth and creamy cheesecake with a light, perfectly balanced sweetness.', 280, 7, 22.40, 'images/image39.jpg', ''],
];

$check = $pdo->prepare("SELECT COUNT(*) FROM products WHERE name = ?");
$insert = $pdo->prepare("INSERT IGNORE INTO products (category_id, name, description, calories, protein, price, image_url, ingredients) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
foreach ($productsToAdd as $p) {
    $check->execute([$p[1]]);
    if ($check->fetchColumn() == 0) {
        $insert->execute($p);
        $output[] = "Added product: " . $p[1];
    }
}

echo json_encode(['success' => true, 'messages' => $output]);
