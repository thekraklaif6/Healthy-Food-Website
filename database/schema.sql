CREATE DATABASE IF NOT EXISTS freshplate CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE freshplate;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(180) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(80) NOT NULL UNIQUE,
  name VARCHAR(120) NOT NULL
);

CREATE TABLE IF NOT EXISTS build_options (
  id INT AUTO_INCREMENT PRIMARY KEY,
  TYPE VARCHAR(50) NOT NULL,
  NAME VARCHAR(120) NOT NULL,
  calories INT NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  icon VARCHAR(50) DEFAULT NULL,
  image_url VARCHAR(255) DEFAULT NULL
);

INSERT IGNORE INTO build_options (TYPE, NAME, calories, price, image_url) VALUES
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
('extra', 'Mixed Nuts', 150, 2.50, 'images1/nuts.png');

CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL,
  name VARCHAR(180) NOT NULL,
  description TEXT NULL,
  image_url VARCHAR(255) NULL,
  calories INT NULL,
  protein INT NULL,
  price DECIMAL(10,2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  ingredients VARCHAR(300) NOT NULL DEFAULT '',
  CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE IF NOT EXISTS carts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL UNIQUE,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_carts_user FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS cart_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cart_id INT NOT NULL,
  product_id INT NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  UNIQUE KEY uniq_cart_product (cart_id, product_id),
  CONSTRAINT fk_cart_items_cart FOREIGN KEY (cart_id) REFERENCES carts(id),
  CONSTRAINT fk_cart_items_product FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  status VARCHAR(40) NOT NULL DEFAULT 'pending',
  total_price DECIMAL(10,2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  quantity INT NOT NULL,
  unit_price DECIMAL(10,2) NOT NULL,
  CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id),
  CONSTRAINT fk_order_items_product FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE IF NOT EXISTS meal_plans (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  goal VARCHAR(120) NOT NULL,
  diet_type VARCHAR(120) NOT NULL,
  weight_kg INT NULL,
  height_cm INT NULL,
  activity_level FLOAT NULL,
  allergies_json JSON NULL,
  summary_json JSON NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_meal_plans_user FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS custom_meals (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  protein VARCHAR(120) NOT NULL,
  sauce VARCHAR(120) NOT NULL,
  extras_json JSON NULL,
  calories INT NOT NULL,
  total_price DECIMAL(10,2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_custom_meals_user FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS contact_messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(180) NOT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  rating TINYINT NOT NULL DEFAULT 5 CHECK (rating BETWEEN 1 AND 5),
  comment TEXT NOT NULL,
  is_approved TINYINT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT IGNORE INTO categories (id, slug, name) VALUES
  (1, 'salads', 'Salads'),
  (2, 'meals', 'Healthy Main Courses'),
  (3, 'drinks', 'Drinks'),
  (4, 'sweets', 'Healthy Desserts'),
  (5, 'wraps', 'Healthy Sandwiches & Wraps');

INSERT IGNORE INTO reviews (name, rating, comment, is_approved) VALUES
  ('Mary Lukach', 5, 'Amazing food and fast delivery!', 1),
  ('John Smith', 5, 'Best healthy meals I have tried.', 1),
  ('Sara Lee', 5, 'Super tasty and quick service!', 1),
  ('Ahmed Hassan', 4, 'Great variety and fresh ingredients.', 1),
  ('Emma Wilson', 5, 'The meal planner helped me stay on track!', 1);

INSERT INTO products (category_id, name, description, calories, protein, price, image_url)
SELECT 4, 'Fresh Berry Cream Cake', 'Light cream cake with berries', 210, 8, 7.50, 'images1/sweet3.png'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Fresh Berry Cream Cake');

INSERT INTO products (category_id, name, description, calories, protein, price, image_url)
SELECT 3, 'Strawberry Smoothie', 'Refreshing fruit smoothie', 180, 6, 5.00, 'images/image37.jpg'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Strawberry Smoothie');

INSERT INTO products (category_id, name, description, calories, protein, price, image_url)
SELECT 3, 'Tropical Energy', 'A refreshing blend of pineapple, orange, and lime that delivers natural energy and a vibrant tropical flavor. Perfect for hot days or a quick healthy boost.', 350, 8, 7.99, 'images/image26 .jpg'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Tropical Energy');

INSERT INTO products (category_id, name, description, calories, protein, price, image_url)
SELECT 3, 'Pineapple Smoothie', 'A smooth and creamy mix of fresh pineapple, yogurt, and natural honey. Rich in vitamins and perfect for a refreshing healthy treat.', 420, 12, 8.49, 'images/image27.jpg'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Pineapple Smoothie');

INSERT INTO products (category_id, name, description, calories, protein, price, image_url)
SELECT 3, 'Fresh Orange Juice', 'Made from freshly squeezed ripe oranges, this juice is packed with Vitamin C to support your immune system and keep you energized.', 280, 6, 6.99, 'images/image28.jpeg'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Fresh Orange Juice');

INSERT INTO products (category_id, name, description, calories, protein, price, image_url)
SELECT 3, 'Green Detox', 'A revitalizing mix of kiwi, mint, lemon, and chia seeds designed to refresh your body and support natural detox.', 190, 5, 7.49, 'images/image29.jpeg'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Green Detox');

INSERT INTO products (category_id, name, description, calories, protein, price, image_url)
SELECT 2, 'Classic Tomato Soup', 'Rich and creamy tomato soup made with fresh ingredients and a touch of herbs.', 180, 6, 12.40, 'images/image35.jpg'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Classic Tomato Soup');

INSERT INTO products (category_id, name, description, calories, protein, price, image_url)
SELECT 2, 'Grilled Chicken Plate', 'Juicy grilled chicken served with creamy mashed potatoes and fresh roasted vegetables.', 450, 35, 22.40, 'images/image36.jpg'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Grilled Chicken Plate');

INSERT INTO products (category_id, name, description, calories, protein, price, image_url)
SELECT 2, 'Croissant Sandwich', 'Flaky croissant filled with fresh veggies and rich, savory flavors.', 320, 12, 22.40, 'images/image38.jpg'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Croissant Sandwich');

INSERT INTO products (category_id, name, description, calories, protein, price, image_url)
SELECT 4, 'Creamy Cheesecake', 'Smooth and creamy cheesecake with a light, perfectly balanced sweetness.', 280, 7, 22.40, 'images/image39.jpg'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Creamy Cheesecake');

INSERT INTO products (category_id, name, description, calories, protein, price, image_url)
SELECT 5, 'Italian Sandwich', 'Toasted sourdough bread, Italian salami, prosciutto, fresh mozzarella cheese, tomatoes, basil, and extra virgin olive oil. An authentic Italian meal.', 650, 32, 18.99, 'images/image11.jpeg'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Italian Sandwich');

INSERT INTO products (category_id, name, description, calories, protein, price, image_url)
SELECT 5, 'Chicken Club', 'Grilled chicken breast, crispy bacon, fresh lettuce, tomatoes, garlic mayo, and toasted bread with butter. A delicious and satisfying sandwich.', 780, 48, 22.99, 'images/image4.jpeg'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Chicken Club');

INSERT INTO products (category_id, name, description, calories, protein, price, image_url)
SELECT 5, 'Veggie Delight', 'Hummus with tahini, fresh avocado, cucumber, shredded carrots, sprouts, spinach, and whole wheat bread. Rich in fiber and vitamins.', 420, 15, 15.99, 'images/image7.jpeg'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Veggie Delight');

INSERT INTO products (category_id, name, description, calories, protein, price, image_url)
SELECT 5, 'Steak & Cheese', 'Grilled beef slices, melted cheddar cheese, grilled mushrooms, caramelized onions, black pepper sauce, and Italian sub rolls.', 890, 52, 28.99, 'images/image13.jpeg'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Steak & Cheese');

INSERT INTO `products` ( `category_id`, `name`, `description`, `image_url`, `calories`, `protein`, `price`, `created_at`, `ingredients`) VALUES
( 4, 'Fresh Berry Cream Cake', 'A soft vanilla sponge made with wholesome ingredients and layered with smooth, light cream, then topped with fresh strawberries, blueberries, and raspberries. Naturally sweet and delicately airy, each bite offers a refreshing burst of real fruit flavor without overwhelming richness. Carefully crafted to be lighter and balanced, this cake lets you indulge while staying true to a fresh and mindful lifestyle.', 'images1/sweet3.png', 210, 8, 7.50, '2026-05-08 13:02:46', ''),
( 3, 'Strawberry Smoothie', 'Refreshing fruit smoothie', 'images/image37.jpg', 180, 6, 5.00, '2026-05-08 13:02:46', ''),
(3, 'Tropical Energy', 'Pineapple, orange, and lime blend', 'images/image26 .jpg', 350, 8, 7.99, '2026-05-08 13:02:46', ''),
(3, 'Pineapple Smoothie', 'Creamy pineapple smoothie with yogurt and honey', 'images/image27.jpg', 420, 12, 8.49, '2026-05-08 13:02:46', ''),
( 3, 'Fresh Orange Juice', 'Freshly squeezed orange juice', 'images/image28.jpeg', 280, 6, 6.99, '2026-05-08 13:02:46', ''),
( 3, 'Green Detox', 'Kiwi, mint, lemon, and chia seeds detox drink', 'images/image29.jpeg', 190, 5, 7.49, '2026-05-08 13:02:46', ''),
(1, 'Greek Salad', ' A vibrant mix of fresh greens, cherry tomatoes, black olives, and creamy mozzarella.\r\n            Lightly dressed with extra virgin olive oil for a refined Mediterranean touch.', 'images1/salad1.png', 280, 16, 12.99, '2026-05-08 13:02:46', ''),
( 1, 'Chicken Salad', 'Grilled chicken breast served over fluffy quinoa with roasted chickpeas, fresh avocado, and cherry tomatoes. Finished with creamy tahini dressing for a wholesome and protein-packed meal.', 'images1/salad2.png', 500, 42, 32.99, '2026-05-08 13:02:46', ''),
( 1, 'Pasta Salad', 'A vibrant bowl of colorful fusilli, juicy cherry tomatoes, crunchy cucumbers, hearty chickpeas, and creamy feta — all tossed in a zesty lemon olive oil dressing. Light, refreshing, and packed with wholesome goodness in every bite.', 'images1/salad3.png', 470, 18, 21.99, '2026-05-08 13:02:46', ''),
( 5, 'Italian Sandwich', 'Italian salami, mozzarella, tomatoes, basil', 'images/image11.jpeg', 650, 32, 18.99, '2026-05-08 13:02:46', ''),
( 5, 'Chicken Club', 'Grilled chicken, bacon, and garlic mayo', 'images/image4.jpeg', 780, 48, 22.99, '2026-05-08 13:02:46', ''),
( 5, 'Veggie Delight', 'Hummus, avocado, and mixed vegetables', 'images/image7.jpeg', 420, 15, 15.99, '2026-05-08 13:02:46', ''),
( 5, 'Steak & Cheese', 'Grilled beef with cheddar and mushrooms', 'images/image13.jpeg', 890, 52, 28.99, '2026-05-08 13:02:46', ''),
( 4, 'Cookies', 'Soft and chewy cookies made with whole-grain oats, natural honey, and dark chocolate chips. A lighter sweet treat with simple, wholesome ingredients. 🍪', 'images1/sweet1.png', 210, 8, 7.99, '2026-05-08 14:52:03', ''),
(4, 'Cinnamon Rolls', 'Soft and fluffy cinnamon rolls made with whole-grain flour, lightly sweetened with honey and filled with warm cinnamon. A comforting, naturally sweet treat with a soft, delicious texture.', 'images1/sweet2.png', 180, 6, 8.49, '2026-05-08 14:54:25', ''),
(4, 'Muffin', 'Soft and moist muffins made with whole-grain flour, natural honey, and fresh fruits like blueberries or banana. A healthy, wholesome treat perfect for breakfast or a light snack.', 'images1/sweet4.png', 195, 5, 6.99, '2026-05-08 14:55:19', ''),
( 4, 'Croissant', 'Flaky and buttery croissants made with whole-grain flour and a touch of honey. Light, airy, and naturally delicious—a perfect healthy twist on a classic pastry. 🥐', 'images1/sweet5.png', 230, 12, 9.99, '2026-05-08 14:56:04', ''),
( 2, 'Healthy Veggie Pizza', 'A delicious and balanced pizza made with a light crust, rich tomato sauce, melted cheese, and fresh toppings. Perfectly satisfying, flavorful, and crafted for a healthier way to enjoy a classic favorite.', 'images1/meal2.png', 300, 18, 15.99, '2026-05-08 16:17:27', ''),
( 2, 'Healthy Burger', 'A juicy and wholesome burger made with fresh ingredients, crisp vegetables, and a flavorful patty. A lighter take on a comfort-food favorite without sacrificing taste.', 'images1/meal3.png', 360, 32, 20.50, '2026-05-08 16:19:09', ''),
( 2, 'Avocado & Egg Toast', 'Whole grain toast with avocado and egg', 'images1/meal4.png', 210, 8, 6.99, '2026-05-08 13:02:46', 'Avocado\r\nTomatoes\r\nEggs\r\nToasted bread'),
( 2, 'Avocado Toast', 'Simple and fresh avocado toast', 'images1/meal7.png', 180, 6, 8.99, '2026-05-08 13:02:46', 'Avocado\r\nTomatoes\r\nFeta Cheese\r\nToasted bread'),
( 2, 'Ricotta & Tomato Toast', 'Ricotta and tomato on artisan toast', 'images1/meal8.png', 230, 12, 10.49, '2026-05-08 13:02:46', 'Ricotta Cheese\r\nToasted bread\r\nOlive Oil\r\nTomatoes'),
( 2, 'Oatmeal with Fruits', 'Oatmeal bowl topped with fresh fruits', 'images1/meal6.png', 195, 5, 7.99, '2026-05-08 13:02:46', 'Oats\r\nPeanut Butter\r\nBanana & Mango\r\nStrawberries & Blueberries'),
( 2, 'Salmon Quinoa Bowl', 'Salmon with quinoa and vegetables', 'images1/meal10.png', 410, 28, 16.99, '2026-05-08 13:02:46', 'Salmon\r\nQuinoa\r\nBroccoli\r\nGreen Beans & Carrots'),
( 2, 'Tacos', 'Healthy tacos with lean filling', 'images1/meal11.png', 380, 20, 13.99, '2026-05-08 13:02:46', 'Tortillas\r\nMeat\r\nTomatoes & Onions'),
( 2, 'Chicken with Rice', 'Lean chicken with rice and greens', 'images1/meal12.png', 430, 32, 14.99, '2026-05-08 13:02:46', 'Chicken Breast\r\nRice\r\nParsley'),
( 2, 'Steak with Rice', 'Grilled steak with rice', 'images1/meal14.png', 520, 35, 18.49, '2026-05-08 13:02:46', 'Steak\r\nRice\r\nCarrots\r\nZucchini');


CREATE TABLE IF NOT EXISTS meals (
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
);

CREATE TABLE IF NOT EXISTS meal_shopping_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  meal_id INT NOT NULL,
  item VARCHAR(255) NOT NULL,
  CONSTRAINT fk_shopping_meal FOREIGN KEY (meal_id) REFERENCES meals(id) ON DELETE CASCADE
);

INSERT INTO products (category_id, name, description, calories, protein, price, image_url)
SELECT 2, 'Custom Meal Plan', 'Personalized meal plan with 7 days of breakfast, lunch, and dinner based on your goals and diet preferences.', 0, 0, 0.00, 'images/default-food.png'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Custom Meal Plan');

INSERT IGNORE INTO meals (name, type, diet_type, goal, calories, protein_g, carbs_g, fats_g) VALUES
('Classic Oatmeal', 'breakfast', 'Standard', 'All', 350, 12, 58, 7),
('Spinach Omelet', 'breakfast', 'Standard', 'All', 280, 18, 4, 20),
('Protein Smoothie', 'breakfast', 'Standard', 'All', 320, 25, 40, 5),
('Avocado Toast', 'breakfast', 'Standard', 'All', 290, 8, 30, 15),
('Tofu Scramble', 'breakfast', 'Vegan', 'All', 260, 16, 18, 14),
('Vegan Pancakes', 'breakfast', 'Vegan', 'All', 340, 10, 52, 10),
('Keto Coffee', 'breakfast', 'Keto', 'All', 220, 2, 3, 22),
('Egg Muffins', 'breakfast', 'Keto', 'All', 310, 20, 4, 24),
('Grilled Chicken & Rice', 'lunch', 'Standard', 'All', 520, 42, 58, 8),
('Tuna Salad Bowl', 'lunch', 'Standard', 'All', 380, 32, 20, 18),
('Lentil Stew', 'lunch', 'Standard', 'All', 360, 20, 55, 6),
('Turkey Sandwich', 'lunch', 'Standard', 'All', 440, 35, 40, 14),
('Vegan Buddha Bowl', 'lunch', 'Vegan', 'All', 410, 16, 60, 12),
('Quinoa & Black Beans', 'lunch', 'Vegan', 'All', 390, 18, 62, 8),
('Grilled Salmon Salad', 'lunch', 'Keto', 'All', 450, 38, 8, 30),
('Chicken Caesar Wrap', 'lunch', 'Keto', 'All', 480, 40, 10, 32),
('Baked Salmon & Veggies', 'dinner', 'Standard', 'All', 480, 38, 22, 24),
('Lean Beef Stir-fry', 'dinner', 'Standard', 'All', 510, 40, 35, 20),
('Zucchini Pasta', 'dinner', 'Standard', 'All', 340, 18, 28, 16),
('Grilled Tofu', 'dinner', 'Standard', 'All', 360, 28, 20, 18),
('Vegan Chili', 'dinner', 'Vegan', 'All', 380, 18, 50, 12),
('Stuffed Bell Peppers', 'dinner', 'Vegan', 'All', 350, 14, 48, 14),
('Steak with Butter', 'dinner', 'Keto', 'All', 550, 42, 4, 40),
('Keto Meatballs', 'dinner', 'Keto', 'All', 490, 36, 8, 34);
