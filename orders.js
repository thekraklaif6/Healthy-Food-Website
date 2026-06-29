const ORDERS_API_BASE = "http://localhost/web%20project/api";

const ordersList = document.getElementById("ordersList");
const ordersMessage = document.getElementById("ordersMessage");
const statusFilter = document.getElementById("statusFilter");

function setOrdersMessage(text, color = "#333") {
  ordersMessage.textContent = text;
  ordersMessage.style.color = color;
}

async function loadOrders() {
  setOrdersMessage("Loading orders...");
  try {
    const status = statusFilter?.value || "";
    const query = status ? `?status=${encodeURIComponent(status)}` : "";
    const res = await fetch(`${ORDERS_API_BASE}/orders/list.php${query}`, {
      credentials: "include",
    });
    const data = await res.json();
    if (!res.ok) throw new Error(data.message || "Could not load orders");

    const orders = data.orders || [];
    if (!orders.length) {
      ordersList.innerHTML = "";
      setOrdersMessage("No orders yet.");
      return;
    }

    setOrdersMessage("");
    ordersList.innerHTML = orders
      .map((order) => {
        const lines = (order.items || [])
          .map(
            (item) =>
              `<li>${item.quantity} x ${item.name} ($${item.unit_price})</li>`
          )
          .join("");

        return `
          <article class="order-item">
            <h3>Order #${order.id}</h3>
            <div class="order-meta">
              Status: ${order.status} | Total: $${order.total_price} | Date: ${order.created_at}
            </div>
            <ul class="order-lines">${lines}</ul>
            <div class="order-actions">
              <a class="btn-details" href="order-details.html?id=${order.id}">Details</a>
              <button class="btn-reorder" data-order-id="${order.id}" type="button">Reorder</button>
            </div>
          </article>
        `;
      })
      .join("");
    bindReorderButtons();
  } catch (err) {
    setOrdersMessage(err.message + " (Please login first)", "crimson");
    ordersList.innerHTML = "";
  }
}

function bindReorderButtons() {
  document.querySelectorAll(".btn-reorder").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const orderId = Number(btn.dataset.orderId);
      try {
        const res = await fetch(`${ORDERS_API_BASE}/orders/reorder.php`, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          credentials: "include",
          body: JSON.stringify({ orderId }),
        });
        const data = await res.json();
        if (!res.ok) throw new Error(data.message || "Reorder failed");
        setOrdersMessage("Items added back to cart successfully.", "green");
      } catch (err) {
        setOrdersMessage(err.message, "crimson");
      }
    });
  });
}

statusFilter?.addEventListener("change", loadOrders);
loadOrders();
