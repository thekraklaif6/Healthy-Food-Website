// ===================== Image Slider =====================
class ImageSlider {
    constructor() {
        this.currentSlide = 0;
        this.slides = document.querySelectorAll('.slide');
        this.dots = document.querySelectorAll('.dot');
        this.prevBtn = document.querySelector('.prev-btn');
        this.nextBtn = document.querySelector('.next-btn');
        this.autoPlayInterval = null;
        this.autoPlayDelay = 5000;
        this.init();
    }

    init() {
        if (!this.slides.length) return;
        this.addEventListeners();
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
        this.slides.forEach(slide => slide.classList.remove('active'));
        this.dots.forEach(dot => dot.classList.remove('active'));
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

// ===================== Dynamic Products =====================
let drinksDataCache = '';
let drinksLoading = false;

function renderDrinkCard(item) {
    return '<div class="card-image">' +
        '<img src="' + (item.image_url || 'images/default-food.png') + '" alt="' + item.name + '" onerror="this.src=\'images/default-food.png\'">' +
    '</div>' +
    '<div class="card-content">' +
        '<div class="card-header">' +
            '<h2 class="card-title">' +
                '<i class="fas fa-glass-cheers"></i> ' + item.name +
            '</h2>' +
            '<span class="card-badge"><i class="fas fa-star"></i><b> Refreshing drink</b></span>' +
        '</div>' +
        '<div class="card-description">' +
            '<i class="fas fa-circle" style="font-size: 0.4rem; vertical-align: middle;"></i> ' +
            (item.description || '') +
        '</div>' +
        '<div class="nutrition-info">' +
            '<span class="calories"><i class="fas fa-fire"></i> ' + (item.calories || '0') + ' cal</span>' +
            '<span class="protein"><i class="fas fa-dumbbell"></i> ' + (item.protein || '0') + 'g protein</span>' +
        '</div>' +
        '<div class="card-footer">' +
            '<span class="price-tag">$' + parseFloat(item.price).toFixed(2) + ' <small>USD</small></span>' +
            '<div class="card-actions">' +
                '<button class="details-btn" onclick="window.location.href=\'product.html?id=' + item.id + '\'">show details <i class="fas fa-arrow-right"></i></button>' +
                '<button class="add-cart-btn" onclick="addToCart(' + item.id + ')"><i class="fas fa-cart-plus"></i></button>' +
            '</div>' +
        '</div>' +
    '</div>';
}

async function loadDrinks() {
    if (drinksLoading) return;
    const grid = document.getElementById('cardsGrid');
    if (!grid) return;

    drinksLoading = true;
    try {
        const res = await fetch(API_BASE + '/products/list.php?category=drinks');
        const data = await res.json();
        const cacheKey = JSON.stringify(data.items || []);

        if (cacheKey === drinksDataCache) return;
        drinksDataCache = cacheKey;

        if (data.items && data.items.length) {
            grid.innerHTML = '';
            data.items.forEach(function(item) {
                const card = document.createElement('div');
                card.className = 'sandwich-card';
                card.innerHTML = renderDrinkCard(item);
                grid.appendChild(card);
            });
        }
    } catch (err) {
        console.error('Failed to load drinks:', err);
    } finally {
        drinksLoading = false;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    new ImageSlider();
    loadDrinks();
    setInterval(loadDrinks, 5000);
});
