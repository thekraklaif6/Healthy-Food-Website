var API_BASE = window.API_BASE || "http://localhost/web%20project/api";

const contactForm = document.getElementById("contactForm");
const contactFormMessage = document.getElementById("contactFormMessage");

if (contactForm) {
  contactForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const name = document.getElementById("contactName")?.value.trim();
    const email = document.getElementById("contactEmail")?.value.trim();
    const message = document.getElementById("contactMessage")?.value.trim();

    contactFormMessage.textContent = "Sending...";
    contactFormMessage.style.color = "#444";

    try {
      const res = await fetch(`${API_BASE}/contact/send.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        credentials: "include",
        body: JSON.stringify({ name, email, message }),
      });

      const data = await res.json();
      if (!res.ok) throw new Error(data.message || "Failed to send message");

      contactFormMessage.textContent = "Message sent successfully.";
      contactFormMessage.style.color = "green";
      contactForm.reset();
    } catch (err) {
      contactFormMessage.textContent = err.message;
      contactFormMessage.style.color = "crimson";
    }
  });
}
