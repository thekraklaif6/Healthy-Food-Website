<?php
include "db.php";

$proteins = mysqli_query($conn, "SELECT * FROM build_options WHERE TYPE='protein'");
$sauces   = mysqli_query($conn, "SELECT * FROM build_options WHERE TYPE='sauce'");
$extras   = mysqli_query($conn, "SELECT * FROM build_options WHERE TYPE='extra'");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Build Meal</title>

    <link rel="stylesheet" href="style2.css">
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
              <span id="cart-count" style="display:none;position:absolute;top:-8px;right:-2px;background:#2ecc71;color:white;border-radius:50%;padding:2px 6px;font-size:10px;font-weight:bold;">0</span>
            </div>
            <button class="login-btn3" onclick="window.location.href='sign_in.html'">Login</button>
            <div class="hamburger">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
        </div>
    </header>
<br><br><br><br><br><br>
    <section class="container">

        <div class="header-icon"><i class="fa-solid fa-utensils"></i></div>

        <h1>Build Your Custom Meal</h1>

        <p class="subtitle">Create your perfect meal by choosing protein, sauce, and extras</p>

        <div class="section">
            <span class="section-title">1. Choose Your Protein</span>
            <div class="options-grid" id="protein-group">
                <?php while($row = mysqli_fetch_assoc($proteins)) { ?>
                    <div class="card"
                        data-type="protein"
                        data-name="<?php echo $row['NAME']; ?>"
                        data-cal="<?php echo $row['calories']; ?>"
                        data-price="<?php echo $row['price']; ?>">
                        <img src="<?php echo $row['image_url']; ?>" onerror="this.src='images/default-food.png'">
                        <span><?php echo $row['NAME']; ?></span>
                    </div>
                <?php } ?>
            </div>
        </div>

        <div class="section">
            <span class="section-title">2. Choose Your Sauce</span>
            <div class="options-grid" id="sauce-group">
                <?php while($row = mysqli_fetch_assoc($sauces)) { ?>
                    <div class="card"
                        data-type="sauce"
                        data-name="<?php echo $row['NAME']; ?>"
                        data-cal="<?php echo $row['calories']; ?>"
                        data-price="<?php echo $row['price']; ?>">
                        <img src="<?php echo $row['image_url']; ?>" onerror="this.src='images/default-food.png'">
                        <span><?php echo $row['NAME']; ?></span>
                    </div>
                <?php } ?>
            </div>
        </div>

        <div class="section">
            <span class="section-title">3. Add Extras (Optional)</span>
            <div class="options-grid" id="extras-group">
                <?php while($row = mysqli_fetch_assoc($extras)) { ?>
                    <div class="card"
                        data-type="extra"
                        data-name="<?php echo $row['NAME']; ?>"
                        data-cal="<?php echo $row['calories']; ?>"
                        data-price="<?php echo $row['price']; ?>">
                        <img src="<?php echo $row['image_url']; ?>" onerror="this.src='images/default-food.png'">
                        <span><?php echo $row['NAME']; ?></span>
                    </div>
                <?php } ?>
            </div>
        </div>

        <button id="build-btn" class="build-btn" style="margin:30px auto;display:block">
            Build My Meal
        </button>

        <div id="summary-card">
            <div class="summary-header">
                <div class="summary-item">
                    <h4>Protein</h4>
                    <p id="res-protein">-</p>
                </div>
                <div class="summary-item">
                    <h4>Sauce</h4>
                    <p id="res-sauce">-</p>
                </div>
                <div class="summary-item">
                    <h4>Extras</h4>
                    <p id="res-extras">0</p>
                </div>
            </div>

            <div class="total-calories-box">
                <span>Total Calories</span>
                <strong id="res-calories">0</strong>
            </div>

            <div class="summary-footer">
                <span class="price-text">
                    Estimated Price:
                    <span id="res-price">$0.00</span>
                </span>
                <button class="order-btn" id="order-now-btn" onclick="handleOrderNow(event)">Order Now</button>
            </div>
        </div>

    </section>

    <footer class="footer" style="margin-top:60px">
      <div class="container-footer">
        <div class="footer-section brand">
          <div class="logo">
            <span class="icon"><i class="fa-solid fa-leaf"></i></span>
            <h3>FreshPlate</h3>
          </div>
          <p>Nourishing your body with wholesome, delicious meals that fuel your healthy lifestyle.</p>
        </div>
        <div class="footer-section">
          <h3>Quick Links</h3>
          <ul>
            <li><a href="home.html">Menu</a></li>
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
            <a href="#"><img src="images1/facebook.png"></a>
            <a href="#"><img src="images1/instagram.png"></a>
            <a href="#"><img src="images1/twitter.png"></a>
          </div>
        </div>
      </div>
      <div class="copyright">© 2026 FreshPlate. All rights reserved.</div>
    </footer>

    <script>
        var proteinCards = document.querySelectorAll('#protein-group .card');
        var sauceCards = document.querySelectorAll('#sauce-group .card');
        var extraCards = document.querySelectorAll('#extras-group .card');
        var buildBtn = document.getElementById('build-btn');
        var summaryCard = document.getElementById('summary-card');

        var selectedProtein = null;
        var selectedSauce = null;
        var selectedExtras = [];

        function updateButtonState() {
            if (selectedProtein && selectedSauce) {
                buildBtn.classList.add('active');
            } else {
                buildBtn.classList.remove('active');
            }
        }

        proteinCards.forEach(function(card) {
            card.addEventListener('click', function() {
                proteinCards.forEach(function(c) { c.classList.remove('selected-green'); });
                card.classList.add('selected-green');
                selectedProtein = {
                    name: card.dataset.name,
                    cal: parseInt(card.dataset.cal),
                    price: parseFloat(card.dataset.price)
                };
                updateButtonState();
            });
        });

        sauceCards.forEach(function(card) {
            card.addEventListener('click', function() {
                sauceCards.forEach(function(c) { c.classList.remove('selected-green'); });
                card.classList.add('selected-green');
                selectedSauce = {
                    name: card.dataset.name,
                    cal: parseInt(card.dataset.cal),
                    price: parseFloat(card.dataset.price)
                };
                updateButtonState();
            });
        });

        extraCards.forEach(function(card) {
            card.addEventListener('click', function() {
                var name = card.dataset.name;
                var index = -1;
                for (var i = 0; i < selectedExtras.length; i++) {
                    if (selectedExtras[i].name === name) { index = i; break; }
                }
                if (index > -1) {
                    selectedExtras.splice(index, 1);
                    card.classList.remove('selected-orange');
                } else {
                    selectedExtras.push({
                        name: card.dataset.name,
                        cal: parseInt(card.dataset.cal),
                        price: parseFloat(card.dataset.price)
                    });
                    card.classList.add('selected-orange');
                }
            });
        });

        buildBtn.addEventListener('click', function() {
            if (!selectedProtein || !selectedSauce) return;

            var totalCal = selectedProtein.cal + selectedSauce.cal;
            var totalPrice = selectedProtein.price + selectedSauce.price;
            for (var i = 0; i < selectedExtras.length; i++) {
                totalCal += selectedExtras[i].cal;
                totalPrice += selectedExtras[i].price;
            }

            document.getElementById('res-protein').innerText = selectedProtein.name;
            document.getElementById('res-sauce').innerText = selectedSauce.name;
            document.getElementById('res-extras').innerText = selectedExtras.length;
            document.getElementById('res-calories').innerText = totalCal;
            document.getElementById('res-price').innerText = "$" + totalPrice.toFixed(2);

            summaryCard.style.display = 'block';
            summaryCard.scrollIntoView({ behavior: 'smooth' });
        });

        function handleOrderNow(e) {
            if (e) e.stopPropagation();
            if (!selectedProtein || !selectedSauce) {
                if (typeof Toast !== 'undefined') Toast.warning('Please select protein and sauce first');
                return;
            }

            var totalPrice = selectedProtein.price + selectedSauce.price;
            for (var i = 0; i < selectedExtras.length; i++) {
                totalPrice += selectedExtras[i].price;
            }

            var btn = document.getElementById('order-now-btn');
            btn.disabled = true;
            btn.textContent = 'Adding...';

            var url = window.API_BASE || 'http://localhost/web%20project/api';
            fetch(url + '/products/ensure_meal_plan.php?price=' + totalPrice.toFixed(2))
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data && data.id) {
                        if (typeof window.addToCart === 'function') {
                            window.addToCart(data.id);
                            if (typeof Toast !== 'undefined') Toast.success('Custom meal added to cart!');
                        } else {
                            alert('Custom meal added! Please login to add to cart.');
                        }
                    } else {
                        throw new Error('Failed to create meal product');
                    }
                })
                .catch(function(err) {
                    console.error('Order error:', err);
                    if (typeof Toast !== 'undefined') Toast.error('Failed to add meal. Please login first.');
                })
                .finally(function() {
                    btn.disabled = false;
                    btn.textContent = 'Order Now';
                });
        }
    </script>

</body>
</html>
