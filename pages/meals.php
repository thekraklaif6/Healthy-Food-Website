<?php
include "db.php";

// Featured products
$pizza = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE name LIKE '%Pizza%' AND category_id = 2 LIMIT 1"));
$burger = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE name LIKE '%Burger%' AND category_id = 2 LIMIT 1"));

// Breakfast items (toasts + oatmeal)
$breakfast_ids = [14, 15, 16, 17];
$breakfast_items = [];
foreach ($breakfast_ids as $bid) {
    $r = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id = $bid"));
    if ($r) $breakfast_items[] = $r;
}

// Dinner items
$dinner_ids = [18, 19, 20, 21];
$dinner_items = [];
foreach ($dinner_ids as $did) {
    $r = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id = $did"));
    if ($r) $dinner_items[] = $r;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Meals</title>
     <link rel="stylesheet" href="style1.css">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
     <link rel="stylesheet" href="nav-bar.css">
     <script src="config.js" defer></script>
     <script src="common.js" defer></script>
     <script src="nav_bar.js" defer></script>
     <style>
        .fp-cart-badge { display: none !important; }
     </style>
</head>

<body>
    <header class="navbar">
        <div class="m1"> 
    <i class="fa-solid fa-leaf fa-2xl"></i>
    <div class="logo">
        <h2>FreshPlate</h2>
        <span>Eat Clean. Feel Strong.</span>
    </div>
        </div>

    <nav class="nav-links2">
        <a href="home.html">Home</a>
        <a href="home.html#menu">Menu</a>
        <a href="meal-planner.html">Meal Planner</a>
        <a href="build_meal.php">Build Meal</a>
        <a href="about.html">About</a>
        <a href="contact.html">Contact</a>
    </nav>

    <div class="nav-right">
      <div style="position:relative;display:inline-block;cursor:pointer" onclick="toggleCart()">
                       <i class="fa-solid fa-cart-shopping" style="width: 40px; height: 30px;"></i>
              <span id="cart-count" style="display:none;position:absolute;top:-7px;right:-2px;background:#2ecc71;color:white;border-radius:50%;padding:2px 6px;font-size:10px;font-weight:bold;">0</span>
      </div>
      <button class="login-btn3" onclick="window.location.href='sign_in.html'">Login</button>
      <div class="hamburger">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
      </div>
    </div>
</header>

 <section class="hero">
    <div class="overlay">
      <h1>Fresh & Balanced Meals</h1>
      <p>Elevate your daily dining experience with our carefully curated selection of wholesome, nutrient-rich meals</p>
    </div>
  </section>

<?php if ($pizza) { ?>
<section class="meal1-section">
    <div class="meal-image">
       <img src="<?php echo $pizza['image_url']; ?>" onerror="this.src='images/default-food.png'">
    </div>
    <div class="meal-text">
        <h2><?php echo $pizza['name']; ?></h2>
        <p><?php echo $pizza['description']; ?></p>
        <a href="meals2.php?id=<?php echo $pizza['id']; ?>">
        <button class="meal-btn">View Pizza Details</button>
           </a>
    </div>
</section>
<?php } ?>

<?php if ($burger) { ?>
<section class="meal1-section">
    <div class="meal-text">
        <h2><?php echo $burger['name']; ?></h2>
        <p><?php echo $burger['description']; ?></p>
        <a href="meals2.php?id=<?php echo $burger['id']; ?>">
            <button class="meal-btn">View Burger Details</button>
        </a>
    </div>
    <div class="meal-image">
        <img src="<?php echo $burger['image_url']; ?>" onerror="this.src='images/default-food.png'">
    </div>
</section>
<?php } ?>

<section class="meal-section2">
    <h2 class="meal-title">Our Breakfast Selection</h2>
    <div class="meal1-container">
<?php foreach ($breakfast_items as $meal) { ?>        
        <div class="meal-card">
            <img src="<?php echo $meal['image_url']; ?>" onerror="this.src='images/default-food.png'">
            <h3><?php echo $meal['name']; ?></h3>
            <p><?php echo $meal['calories']; ?> kcal | <?php echo $meal['protein']; ?>g protein</p>
            <a href="meals2.php?id=<?php echo $meal['id']; ?>">
            <button>View Details</button>
            </a>
        </div>
<?php } ?>
    </div>
</section>

<div class="section-divider">
    <span></span>
    <div class="divider-icon">🍃</div>
    <span></span>
</div>

<section class="meal-section2">
    <h2 class="meal-title">Our Dinner Selection</h2>
    <div class="meal-container">
<?php foreach ($dinner_items as $meal) { ?>  
        <div class="meal-card">
            <img src="<?php echo $meal['image_url']; ?>" onerror="this.src='images/default-food.png'">
          <h3><?php echo $meal['name']; ?></h3>
            <p><?php echo $meal['calories']; ?> kcal | <?php echo $meal['protein']; ?>g protein</p>
           <a href="meals2.php?id=<?php echo $meal['id']; ?>">
            <button>View Details</button>
            </a>
        </div>
<?php } ?>
    </div>
</section>

<footer class="footer">
  <div class="container-footer">
    <div class="footer-section brand">
      <div class="logo">
        <span class="icon"><i class="fa-solid fa-leaf"></i></span>
        <h3>FreshPlate</h3>
      </div>
      <p>
        Nourishing your body with wholesome, delicious meals that fuel your
        healthy lifestyle.
      </p>
    </div>

    <div class="footer-section">
      <h3>Quick Links</h3>
      <ul>
        <li><a href="home.html#menu">Menu</a></li>
        <li><a href="meal-planner.html">Meal Planner</a></li>
        <li><a href="about.html">About Us</a></li>
        <li><a href="contact.html">Contact</a></li>
      </ul>
    </div>

    <div class="footer-section contact">
      <h3>Contact</h3>
      <p><i class="fa-solid fa-location-dot"></i> 123 Health Street, Wellness City</p>
      <p><i class="fa-solid fa-phone"></i> (555) 123-4567</p>
      <p><i class="fa-solid fa-envelope"></i> hello@freshplate.com</p>
    </div>

    <div class="footer-section">
      <h3>Follow Us</h3>
      <div class="social">
        <a href="#"><img src="images1/facebook.png" ></a>
        <a href="#"><img src="images1/instagram.png" ></a>
        <a href="#"><img src="images1/twitter.png" ></a>
      </div>
    </div>
  </div>

  <div class="copyright">
    © 2026 FreshPlate. All rights reserved.
  </div>
</footer>

</body>
</html>
