const SWEET_API_BASE = "http://localhost/web%20project/api";

const dessertsContainer = document.getElementById("dessertsContainer");
const sweetApiMessage = document.getElementById("sweetApiMessage");

async function loadSweets() {
  if (!dessertsContainer) return;

  try {
    const res = await fetch(`${SWEET_API_BASE}/products/list.php?category=sweets`, {
      credentials: "include",
    });
    const data = await res.json();
    if (!res.ok) throw new Error(data.message || "Failed to load desserts");

    if (!Array.isArray(data.items) || data.items.length === 0) {
      sweetApiMessage.textContent = "No desserts found in database.";
      return;
    }

    dessertsContainer.innerHTML = data.items
      .map((item) => {
        const img = item.image_url || "images1/sweet1.png";
        const protein = item.protein ?? 0;
        const calories = item.calories ?? 0;
        return `
          <div class="dessert-card">
            <img src="${img}" alt="${item.name}">
            <h3>${item.name}</h3>
            <p>${calories} kcal | ${protein}g protein</p>
            <button type="button" class="sweet-add-btn" data-id="${item.id}">Add To Cart - $${item.price}</button>
          </div>
        `;
      })
      .join("");

    sweetApiMessage.textContent = "";
    bindAddToCartButtons();
  } catch (err) {
    sweetApiMessage.textContent = err.message;
    sweetApiMessage.style.color = "crimson";
  }
}

function bindAddToCartButtons() {
  document.querySelectorAll(".sweet-add-btn").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const productId = Number(btn.dataset.id);
      try {
        const res = await fetch(`${SWEET_API_BASE}/cart/add.php`, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          credentials: "include",
          body: JSON.stringify({ productId, quantity: 1 }),
        });
        const data = await res.json();
        if (!res.ok) throw new Error(data.message || "Could not add to cart");
        sweetApiMessage.textContent = "Added to cart successfully.";
        sweetApiMessage.style.color = "green";
      } catch (err) {
        sweetApiMessage.textContent = err.message + " (Please login first)";
        sweetApiMessage.style.color = "crimson";
      }
    });
  });
}

loadSweets();
