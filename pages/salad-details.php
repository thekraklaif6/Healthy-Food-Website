<?php

include "db.php";

$id = (int)$_GET['id'];

$sql = "SELECT * FROM products WHERE id = $id";
$result = mysqli_query($conn, $sql);
$meal = mysqli_fetch_assoc($result);

if (!$meal) {
    die("Product not found");
}
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
<a href="salad.php" class="Back_button"> 
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
        <div class="size-box2"> 
            <h3>Ingredients</h3>
            <div class="size-options2">
              <button class="size-btn2">Cherry tomatoes</button>
              <button class="size-btn2">Black olives</button>
              <button class="size-btn2">creamy mozzarella</button>
            </div>
        </div>
        <div class="size-box">
          <h3>Size</h3>
            <div class="size-options">
              <button class="size-btn active" data-extra="0">Regular</button>
              <button class="size-btn" data-extra="3">Medium</button>
              <button class="size-btn" data-extra="5">Large</button>
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
    <h1 id="price" data-base-price="<?php echo $meal['price']; ?>">
    $<?php echo $meal['price']; ?>
</h1>
    <button class="add-btn" onclick="addToCart(<?php echo $meal['id']; ?>, quantity, basePrice + sizeExtra)">Add to Cart</button>
</div>

    </div>
    <!-- BOX 1 -->
    <div class="meal-box meal-image-box">
        <img src="<?php echo $meal['image_url']; ?>" onerror="this.src='images/default-food.png'">
    </div>

</div>
</section>

<script>
let quantity = 1;

const quantityText = document.getElementById("quantity");
const priceElement = document.getElementById("price");

const basePrice = parseFloat(priceElement.dataset.basePrice);

const plusBtn = document.querySelector(".plus");
const minusBtn = document.querySelector(".minus");
const sizeButtons = document.querySelectorAll(".size-btn");

let sizeExtra = 0;

function updatePrice() {
    const finalPrice = (basePrice + sizeExtra) * quantity;
    priceElement.textContent = "$" + finalPrice.toFixed(2);
}

plusBtn.onclick = function () {
    quantity++;
    quantityText.textContent = quantity;
    updatePrice();
};

minusBtn.onclick = function () {
    if (quantity > 1) {
        quantity--;
        quantityText.textContent = quantity;
        updatePrice();
    }
};

sizeButtons.forEach(button => {
    button.addEventListener("click", function () {
        sizeButtons.forEach(btn => btn.classList.remove("active"));
        this.classList.add("active");

        sizeExtra = parseFloat(this.dataset.extra);
        updatePrice();
    });
});
</script>

</body>
</html>
