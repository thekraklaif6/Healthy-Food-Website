(function () {
  document.addEventListener('DOMContentLoaded', function () {
    initFormHandlers();
    initCartSystem();
  });

  function initFormHandlers() {
    document.querySelectorAll('form').forEach(function (form) {
      if (form.id === 'loginForm' || form.id === 'signupForm' || form.id === 'contactForm') {
        return;
      }
      form.addEventListener('submit', function (e) {
        e.preventDefault();
        var message = 'The data has been sent successfully.';

        if (form.classList.contains('login-form')) {
          message = 'Signed in successfully. Redirecting to home...';
        } else if (form.classList.contains('signup-form')) {
          message = 'Account created successfully.';
        } else if (form.closest('.contact-form') || form.querySelector('textarea')) {
          message = 'Your message has been received. We will contact you soon.';
        }

        showFormMessage(form, message);
        form.reset();

        if (form.classList.contains('login-form')) {
          setTimeout(function () {
            window.location.href = 'home.html';
          }, 900);
        }
      });
    });
  }

  function showFormMessage(form, message) {
    var existingNote = form.querySelector('.fp-form-note');
    if (existingNote) {
      existingNote.textContent = message;
      return;
    }

    var note = document.createElement('div');
    note.className = 'fp-form-note';
    note.style.marginTop = '1rem';
    note.style.padding = '0.9rem 1rem';
    note.style.background = '#e8f8f5';
    note.style.border = '1px solid #2ecc71';
    note.style.color = '#1d6f4f';
    note.style.borderRadius = '12px';
    note.style.fontWeight = '600';
    note.textContent = message;
    form.appendChild(note);
  }

  function initCartSystem() {
    var cart = loadCart();
    updateCartBadge(cart.count);
    attachCartButtons();
    bindCartIcon();
  }

  function bindCartIcon() {
    var cartIcons = document.querySelectorAll("img[src*='shopping-cart']");
    cartIcons.forEach(function (icon) {
      icon.style.cursor = 'pointer';
      icon.addEventListener('click', function () {
        window.location.href = 'cart.html';
      });
    });
  }

  function attachCartButtons() {
    var buttons = document.querySelectorAll('button.food-slider-btn, button.add-btn, button.order-btn, button.btn-fill');
    if (!buttons.length) return;

    buttons.forEach(function (button) {
      if (button.hasAttribute('onclick')) return;

      button.addEventListener('click', function () {
        var label = button.textContent.trim().toLowerCase();
        if (label === 'add to cart' || label.indexOf('add') !== -1 || label.indexOf('order') !== -1) {
          addToCart(button);
        }
      });
    });
  }

  function addToCart(button) {
    var card = button.closest('.food-slider-card, .sandwich-card, .meal-box, .card, .meal-card, .dessert-card');
    var name = '';
    var price = '';
    var quantity = 1;
    var variant = '';

    if (card) {
      var titleEl = card.querySelector('h2, h3, .card-title, .meal-text h2, .meal-box h2');
      if (titleEl) name = titleEl.textContent.trim();
      var priceEl = card.querySelector('.food-slider-price, .price-tag, .price');
      if (priceEl) price = priceEl.textContent.trim();

      var qtyEl = card.querySelector('#quantity, .quantity span, .quantity-number');
      if (qtyEl) {
        var qtyValue = parseInt(qtyEl.textContent || qtyEl.value, 10);
        if (!isNaN(qtyValue) && qtyValue > 0) {
          quantity = qtyValue;
        }
      }

      var sizeEl = card.querySelector('.size-btn.active, .size-btn2.active, .size-selector .active');
      if (sizeEl) {
        variant = sizeEl.textContent.trim();
      }
    }

    if (!name) {
      name = button.dataset.name || button.dataset.sandwich || button.textContent.trim();
    }

    if (variant) {
      name += ' (' + variant + ')';
    }

    var cart = loadCart();
    var existing = cart.items.find(function (item) {
      return item.name === name && item.price === price;
    });

    if (existing) {
      existing.quantity += quantity;
    } else {
      cart.items.push({
        name: name,
        price: price,
        quantity: quantity
      });
    }

    cart.count += quantity;
    saveCart(cart);
    updateCartBadge(cart.count);
    showCartToast('Added ' + quantity + ' × "' + name + '" to cart. Total items: ' + cart.count + '.');
  }

  function loadCart() {
    try {
      var data = window.localStorage.getItem('freshplateCart');
      if (!data) return { count: 0, items: [] };
      return JSON.parse(data);
    } catch (e) {
      return { count: 0, items: [] };
    }
  }

  function saveCart(cart) {
    try {
      window.localStorage.setItem('freshplateCart', JSON.stringify(cart));
    } catch (e) {
      // ignore storage errors
    }
  }

  function updateCartBadge(count) {
    var badge = document.querySelector('.fp-cart-badge');
    if (!badge) {
      var container = document.querySelector('.nav-right, .login-section, .nav-container');
      if (!container) return;
      badge = document.createElement('span');
      badge.className = 'fp-cart-badge';
      badge.style.position = 'absolute';
      badge.style.top = '8px';
      badge.style.right = '8px';
      badge.style.minWidth = '18px';
      badge.style.height = '18px';
      badge.style.fontSize = '0.75rem';
      badge.style.fontWeight = '700';
      badge.style.display = 'inline-flex';
      badge.style.alignItems = 'center';
      badge.style.justifyContent = 'center';
      badge.style.background = '#2ecc71';
      badge.style.color = '#fff';
      badge.style.borderRadius = '999px';
      badge.style.padding = '0 6px';
      badge.style.zIndex = '100';

      container.style.position = 'relative';
      container.appendChild(badge);
    }
    badge.textContent = count;
    badge.style.display = count > 0 ? 'inline-flex' : 'none';
  }

  function showCartToast(message) {
    var toast = document.querySelector('.fp-cart-toast');
    if (!toast) {
      toast = document.createElement('div');
      toast.className = 'fp-cart-toast';
      toast.style.position = 'fixed';
      toast.style.bottom = '20px';
      toast.style.left = '50%';
      toast.style.transform = 'translateX(-50%)';
      toast.style.background = 'rgba(0, 0, 0, 0.8)';
      toast.style.color = '#fff';
      toast.style.padding = '12px 18px';
      toast.style.borderRadius = '999px';
      toast.style.zIndex = '10000';
      toast.style.fontSize = '0.95rem';
      toast.style.maxWidth = '90%';
      toast.style.textAlign = 'center';
      toast.style.boxShadow = '0 16px 40px rgba(0, 0, 0, 0.25)';
      document.body.appendChild(toast);
    }

    toast.textContent = message;
    toast.style.opacity = '1';
    toast.style.transition = 'opacity 0.3s ease';
    toast.style.display = 'block';

    clearTimeout(toast.hideTimeout);
    toast.hideTimeout = setTimeout(function () {
      toast.style.opacity = '0';
      setTimeout(function () {
        toast.style.display = 'none';
      }, 300);
    }, 1800);
  }

})();