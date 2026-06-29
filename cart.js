var CART_API_BASE = window.API_BASE || "http://localhost/web%20project/api";

const cartList = document.getElementById("cartList");
const cartTotal = document.getElementById("cartTotal");
const cartMessage = document.getElementById("cartMessage");
const refreshCartBtn = document.getElementById("refreshCartBtn");
const checkoutBtn = document.getElementById("checkoutBtn");

function setCartMessage(text, color = "#333") {
  cartMessage.textContent = text;
  cartMessage.style.color = color;
}

async function loadCart() {
  setCartMessage("Loading cart...");
  try {
    const res = await fetch(`${CART_API_BASE}/cart/list.php`, {
      credentials: "include",
    });
    const data = await res.json();
    if (!res.ok) throw new Error(data.message || "Failed to load cart");

    renderCart(data.items || [], data.total || "0.00");
    setCartMessage((data.items || []).length ? "" : "Your cart is empty.");
  } catch (err) {
    setCartMessage(err.message + " (Please login first)", "crimson");
    renderCart([], "0.00");
  }
}

function renderCart(items, total) {
  cartTotal.textContent = total;
  if (!items.length) {
    cartList.innerHTML = "";
    return;
  }

  cartList.innerHTML = items
    .map(
      (item) => `
      <div class="cart-item">
        <div>
          <strong>${item.name}</strong>
          <small>$${parseFloat(item.unit_price).toFixed(2)} each</small>
        </div>
        <div>$${item.line_total}</div>
        <div class="cart-actions">
          <input type="number" min="1" value="${item.quantity}" id="qty-${item.id}">
          <button class="btn-update" data-id="${item.id}">Update</button>
        </div>
        <button class="btn-remove" data-id="${item.id}">Remove</button>
      </div>
    `
    )
    .join("");

  bindCartButtons();
}

function bindCartButtons() {
  document.querySelectorAll(".btn-update").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const id = Number(btn.dataset.id);
      const qty = Number(document.getElementById(`qty-${id}`)?.value || 1);
      await updateCartItem(id, qty);
    });
  });

  document.querySelectorAll(".btn-remove").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const id = Number(btn.dataset.id);
      await removeCartItem(id);
    });
  });
}

async function updateCartItem(itemId, quantity) {
  try {
    const res = await fetch(`${CART_API_BASE}/cart/update.php`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      credentials: "include",
      body: JSON.stringify({ itemId, quantity }),
    });
    const data = await res.json();
    if (!res.ok) throw new Error(data.message || "Update failed");
    setCartMessage("Item updated.", "green");
    await loadCart();
    if (typeof updateCartUI === "function") updateCartUI();
  } catch (err) {
    setCartMessage(err.message, "crimson");
  }
}

async function removeCartItem(itemId) {
  try {
    const res = await fetch(`${CART_API_BASE}/cart/remove.php`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      credentials: "include",
      body: JSON.stringify({ itemId }),
    });
    const data = await res.json();
    if (!res.ok) throw new Error(data.message || "Delete failed");
    setCartMessage("Item removed.", "green");
    await loadCart();
    if (typeof updateCartUI === "function") updateCartUI();
  } catch (err) {
    setCartMessage(err.message, "crimson");
  }
}

refreshCartBtn?.addEventListener("click", loadCart);

checkoutBtn?.addEventListener("click", async () => {
  try {
    const res = await fetch(`${CART_API_BASE}/orders/checkout.php`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      credentials: "include",
    });
    const data = await res.json();
    if (!res.ok) throw new Error(data.message || "Checkout failed");
    setCartMessage(
      `Order #${data.orderId} placed successfully. Total: $${data.total}`,
      "green"
    );
    await loadCart();
    if (typeof updateCartUI === "function") updateCartUI();
  } catch (err) {
    setCartMessage(err.message, "crimson");
  }
});

loadCart();
