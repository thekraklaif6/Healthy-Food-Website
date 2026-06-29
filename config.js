
window.API_BASE = window.API_BASE || "http://localhost/web%20project/api";
var API_BASE = window.API_BASE;

(async function injectSideCart() {
  if (document.getElementById("side-cart-style")) return;

  var css = document.createElement("link");
  css.id = "side-cart-style";
  css.rel = "stylesheet";
  css.href = "side-cart.css";
  document.head.appendChild(css);

  if (document.getElementById("side-cart")) return;

  await new Promise(resolve => {
    if (document.readyState === "loading") {
      document.addEventListener("DOMContentLoaded", resolve);
    } else {
      resolve();
    }
  });

  if (document.getElementById("side-cart")) return;

  var html =
    '<div id="cart-overlay" onclick="toggleCart()"></div>' +
    '<div id="side-cart">' +
      '<div id="side-cart-header">' +
        '<h2>My Basket</h2>' +
        '<button id="side-cart-close" onclick="toggleCart()">&times;</button>' +
      '</div>' +
      '<div id="cart-items-list">' +
        '<p class="empty-cart-msg">Your basket is empty</p>' +
      '</div>' +
      '<div id="side-cart-footer">' +
        '<div class="cart-summary">' +
          '<h3>Total:</h3>' +
          '<h3 id="cart-total-price">$0.00</h3>' +
        '</div>' +
        '<a href="cart.html" style="display:block;text-align:center;margin-bottom:8px;color:#2ecc71;text-decoration:none;font-weight:600;font-size:0.9rem">View Full Cart →</a>' +
        '<button id="checkout-btn-side" onclick="checkoutCart()">Checkout Now</button>' +
      '</div>' +
    '</div>';
  document.body.insertAdjacentHTML("beforeend", html);

  var cartIconFound = false;
  var imgs = document.querySelectorAll('img[src*="shopping-cart.png"]');
  imgs.forEach(function (img) {
    if (img.closest("#side-cart")) return;
    var parent = img.parentElement;
    if (parent && parent.id === "cart-icon-wrap") return;
    cartIconFound = true;
    var wrapper = document.createElement("div");
    wrapper.id = "cart-icon-wrap";
    wrapper.onclick = toggleCart;
    wrapper.style.cssText = "position:relative;cursor:pointer;display:inline-block;";
    parent.insertBefore(wrapper, img);
    wrapper.appendChild(img);
    var badge = document.createElement("span");
    badge.id = "cart-count";
    badge.style.cssText = "display:none;position:absolute;top:-5px;right:-5px;background:#2ecc71;color:white;border-radius:50%;padding:2px 6px;font-size:12px;font-weight:bold;";
    wrapper.appendChild(badge);
  });

  if (!cartIconFound && !document.getElementById("cart-count")) {
    var floatBtn = document.createElement("div");
    floatBtn.id = "fp-cart-float";
    floatBtn.innerHTML = "🛒";
    floatBtn.onclick = toggleCart;
    Object.assign(floatBtn.style, {
      position: "fixed", bottom: "24px", right: "24px", zIndex: "9999",
      width: "56px", height: "56px", borderRadius: "50%",
      background: "#2ecc71", color: "#fff", fontSize: "24px",
      display: "flex", alignItems: "center", justifyContent: "center",
      cursor: "pointer", boxShadow: "0 4px 20px rgba(46,204,113,0.4)",
      transition: "transform 0.2s", border: "none"
    });
    floatBtn.onmouseenter = function() { this.style.transform = "scale(1.1)"; };
    floatBtn.onmouseleave = function() { this.style.transform = "scale(1)"; };
    document.body.appendChild(floatBtn);
  }

  updateCartUI();
})();

function toggleCart() {
  var cart = document.getElementById("side-cart");
  var overlay = document.getElementById("cart-overlay");
  if (!cart) return;
  var isOpen = cart.classList.contains("open");
  if (isOpen) {
    cart.classList.remove("open");
    if (overlay) overlay.style.display = "none";
  } else {
    cart.classList.add("open");
    if (overlay) overlay.style.display = "block";
    updateCartUI();
  }
}

