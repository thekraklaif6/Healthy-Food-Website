// ===================== Image Slider =====================
class ImageSlider {
    constructor() {
        this.currentSlide = 0;
        this.slides = document.querySelectorAll('.slide');
        this.dots = document.querySelectorAll('.dot');
        this.prevBtn = document.querySelector('.prev-btn');
        this.nextBtn = document.querySelector('.next-btn');
        this.autoPlayInterval = null;
        this.autoPlayDelay = 15000;
        if (!this.slides.length) return;
        this.init();
    }

    init() {
        this.addEventListeners();
        this.showSlide(0);
        this.startAutoPlay();
        this.pauseOnHover();
    }

    addEventListeners() {
        if (this.prevBtn) this.prevBtn.addEventListener('click', () => this.prevSlide());
        if (this.nextBtn) this.nextBtn.addEventListener('click', () => this.nextSlide());
        this.dots.forEach((dot, index) => {
            dot.addEventListener('click', () => this.goToSlide(index));
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowRight') this.nextSlide();
            else if (e.key === 'ArrowLeft') this.prevSlide();
        });
        let touchStartX = 0;
        const slider = document.querySelector('.slider');
        if (slider) {
            slider.addEventListener('touchstart', (e) => { touchStartX = e.changedTouches[0].screenX; });
            slider.addEventListener('touchend', (e) => {
                const endX = e.changedTouches[0].screenX;
                if (touchStartX - endX > 50) this.nextSlide();
                else if (endX - touchStartX > 50) this.prevSlide();
            });
        }
    }

    showSlide(index) {
        if (index >= this.slides.length) this.currentSlide = 0;
        else if (index < 0) this.currentSlide = this.slides.length - 1;
        else this.currentSlide = index;
        this.slides.forEach(s => s.classList.remove('active'));
        this.dots.forEach(d => d.classList.remove('active'));
        this.slides[this.currentSlide]?.classList.add('active');
        this.dots[this.currentSlide]?.classList.add('active');
    }

    nextSlide() { this.showSlide(this.currentSlide + 1); this.resetAutoPlay(); }
    prevSlide() { this.showSlide(this.currentSlide - 1); this.resetAutoPlay(); }
    goToSlide(index) { this.showSlide(index); this.resetAutoPlay(); }
    startAutoPlay() { this.autoPlayInterval = setInterval(() => this.nextSlide(), this.autoPlayDelay); }
    stopAutoPlay() { if (this.autoPlayInterval) clearInterval(this.autoPlayInterval); }
    resetAutoPlay() { this.stopAutoPlay(); this.startAutoPlay(); }
    pauseOnHover() {
        const slider = document.querySelector('.slider');
        if (slider) {
            slider.addEventListener('mouseenter', () => this.stopAutoPlay());
            slider.addEventListener('mouseleave', () => this.startAutoPlay());
        }
    }
}

// ===================== Testimonials =====================
let testimonials = [];
let testIndex = 0;
let testAutoInterval = null;
let reviewsCacheKey = '';
let reviewsLoading = false;

function starsHTML(rating) {
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        stars += i <= rating
            ? '<span style="color:#ffb74d;">★</span>'
            : '<span style="color:#ddd;">★</span>';
    }
    return stars;
}

function renderTestDots() {
    const container = document.getElementById('dots');
    if (!container) return;
    container.innerHTML = testimonials.map((_, i) =>
        `<span class="dot ${i === testIndex ? 'active' : ''}" onclick="showTest(${i})"></span>`
    ).join('');
}

function showTest(index) {
    testIndex = index;
    updateTest();
}

function updateTest() {
    const nameEl  = document.getElementById("t-name");
    const textEl  = document.getElementById("t-text");
    const starsEl = document.querySelector(".testimonial-box .food-slider-stars");
    if (!nameEl || !textEl) return;
    if (!testimonials.length || !testimonials[testIndex]) return;

    nameEl.textContent  = testimonials[testIndex].name;
    textEl.textContent  = testimonials[testIndex].comment || testimonials[testIndex].text;
    if (starsEl) starsEl.innerHTML = starsHTML(testimonials[testIndex].rating || 5);
    renderTestDots();
}

function nextTest() {
    if (!testimonials.length) return;
    testIndex = (testIndex + 1) % testimonials.length;
    updateTest();
}

function prevTest() {
    if (!testimonials.length) return;
    testIndex = (testIndex - 1 + testimonials.length) % testimonials.length;
    updateTest();
}

