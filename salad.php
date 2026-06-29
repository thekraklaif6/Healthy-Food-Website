<?php
include "db.php";

$sql = "SELECT * FROM products WHERE category_id = 1";
$result = mysqli_query($conn, $sql);

$products = [];
while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salads</title>
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
              <span id="cart-count" style="display:none;position:absolute;top:-7px;right:-2px;
              background:#2ecc71;color:white;border-radius:50%;padding:2px 6px;font-size:10px;
              font-weight:bold;">0</span>
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
            <h1>Fresh & Healthy Salads</h1>
            <p>Discover our delicious and nutritious selection</p>
        </div>
    </section>

    <section class="intro">
    <h2>Our Signature Salads</h2>
    <p>Fresh ingredients, balanced nutrition, and vibrant flavors crafted for a healthy lifestyle.</p>
    <div class="divider"></div>
</section>

<?php if (count($products) > 0) { ?>
    <div class="cards-container"> 
<?php foreach ($products as $meal) { ?>
    <div class="card1">
    <img src="<?php echo $meal['image_url']; ?>" class="card-img1" onerror="this.src='images/default-food.png'">
    <div class="card-content1">
       <h2><?php echo $meal['name']; ?></h2>
        <p class="description1"><?php echo $meal['description']; ?></p>
        <div class="nutrition1">
            <span>🔥 <?php echo $meal['calories']; ?> cal</span>
            <span>💪 <?php echo $meal['protein']; ?>g protein</span>
        </div>
        <div class="card-footer1">
            <p class="price1">$<?php echo $meal['price']; ?></p>
           <a href="salad-details.php?id=<?php echo $meal['id']; ?>">
            <button class="btn1">View Details</button>
            </a>
        </div>
    </div>
</div>
<?php } ?>
</div>
<?php } else { ?>
    <div style="text-align:center;padding:60px 20px"><h2>Coming Soon</h2><p>Our fresh salad selection is being prepared. Check back soon!</p></div>
<?php } ?>

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