function updateCartUI() {
  fetch(API_BASE + "/cart/list.php", { credentials: "include" })
    .then(function (r) { return r.json(); })
    .then(function (data) {
      var list = document.getElementById("cart-items-list");
      var totalDisplay = document.getElementById("cart-total-price");
      var badge = document.getElementById("cart-count");
      if (totalDisplay) totalDisplay.innerText = "$" + (data.total || "0.00");
      if (!list) return;
      if (!data.items || !data.items.length) {
        list.innerHTML = '<p class="empty-cart-msg">Your basket is empty</p>';
        if (badge) { badge.innerText = "0"; badge.style.display = "none"; }
        return;
      }
      var totalQty = 0;
      list.innerHTML = "";
      data.items.forEach(function (item) {
        totalQty += parseInt(item.quantity) || 0;
        var imgSrc = item.image_url || "images/default-food.png";
        var p = parseFloat(item.unit_price).toFixed(2);
        var lt = parseFloat(item.line_total).toFixed(2);
        list.innerHTML +=
          '<div class="cart-item-row">' +
          '<img src="' + imgSrc + '" onerror="this.src=\'images/default-food.png\'" alt="' + item.name + '">' +
          '<div class="cart-item-info">' +
          '<span class="cart-item-name">' + item.name + '</span>' +
          '<span class="cart-item-detail">Qty: ' + item.quantity + ' x $' + p + '</span>' +
          '</div>' +
          '<span class="cart-item-total">$' + lt + '</span>' +
          '</div>';
      });
      if (badge) { badge.innerText = totalQty; badge.style.display = totalQty > 0 ? "inline" : "none"; }
    })
    .catch(function (err) { console.error("Cart error:", err); });
}

var cartRequestInProgress = false;

function addToCart(id, qty, unitPrice) {
  if (cartRequestInProgress) return;
  cartRequestInProgress = true;
  var body = { productId: parseInt(id), quantity: qty || 1 };
  if (unitPrice !== undefined && unitPrice !== null) {
    body.unitPrice = parseFloat(unitPrice);
  }
  fetch(API_BASE + "/cart/add.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    credentials: "include",
    body: JSON.stringify(body),
  })
    .then(function (r) {
      if (r.status === 401) {
        Toast.warning("Please login first to add items to cart", 3000);
        setTimeout(function () { window.location.href = "sign_in.html"; }, 1500);
        cartRequestInProgress = false;
        return;
      }
      return r.json();
    })
    .then(function (data) {
      if (data && data.message && data.message.indexOf("successfully") !== -1) {
        updateCartUI();
        var cart = document.getElementById("side-cart");
        if (cart && !cart.classList.contains("open")) toggleCart();
      }
    })
    .catch(function (err) { console.error("Add to cart error:", err); if (err instanceof SyntaxError && err.message.indexOf("Unexpected token") !== -1) { fetch(API_BASE + "/cart/add.php", { method: "POST", headers: { "Content-Type": "application/json" }, credentials: "include", body: JSON.stringify({ productId: 1, quantity: 1 }) }).then(function(r) { return r.text(); }).then(function(t) { console.error("Raw response:", t); }); } })
    .finally(function () { cartRequestInProgress = false; });
}

function checkoutCart() {
  fetch(API_BASE + "/orders/checkout.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    credentials: "include",
  })
    .then(function (r) { return r.json(); })
    .then(function (data) {
      if (data.orderId) {
        alert("Order #" + data.orderId + " placed successfully! Total: $" + data.total);
        toggleCart();
        updateCartUI();
      }
    })
    .catch(function (err) { alert("Checkout failed: " + (err.message || "Please login first")); });
}

async function apiFetch(path, options = {}) {
  const url = `${API_BASE}${path}`;
  const defaults = {
    credentials: "include",
    headers: { "Content-Type": "application/json" },
  };
  const res = await fetch(url, { ...defaults, ...options });
  const data = await res.json().catch(() => ({}));
  if (!res.ok) {
    const err = new Error(data.message || `Request failed (${res.status})`);
    err.status = res.status;
    throw err;
  }
  return data;
}

