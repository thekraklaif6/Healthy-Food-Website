document.addEventListener('DOMContentLoaded', function () {
  var hamburger = document.querySelector(".hamburger");
  var navMenu = document.querySelector(".nav-links, .nav-links2, .nav-menu");

  if (hamburger && navMenu) {
    hamburger.addEventListener("click", function () {
      hamburger.classList.toggle("active");
      navMenu.classList.toggle("active");
    });

    navMenu.querySelectorAll("a").forEach(function (n) {
      n.addEventListener("click", function () {
        hamburger.classList.remove("active");
        navMenu.classList.remove("active");
      });
    });
  }

  var loginBtn = document.querySelector(".login-btn, .login-btn2, .login-btn3");
  if (loginBtn) {
    loginBtn.addEventListener("click", function (e) {
      e.preventDefault();
      window.location.href = 'sign_in.html';
    });
  }
});
