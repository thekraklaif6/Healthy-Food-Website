var API_BASE = window.API_BASE || "http://localhost/web%20project/api";

const loginForm = document.getElementById("loginForm");
const loginMessage = document.getElementById("loginMessage");

if (loginForm) {
  loginForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const email = document.getElementById("email")?.value.trim();
    const password = document.getElementById("password")?.value;

    loginMessage.textContent = "Signing in...";
    loginMessage.style.color = "#444";

    try {
      const res = await fetch(`${API_BASE}/auth/login.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        credentials: "include",
        body: JSON.stringify({ email, password }),
      });

      const data = await res.json();
      if (!res.ok) throw new Error(data.message || "Login failed");

      loginMessage.textContent = `Welcome back, ${data.name}!`;
      loginMessage.style.color = "green";
      setTimeout(() => {
        window.location.href = "home.html";
      }, 700);
    } catch (err) {
      loginMessage.textContent = err.message;
      loginMessage.style.color = "crimson";
    }
  });
}
