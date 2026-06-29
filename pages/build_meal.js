var API_BASE = window.API_BASE || "http://localhost/web%20project/api";

const proteinCards = document.querySelectorAll("#protein-group .card");
const sauceCards = document.querySelectorAll("#sauce-group .card");
const extraCards = document.querySelectorAll("#extras-group .card");
const buildBtn = document.getElementById("build-btn");
const summaryCard = document.getElementById("summary-card");
const orderBtn = document.getElementById("orderBtn");
const buildMealMessage = document.getElementById("buildMealMessage");

let selectedProtein = null;
let selectedSauce = null;
let selectedExtras = [];
let lastSummary = null;

function setBuildMessage(text, color = "#333") {
  if (!buildMealMessage) return;
  buildMealMessage.textContent = text;
  buildMealMessage.style.color = color;
}

function updateButtonState() {
  if (selectedProtein && selectedSauce) {
    buildBtn.classList.add("active");
  } else {
    buildBtn.classList.remove("active");
  }
}

proteinCards.forEach((card) => {
  card.addEventListener("click", () => {
    proteinCards.forEach((c) => c.classList.remove("selected-green"));
    card.classList.add("selected-green");
    selectedProtein = {
      name: card.dataset.name,
      cal: Number(card.dataset.cal),
      price: Number(card.dataset.price),
    };
    updateButtonState();
  });
});

sauceCards.forEach((card) => {
  card.addEventListener("click", () => {
    sauceCards.forEach((c) => c.classList.remove("selected-green"));
    card.classList.add("selected-green");
    selectedSauce = {
      name: card.dataset.name,
      cal: Number(card.dataset.cal),
      price: Number(card.dataset.price),
    };
    updateButtonState();
  });
});

extraCards.forEach((card) => {
  card.addEventListener("click", () => {
    const idx = selectedExtras.findIndex((e) => e.name === card.dataset.name);
    if (idx > -1) {
      selectedExtras.splice(idx, 1);
      card.classList.remove("selected-orange");
    } else {
      selectedExtras.push({
        name: card.dataset.name,
        cal: Number(card.dataset.cal),
        price: Number(card.dataset.price),
      });
      card.classList.add("selected-orange");
    }
  });
});

buildBtn?.addEventListener("click", async () => {
  if (!selectedProtein || !selectedSauce) {
    setBuildMessage("Please choose protein and sauce first.", "crimson");
    return;
  }

  setBuildMessage("Calculating your custom meal...");
  try {
    const res = await fetch(`${API_BASE}/custom-meals/calculate.php`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      credentials: "include",
      body: JSON.stringify({
        protein: selectedProtein,
        sauce: selectedSauce,
        extras: selectedExtras,
      }),
    });
    const data = await res.json();
    if (!res.ok) throw new Error(data.message || "Failed to build meal");

    lastSummary = data;
    document.getElementById("res-protein").innerText = selectedProtein.name;
    document.getElementById("res-sauce").innerText = selectedSauce.name;
    document.getElementById("res-extras").innerText = data.extrasCount;
    document.getElementById("res-calories").innerText = data.calories;
    document.getElementById("res-price").innerText = `$${data.price}`;

    summaryCard.style.display = "block";
    summaryCard.scrollIntoView({ behavior: "smooth" });
    setBuildMessage("Custom meal saved successfully.", "green");
  } catch (err) {
    setBuildMessage(err.message, "crimson");
  }
});

orderBtn?.addEventListener("click", async () => {
  if (!lastSummary) {
    setBuildMessage("Build your meal first.", "crimson");
    return;
  }
  try {
    const res = await fetch(`${API_BASE}/cart/add.php`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      credentials: "include",
      body: JSON.stringify({ productId: 1, quantity: 1 }),
    });
    const data = await res.json();
    if (!res.ok) throw new Error(data.message || "Could not add to cart");
    setBuildMessage("Added to cart.", "green");
  } catch (err) {
    setBuildMessage(err.message + " (Please login first)", "crimson");
  }
});
