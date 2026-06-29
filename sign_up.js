var API_BASE = window.API_BASE || "http://localhost/web%20project/api";

const signupForm = document.getElementById("signupForm");
const signupMessage = document.getElementById("signupMessage");

if (signupForm) {
  signupForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const name = document.getElementById("name")?.value.trim();
    const email = document.getElementById("email")?.value.trim();
    const password = document.getElementById("password")?.value;

    signupMessage.textContent = "Creating account...";
    signupMessage.style.color = "#444";

    try {
      const res = await fetch(`${API_BASE}/auth/register.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        credentials: "include",
        body: JSON.stringify({ name, email, password }),
      });

      const data = await res.json();
      if (!res.ok) throw new Error(data.message || "Sign up failed");

      signupMessage.textContent = "Account created successfully. Redirecting...";
      signupMessage.style.color = "green";
      setTimeout(() => {
        window.location.href = "home.html";
      }, 800);
    } catch (err) {
      signupMessage.textContent = err.message;
      signupMessage.style.color = "crimson";
    }
  });
}
