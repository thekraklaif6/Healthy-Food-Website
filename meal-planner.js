// ===================== Save original addToCart from config.js =====================
var configAddToCart = typeof window.addToCart === 'function' ? window.addToCart : null;

// ===================== State =====================
let selectedGoal = "";
let currentPlan  = null; // الخطة الحالية المولّدة

// Fallback لو الـ API ما شتغل
const mealDatabase = {
    breakfast: ["Classic Oatmeal", "Spinach Omelet", "Protein Smoothie", "Avocado Toast"],
    lunch:     ["Grilled Chicken & Rice", "Tuna Salad Bowl", "Lentil Stew", "Turkey Sandwich"],
    dinner:    ["Baked Salmon & Veggies", "Lean Beef Stir-fry", "Zucchini Pasta", "Grilled Tofu"]
};

// ===================== Step 1: Goal =====================
function selectGoal(card, goal) {
    document.querySelectorAll('.goal-card').forEach(c => c.classList.remove('selected'));
    card.classList.add('selected');
    selectedGoal = goal;

    document.getElementById('s2').classList.add('active');
    document.getElementById('detailsSection').classList.remove('hidden');
    document.getElementById('detailsSection').scrollIntoView({ behavior: 'smooth' });
}

// ===================== Step 2: Modal =====================
function openModal() {
    if (!selectedGoal) {
        alert('Please select a goal first.');
        return;
    }
    document.getElementById('setupModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('setupModal').style.display = 'none';
}

// ===================== Step 3: Generate Plan =====================
async function processAndShowResult() {
    closeModal();

    const dietType     = document.getElementById('dietType').value;
    const weightKg     = Number(document.getElementById('weight').value)        || 70;
    const heightCm     = Number(document.getElementById('height').value)        || 170;
    const activityLevel= Number(document.getElementById('activityLevel')?.value)|| 1.5;
    const allergies    = Array.from(document.querySelectorAll('.tag-toggle.active'))
                              .map(el => el.textContent.trim());

    const payload = {
        goal:          selectedGoal,
        dietType,
        weightKg,
        heightCm,
        activityLevel,
        allergies
    };

    // إظهار الـ dashboard فوراً مع loading
    document.getElementById('goalSection').classList.add('hidden');
    document.getElementById('detailsSection').classList.add('hidden');
    document.getElementById('mealPlanResult').classList.remove('hidden');
    document.getElementById('s3').classList.add('active');
    document.getElementById('resGoal').innerText = selectedGoal;
    document.getElementById('resDiet').innerText = dietType;
    document.getElementById('mealTableBody').innerHTML =
        '<tr><td colspan="4" style="text-align:center; padding:30px; color:#aaa;">Generating your plan...</td></tr>';
    window.scrollTo({ top: 0, behavior: 'smooth' });

    try {
        const res  = await fetch(API_BASE + '/meal_plans/generate.php', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'include',
            body: JSON.stringify(payload)
        });
        const data = await res.json();

        // يقبل الـ response سواء فيه success:true أو لا
        const plan = data.plan || (data.success && data.plan);
        if (plan?.days) {
            currentPlan = plan;
            renderDashboard(plan);
        } else {
            renderFallback(dietType);
        }
    } catch (e) {
        console.warn('API unavailable, using fallback:', e);
        renderFallback(dietType);
    }
}

// ===================== Render Dashboard (من API) =====================
function setStatText(id, value) {
    // يشتغل مع HTML القديم (p مباشر داخل stat-card) والجديد (بـ id)
    const el = document.getElementById(id);
    if (el) { el.innerText = value; return; }
    // fallback: ابحث عن stat-card بالعنوان
    const labels = { statCal:'Calories', statProt:'Protein', statCarb:'Carbs', statFat:'Fats' };
    const label = labels[id];
    if (!label) return;
    document.querySelectorAll('.stat-card').forEach(card => {
        if (card.querySelector('h5')?.innerText?.trim() === label) {
            const p = card.querySelector('p');
            if (p) p.innerText = value;
        }
    });
}

