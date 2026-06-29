let productCacheKey = '';
let productLoading = false;
let currentProductId = null;
let productQuantity = 1;
let productSizeExtra = 0;
let productBasePrice = 0;

function renderProduct(item) {
    productBasePrice = parseFloat(item.price);
    productQuantity = 1;
    productSizeExtra = 0;

    return '<div class="product-card-detail">' +
        '<div class="product-image-wrap">' +
            '<img src="' + (item.image_url || 'images/default-food.png') + '" alt="' + item.name + '" onerror="this.src=\'images/default-food.png\'">' +
        '</div>' +
        '<div class="product-info-wrap">' +
            '<h1 class="product-name">' + item.name + '</h1>' +
            '<span class="product-badge"><i class="fas fa-tag"></i> ' + (item.category_slug || 'drinks') + '</span>' +
            '<p class="product-description">' + (item.description || 'No description available.') + '</p>' +
            '<div class="product-nutrition">' +
                '<span class="product-calories"><i class="fas fa-fire"></i> ' + (item.calories || '0') + ' cal</span>' +
                '<span class="product-protein"><i class="fas fa-dumbbell"></i> ' + (item.protein || '0') + 'g protein</span>' +
            '</div>' +
            '<div class="size-box">' +
                '<h3>Size</h3>' +
                '<div class="size-options">' +
                    '<button class="size-btn active" data-extra="0">Regular</button>' +
                    '<button class="size-btn" data-extra="3">Medium</button>' +
                    '<button class="size-btn" data-extra="5">Large</button>' +
                '</div>' +
            '</div>' +
            '<div class="quantity-box">' +
                '<h3>Quantity</h3>' +
                '<div class="quantity-control">' +
                    '<button class="qty-btn minus" onclick="decQty()">&minus;</button>' +
                    '<span id="product-qty">1</span>' +
                    '<button class="qty-btn plus" onclick="incQty()">+</button>' +
                '</div>' +
            '</div>' +
            '<div class="product-price-section">' +
                '<span class="product-price" id="product-price">$' + parseFloat(item.price).toFixed(2) + '</span>' +
                '<button class="product-add-btn" onclick="addToCart(' + item.id + ', productQuantity, productBasePrice + productSizeExtra)"><i class="fas fa-cart-plus"></i> Add to Cart</button>' +
            '</div>' +
            '<button class="product-back-btn" onclick="history.back()">&larr; Back</button>' +
        '</div>' +
    '</div>';
}

function updateProductPrice() {
    var el = document.getElementById('product-price');
    if (el) {
        var total = (productBasePrice + productSizeExtra) * productQuantity;
        el.textContent = '$' + total.toFixed(2);
    }
}

function incQty() {
    productQuantity++;
    var qtyEl = document.getElementById('product-qty');
    if (qtyEl) qtyEl.textContent = productQuantity;
    updateProductPrice();
}

function decQty() {
    if (productQuantity > 1) {
        productQuantity--;
        var qtyEl = document.getElementById('product-qty');
        if (qtyEl) qtyEl.textContent = productQuantity;
        updateProductPrice();
    }
}

function bindSizeButtons() {
    document.querySelectorAll('.size-btn').forEach(function (btn) {
        btn.onclick = function () {
            document.querySelectorAll('.size-btn').forEach(function (b) { b.classList.remove('active'); });
            this.classList.add('active');
            productSizeExtra = parseFloat(this.dataset.extra) || 0;
            updateProductPrice();
        };
    });
}

async function loadProduct() {
    if (productLoading || !currentProductId) return;
    productLoading = true;

    try {
        const res = await fetch(API_BASE + '/products/show.php?id=' + currentProductId);
        const item = await res.json();

        if (!item || item.message) {
            document.getElementById('productDetail').innerHTML = '<p class="product-error">Product not found.</p>';
            return;
        }

        const cacheKey = JSON.stringify(item);
        if (cacheKey === productCacheKey) return;
        productCacheKey = cacheKey;

        document.getElementById('productDetail').innerHTML = renderProduct(item);
        bindSizeButtons();
    } catch (err) {
        console.error('Product load error:', err);
    } finally {
        productLoading = false;
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const params = new URLSearchParams(window.location.search);
    currentProductId = params.get('id');

    if (!currentProductId) {
        document.getElementById('productDetail').innerHTML = '<p class="product-error">No product ID specified.</p>';
        return;
    }

    loadProduct();
    setInterval(loadProduct, 5000);
});
