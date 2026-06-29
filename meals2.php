<?php
include "db.php";

$id = (int)$_GET['id'];
$sql = "SELECT * FROM products WHERE id = $id";
$result = mysqli_query($conn, $sql);
$meal = mysqli_fetch_assoc($result);

if (!$meal) {
    die("Product not found");
}

$isPizza = stripos($meal['name'], 'pizza') !== false;
$isBurger = stripos($meal['name'], 'burger') !== false;
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $meal['name']; ?></title>
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
</header>

<a href="meals.php" class="Back_button"> 
    <i class="fa-solid fa-arrow-left-long"></i>
    <span>Back</span>
</a>

<section class="meal-section">
<div class="meal-container">

    <div class="meal-box meal-details-box">

        <h2><?php echo $meal['name']; ?></h2>

        <div class="nutrition">
            <span><?php echo $meal['calories']; ?> kcal</span>
            <span><?php echo $meal['protein']; ?>g protein</span>
            <span>42g carbs</span>
            <span>12g fats</span>
        </div>

        <?php if ($isPizza) { ?>

            <div class="size-box2"> 
                <h3>Signature Healthy Pizzas</h3>
                <div class="size-options2">
                    <button class="size-btn2 active" onclick="changeImage('images1/pizza1.png')">Margherita</button>
                    <button class="size-btn2" onclick="changeImage('images1/pizza2.png')">Grilled Chicken</button>
                    <button class="size-btn2" onclick="changeImage('images1/pizza3.png')">Vegetable</button>
                </div>
            </div>

        <?php } elseif ($isBurger) { ?>

            <div class="size-box2"> 
                <h3>Signature Healthy Burger</h3>
                <div class="size-options2">
                    <button class="size-btn2 active" onclick="changeImage('images1/br1.png')">Mushroom</button>
                    <button class="size-btn2" onclick="changeImage('images1/br2.png')">Classic Beef</button>
                    <button class="size-btn2" onclick="changeImage('images1/br3.png')">Grilled Chicken</button>
                </div>
            </div>

        <?php } else { ?>

            <div class="size-box2">
                <h3>Description</h3>
                <p><?php echo $meal['description']; ?></p>
            </div>

        <?php } ?>

        <div class="size-box">
            <h3>Size</h3>
            <div class="size-options">
                <button class="size-btn active">Regular</button>
                <button class="size-btn">Medium</button>
                <button class="size-btn">Large</button>
            </div>
        </div>

        <div class="quantity-box">
            <h3>Quantity</h3>
            <div class="quantity-control">
                <button class="qty-btn minus">−</button>
                <span id="quantity">1</span>
                <button class="qty-btn plus">+</button>
            </div>
        </div>

        <div class="price-cart">
            <span class="price">$<?php echo $meal['price']; ?></span>
            <button class="add-btn" onclick="addToCart(<?php echo $meal['id']; ?>, quantity, currentPrice)">Add to Cart</button>
        </div>

    </div>

    <div class="meal-box meal-image-box">
        <img id="productImage" src="<?php echo $meal['image_url']; ?>" onerror="this.src='images/default-food.png'">
    </div>

</div>
</section>

<script>
let quantity = 1;
let basePrice = <?php echo $meal['price']; ?>;
let currentPrice = basePrice;

const quantityText = document.getElementById("quantity");
const priceText = document.querySelector(".price");

function updatePrice() {
    let total = currentPrice * quantity;
    priceText.textContent = "$" + total.toFixed(2);
}

document.querySelector(".plus").onclick = function () {
    quantity++;
    quantityText.textContent = quantity;
    updatePrice();
};

document.querySelector(".minus").onclick = function () {
    if (quantity > 1) {
        quantity--;
        quantityText.textContent = quantity;
        updatePrice();
    }
};

const sizeButtons = document.querySelectorAll(".size-btn");

sizeButtons.forEach(button => {
    button.addEventListener("click", function () {
        sizeButtons.forEach(btn => btn.classList.remove("active"));
        this.classList.add("active");

        let size = this.textContent;

        if (size === "Regular") {
            currentPrice = basePrice;
        } else if (size === "Medium") {
            currentPrice = basePrice + 4;
        } else if (size === "Large") {
            currentPrice = basePrice + 8;
        }

        updatePrice();
    });
});

function changeImage(imagePath) {
    document.getElementById("productImage").src = imagePath;
}

let typeButtons = document.querySelectorAll(".size-btn2");

typeButtons.forEach(function(button){
    button.addEventListener("click", function(){
        typeButtons.forEach(function(btn){
            btn.classList.remove("active");
        });
        this.classList.add("active");
    });
});

updatePrice();
</script>
</body>
</html>