function renderDashboard(plan) {
    // --- Macros ---
    const m = plan.macros;
    setStatText('statCal',  `${Number(m.calories).toLocaleString()} kcal`);
    setStatText('statProt', `${m.protein}g`);
    setStatText('statCarb', `${m.carbs}g`);
    setStatText('statFat',  `${m.fats}g`);

    // --- Meal Table ---
    const tbody = document.getElementById('mealTableBody');
    if (tbody) {
        tbody.innerHTML = plan.days.map(row => `
            <tr>
                <td><strong>${row.day}</strong></td>
                <td>
                    <span>${row.breakfast}</span>
                    <button class="swap-btn" onclick="swapMeal(this, 'breakfast', ${row.b_id ?? 'null'})">🔄</button>
                </td>
                <td>
                    <span>${row.lunch}</span>
                    <button class="swap-btn" onclick="swapMeal(this, 'lunch', ${row.l_id ?? 'null'})">🔄</button>
                </td>
                <td>
                    <span>${row.dinner}</span>
                    <button class="swap-btn" onclick="swapMeal(this, 'dinner', ${row.d_id ?? 'null'})">🔄</button>
                </td>
            </tr>`).join('');
    }

    // --- Shopping List ---
    const list = document.getElementById('shoppingList');
    if (list) {
        list.innerHTML = plan.shoppingList?.length > 0
            ? plan.shoppingList.map(item => `<li>${item}</li>`).join('')
            : '<li style="color:#aaa;">No items found</li>';
    }
}

// ===================== Fallback (بدون API) =====================
function renderFallback(dietType) {
    setStatText('statCal',  '1,850 kcal');
    setStatText('statProt', '125g');
    setStatText('statCarb', '180g');
    setStatText('statFat',  '60g');

    const days  = ['Monday','Tuesday','Wednesday','Thursday','Friday'];
    const tbody = document.getElementById('mealTableBody');
    if (tbody) {
        tbody.innerHTML = days.map(day => `
            <tr>
                <td><strong>${day}</strong></td>
                <td><span>${mealDatabase.breakfast[0]}</span> <button class="swap-btn" onclick="swapMeal(this,'breakfast',null)">🔄</button></td>
                <td><span>${mealDatabase.lunch[0]}</span>     <button class="swap-btn" onclick="swapMeal(this,'lunch',null)">🔄</button></td>
                <td><span>${mealDatabase.dinner[0]}</span>    <button class="swap-btn" onclick="swapMeal(this,'dinner',null)">🔄</button></td>
            </tr>`).join('');
    }

    const list = document.getElementById('shoppingList');
    if (list) list.innerHTML = `
        <li>Chicken Breast (800g)</li>
        <li>Brown Rice (1kg)</li>
        <li>Fresh Spinach</li>
        <li>Eggs (1 Dozen)</li>`;
}

// ===================== Swap Meal =====================
async function swapMeal(btn, type, currentId) {
    const span = btn.previousElementSibling;
    const diet = document.getElementById('dietType')?.value || 'Standard';

    try {
        // جلب وجبات بديلة من DB
        const res  = await fetch(
            `${API_BASE}/meal_plans/get_meals.php?diet=${encodeURIComponent(diet)}&goal=${encodeURIComponent(selectedGoal)}`
        );
        const data = await res.json();

        if (data.success && data.meals[type]?.length > 0) {
            // اختر وجبة مختلفة عن الحالية
            const options = data.meals[type].filter(m => m.id != currentId);
            const pick    = options.length > 0
                ? options[Math.floor(Math.random() * options.length)]
                : data.meals[type][Math.floor(Math.random() * data.meals[type].length)];

            span.innerText = pick.name;
            btn.setAttribute('onclick', `swapMeal(this,'${type}',${pick.id})`);
        } else {
            // fallback محلي
            const options  = mealDatabase[type];
            const filtered = options.filter(m => m !== span.innerText);
            span.innerText = filtered[Math.floor(Math.random() * filtered.length)];
        }
    } catch (e) {
        // fallback محلي لو الـ API ما رد
        const options  = mealDatabase[type];
        const filtered = options.filter(m => m !== span.innerText);
        span.innerText = filtered.length > 0
            ? filtered[Math.floor(Math.random() * filtered.length)]
            : options[Math.floor(Math.random() * options.length)];
    }

    // تأثير بصري
    span.style.color = '#ffb74d';
    setTimeout(() => span.style.color = 'inherit', 600);
}

// ===================== Add to Cart =====================

var mpCartId = null;

var addToCart = async function() {
    var days = currentPlan && currentPlan.days ? currentPlan.days.length : 7;
    var mealsPerDay = 3;
    var pricePerMeal = 5.99;
    var totalPrice = (days * mealsPerDay * pricePerMeal).toFixed(2);

    try {
        var res = await fetch(API_BASE + '/products/ensure_meal_plan.php?price=' + totalPrice);
        var data = await res.json();
        if (!data.id) {
            if (typeof Toast !== 'undefined') Toast.warning('Could not create meal plan product.');
            return;
        }
        mpCartId = data.id;
    } catch (e) {
        if (typeof Toast !== 'undefined') Toast.error('Please login first to add items to cart.');
        return;
    }
    if (configAddToCart) {
        configAddToCart(mpCartId);
    }
}