const Toast = (() => {
  let container = null;

  function getContainer() {
    if (container) return container;
    container = document.createElement("div");
    container.id = "fp-toast-container";
    Object.assign(container.style, {
      position:      "fixed",
      bottom:        "24px",
      left:          "50%",
      transform:     "translateX(-50%)",
      zIndex:        "99999",
      display:       "flex",
      flexDirection: "column",
      alignItems:    "center",
      gap:           "10px",
      pointerEvents: "none",
    });
    document.body.appendChild(container);
    return container;
  }

  function show(message, type = "info", duration = 3000) {
    const colors = {
      success: { bg: "#1f7a57", icon: "✓" },
      error:   { bg: "#c0392b", icon: "✕" },
      warning: { bg: "#e67e22", icon: "⚠" },
      info:    { bg: "#2c3e50", icon: "ℹ" },
    };
    const { bg, icon } = colors[type] ?? colors.info;

    const toast = document.createElement("div");
    toast.style.cssText = `
      display:inline-flex;align-items:center;gap:10px;
      background:${bg};color:#fff;
      padding:13px 20px;border-radius:50px;
      font-family:'Poppins',sans-serif;font-size:0.9rem;font-weight:500;
      box-shadow:0 8px 24px rgba(0,0,0,0.2);
      pointer-events:auto;user-select:none;
      opacity:0;transform:translateY(16px);
      transition:opacity 0.3s ease,transform 0.3s ease;
      max-width:90vw;text-align:center;
    `;
    toast.innerHTML = `<span style="font-size:1rem">${icon}</span><span>${message}</span>`;

    getContainer().appendChild(toast);

    requestAnimationFrame(() => {
      requestAnimationFrame(() => {
        toast.style.opacity = "1";
        toast.style.transform = "translateY(0)";
      });
    });

    const hide = () => {
      toast.style.opacity = "0";
      toast.style.transform = "translateY(16px)";
      toast.addEventListener("transitionend", () => toast.remove(), { once: true });
    };

    const timer = setTimeout(hide, duration);
    toast.addEventListener("click", () => { clearTimeout(timer); hide(); });

    return toast;
  }

  return {
    success: (msg, ms = 3000) => show(msg, "success", ms),
    error:   (msg, ms = 4000) => show(msg, "error",   ms),
    warning: (msg, ms = 3500) => show(msg, "warning", ms),
    info:    (msg, ms = 3000) => show(msg, "info",    ms),
  };
})();

function buildPaginationHTML(pagination, onPageClick) {
  if (!pagination || pagination.total_pages <= 1) return "";

  const { page, total_pages } = pagination;
  let html = `<div class="fp-pagination">`;

  if (pagination.has_prev) {
    html += `<button class="fp-page-btn" data-page="${page - 1}">← Prev</button>`;
  }

  for (let i = 1; i <= total_pages; i++) {
    html += `<button class="fp-page-btn ${i === page ? "active" : ""}" data-page="${i}">${i}</button>`;
  }

  if (pagination.has_next) {
    html += `<button class="fp-page-btn" data-page="${page + 1}">Next →</button>`;
  }

  html += `</div>`;

  setTimeout(() => {
    document.querySelectorAll(".fp-page-btn").forEach((btn) => {
      btn.addEventListener("click", () => onPageClick(Number(btn.dataset.page)));
    });
  }, 0);

  return html;
}

(function injectPaginationCSS() {
  if (document.getElementById("fp-pagination-style")) return;
  const style = document.createElement("style");
  style.id = "fp-pagination-style";
  style.textContent = `
    .fp-pagination {
      display: flex;
      justify-content: center;
      gap: 8px;
      margin: 32px 0;
      flex-wrap: wrap;
    }
    .fp-page-btn {
      padding: 8px 16px;
      border: 2px solid #2ecc71;
      border-radius: 50px;
      background: transparent;
      color: #2ecc71;
      font-weight: 600;
      cursor: pointer;
      font-size: 0.9rem;
      transition: 0.2s;
    }
    .fp-page-btn:hover,
    .fp-page-btn.active {
      background: #2ecc71;
      color: #fff;
    }
  `;
  document.head.appendChild(style);
})();