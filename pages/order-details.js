const ORDER_DETAILS_API_BASE = "http://localhost/web%20project/api";

const detailsMessage = document.getElementById("detailsMessage");
const detailsContent = document.getElementById("detailsContent");

function setDetailsMessage(text, color = "#333") {
  detailsMessage.textContent = text;
  detailsMessage.style.color = color;
}

function getOrderIdFromUrl() {
  const params = new URLSearchParams(window.location.search);
  return Number(params.get("id") || 0);
}

async function loadOrderDetails() {
  const orderId = getOrderIdFromUrl();
  if (!orderId) {
    setDetailsMessage("Invalid order id.", "crimson");
    return;
  }

  setDetailsMessage("Loading order details...");
  try {
    const res = await fetch(`${ORDER_DETAILS_API_BASE}/orders/show.php?id=${orderId}`, {
      credentials: "include",
    });
    const data = await res.json();
    if (!res.ok) throw new Error(data.message || "Failed to load order details");

    const lines = (data.items || [])
      .map((item) => `<li>${item.quantity} x ${item.name} ($${item.unit_price})</li>`)
      .join("");

    detailsContent.innerHTML = `
      <article class="order-item">
        <h3>Order #${data.id}</h3>
        <div class="order-meta">
          Status: ${data.status} | Total: $${data.total_price} | Date: ${data.created_at}
        </div>
        <ul class="order-lines">${lines}</ul>
      </article>
    `;
    setDetailsMessage("");
  } catch (err) {
    setDetailsMessage(err.message + " (Please login first)", "crimson");
    detailsContent.innerHTML = "";
  }
}

loadOrderDetails();