async function loadReviews() {
    if (reviewsLoading) return;
    reviewsLoading = true;
    try {
        const res  = await fetch(API_BASE + '/reviews/get.php');
        const data = await res.json();

        var newReviews;
        if (data.success && data.reviews.length > 0) {
            newReviews = data.reviews;
        } else {
            return;
        }

        var cacheKey = JSON.stringify(newReviews);
        if (cacheKey === reviewsCacheKey) return;
        reviewsCacheKey = cacheKey;

        testimonials = newReviews;
        testIndex = 0;
        updateTest();

        if (testAutoInterval) clearInterval(testAutoInterval);
        testAutoInterval = setInterval(nextTest, 4000);

    } catch (err) {
        console.debug("Reviews API error:", err);
        if (testimonials.length === 0) {
            testimonials = [
                { name: "Mary Lukach", rating: 5, comment: "Amazing food and fast delivery!" },
                { name: "John Smith",  rating: 5, comment: "Best healthy meals I've tried."  },
                { name: "Sara Lee",    rating: 5, comment: "Super tasty and quick service!"  }
            ];
            testIndex = 0;
            updateTest();
            testAutoInterval = setInterval(nextTest, 4000);
        }
    } finally {
        reviewsLoading = false;
    }
}

// ===================== Food Slider =====================
let currentIndex = 0;

function showSlide2(index) {
    const track = document.getElementById('foodTrack');
    const cards = track?.querySelectorAll('.food-slider-card');
    if (!cards?.length) return;
    const visible = window.innerWidth <= 600 ? 1 : window.innerWidth <= 992 ? 2 : 3;
    const maxIndex = Math.max(0, cards.length - visible);
    currentIndex = Math.max(0, Math.min(index, maxIndex));
    track.style.transform = `translateX(-${currentIndex * (100 / visible)}%)`;
    updateDots2();
}

function next() { showSlide2(currentIndex + 1); }
function prev() { showSlide2(currentIndex - 1); }

function updateDots2() {
    const dots = document.querySelectorAll('.food-slider-dot');
    dots.forEach((d, i) => d.classList.toggle('active', i === currentIndex));
}

// ===================== Popular Products =====================
async function loadPopularProducts() {
    var track = document.getElementById('foodTrack');
    var dotsContainer = document.getElementById('foodDots');
    if (!track) return;

    try {
        var res = await fetch(API_BASE + '/products/get_popular.php');
        var data = await res.json();

        if (data.items && data.items.length) {
            track.innerHTML = '';
            data.items.forEach(function(item) {
                var card = document.createElement('div');
                card.className = 'food-slider-card';
                card.innerHTML =
                    '<img src="' + (item.image_url || 'images/default-food.png') + '" alt="' + item.name + '" onerror="this.src=\'images/default-food.png\'">' +
                    '<h3>' + item.name + '</h3>' +
                    '<p>' + (item.description || '').substring(0, 60) + '</p>' +
                    '<div class="food-slider-stars">⭐⭐⭐⭐⭐</div>' +
                    '<p class="food-slider-price">$' + parseFloat(item.price).toFixed(2) + '</p>' +
                    '<button class="food-slider-btn" onclick="addToCart(' + item.id + ')">Add to cart</button>';
                track.appendChild(card);
            });

            if (dotsContainer) {
                dotsContainer.innerHTML = '';
                data.items.forEach(function(_, i) {
                    var dot = document.createElement('span');
                    dot.className = 'food-slider-dot' + (i === 0 ? ' active' : '');
                    dot.onclick = function() { showSlide2(i); };
                    dotsContainer.appendChild(dot);
                });
            }

            setTimeout(function() { showSlide2(0); }, 100);
            window.addEventListener('resize', function() { showSlide2(currentIndex); });
            return;
        }
    } catch (e) {
        console.debug('Could not load popular products, using fallback');
    }

    // Fallback: use existing cards in HTML
    var cards = track.querySelectorAll('.food-slider-card');
    if (cards?.length) {
        if (dotsContainer) {
            dotsContainer.innerHTML = '';
            cards.forEach(function(_, i) {
                var dot = document.createElement('span');
                dot.className = 'food-slider-dot' + (i === 0 ? ' active' : '');
                dot.onclick = function() { showSlide2(i); };
                dotsContainer.appendChild(dot);
            });
        }
        setTimeout(function() { showSlide2(0); }, 100);
        window.addEventListener('resize', function() { showSlide2(currentIndex); });
    }
}

// ===================== Scroll Animation =====================
window.addEventListener("scroll", () => {
    const about = document.getElementById("about");
    if (about && window.scrollY > 100) about.classList.add("show");
});

// ===================== DOMContentLoaded =====================
document.addEventListener('DOMContentLoaded', () => {
    new ImageSlider();
    loadReviews();
    setInterval(loadReviews, 5000);

    const box = document.getElementById("test");
    if (box) {
        let startX = 0;
        box.addEventListener("touchstart", e => { startX = e.touches[0].clientX; });
        box.addEventListener("touchend", e => {
            const endX = e.changedTouches[0].clientX;
            if (startX - endX > 50) nextTest();
            if (endX - startX > 50) prevTest();
        });
    }

    loadPopularProducts();
});