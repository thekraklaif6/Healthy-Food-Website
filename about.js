(function () {
    var hamburger = document.querySelector('.hamburger');
    var navMenu = document.querySelector('.nav-menu');
    if (hamburger && navMenu) {
      hamburger.addEventListener('click', function () {
        hamburger.classList.toggle('active');
        navMenu.classList.toggle('active');
      });
      document.querySelectorAll('.nav-link').forEach(function (link) {
        link.addEventListener('click', function () {
          hamburger.classList.remove('active');
          navMenu.classList.remove('active');
        });
      });
    }

    var form = document.getElementById('fpReservationForm');
    var note = document.getElementById('fpFormNote');
    if (form) {
      form.addEventListener('submit', function (e) {
        e.preventDefault();
        var fd = new FormData(form);
        var body = {};
        fd.forEach(function(v, k) { body[k] = v; });

        var url = (typeof API_BASE !== 'undefined' ? API_BASE : 'http://localhost/web%20project/api');
        fetch(url + '/reservations/add.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          credentials: 'include',
          body: JSON.stringify(body)
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
          if (note) {
            note.textContent = data.message || 'Thank you! We received your request and will confirm shortly.';
            note.style.color = '#2d6a4f';
          }
          form.reset();
        })
        .catch(function(err) {
          if (note) {
            note.textContent = 'Something went wrong. Please try again.';
            note.style.color = '#c0392b';
          }
        });
      });
    }

    var teamCards = document.querySelectorAll('.fp-team-card');
    teamCards.forEach(function (card) {
      card.addEventListener('click', function () {
        var wasActive = card.classList.contains('is-active');
        teamCards.forEach(function (c) {
          c.classList.remove('is-active');
          c.setAttribute('aria-pressed', 'false');
        });
        if (!wasActive) {
          card.classList.add('is-active');
          card.setAttribute('aria-pressed', 'true');
        }
      });
      card.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          card.click();
        }
      });
    });

    var prefersReduced =
      window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    document.querySelectorAll('.fp-team-photo').forEach(function (wrap) {
      var img = wrap.querySelector('img');
      if (img && img.complete && img.naturalWidth) {
        wrap.classList.add('loaded');
      }
    });

    function runStatCounter() {
      var statsEl = document.getElementById('fpStats');
      if (!statsEl || prefersReduced) return;
      var nums = statsEl.querySelectorAll('.fp-stat-num[data-count]');
      var duration = 1400;
      var start = null;

      function tick(ts) {
        if (!start) start = ts;
        var p = Math.min((ts - start) / duration, 1);
        var ease = 1 - Math.pow(1 - p, 3);
        nums.forEach(function (el) {
          var target = parseInt(el.getAttribute('data-count'), 10);
          var suffix = el.getAttribute('data-suffix') || '';
          var val = Math.round(ease * target);
          el.textContent = val + suffix;
        });
        if (p < 1) {
          requestAnimationFrame(tick);
        } else {
          statsEl.classList.add('is-counted');
        }
      }

      requestAnimationFrame(tick);
    }

    if (prefersReduced) {
      document.querySelectorAll('.fp-reveal').forEach(function (el) {
        el.classList.add('is-visible');
      });
      document.querySelectorAll('.fp-stat-num[data-count]').forEach(function (el) {
        el.textContent = el.getAttribute('data-count') + (el.getAttribute('data-suffix') || '');
      });
    } else {
      var revealObserver = new IntersectionObserver(
        function (entries) {
          entries.forEach(function (entry) {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('is-visible');
            revealObserver.unobserve(entry.target);
          });
        },
        { threshold: 0.12, rootMargin: '0px 0px -32px 0px' }
      );

      document.querySelectorAll('.fp-reveal').forEach(function (el) {
        revealObserver.observe(el);
      });

      var heroStatsObs = new IntersectionObserver(
        function (entries) {
          entries.forEach(function (entry) {
            if (!entry.isIntersecting) return;
            runStatCounter();
            heroStatsObs.unobserve(entry.target);
          });
        },
        { threshold: 0.35 }
      );
      var fpStats = document.getElementById('fpStats');
      if (fpStats) heroStatsObs.observe(fpStats);
    }
  })();