// ============================================================
// Premier Shop — Main JavaScript
// ============================================================
import 'bootstrap';
import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// ============================================================
// Navbar Scroll Effect
// ============================================================
const navbar = document.querySelector('.navbar-premium');
if (navbar) {
    window.addEventListener('scroll', () => {
        navbar.classList.toggle('scrolled', window.scrollY > 50);
    });
}

// ============================================================
// Scroll Animations — IntersectionObserver
// ============================================================
const observerOptions = { threshold: 0.1, rootMargin: '0px 0px -40px 0px' };
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            observer.unobserve(entry.target);
        }
    });
}, observerOptions);
document.querySelectorAll('.fade-up, .scale-in').forEach(el => observer.observe(el));

// ============================================================
// Search Auto-Suggest
// ============================================================
function initSearch(inputId, suggestionsId) {
    const input = document.getElementById(inputId);
    const suggestions = document.getElementById(suggestionsId);
    if (!input || !suggestions) return;

    let debounceTimer;

    input.addEventListener('input', function () {
        const query = this.value.trim();
        clearTimeout(debounceTimer);

        if (query.length < 2) {
            suggestions.innerHTML = '';
            suggestions.classList.remove('show');
            return;
        }

        debounceTimer = setTimeout(async () => {
            try {
                const response = await axios.get('/products/suggest', { params: { q: query } });
                const data = response.data;

                if (data.length === 0) {
                    suggestions.innerHTML = `<div class="suggest-empty"><i class="bi bi-search me-2"></i>No products found for "${query}"</div>`;
                    suggestions.classList.add('show');
                    return;
                }

                suggestions.innerHTML = data.map(item => `
                    <a href="${item.url}" class="suggest-item">
                        <img src="${item.image}" alt="" class="suggest-img" onerror="this.style.display='none'">
                        <div class="suggest-info">
                            <div class="suggest-name">${item.name}</div>
                            <div class="suggest-meta">${item.category || ''} · ${item.price}</div>
                        </div>
                    </a>
                `).join('');
                suggestions.classList.add('show');
            } catch (e) {
                console.error('Search error:', e);
            }
        }, 300);
    });

    // Submit search on Enter
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const query = this.value.trim();
            if (query) {
                window.location.href = `/products?search=${encodeURIComponent(query)}`;
            }
        }
    });

    // Close suggestions on click outside
    document.addEventListener('click', (e) => {
        if (!input.contains(e.target) && !suggestions.contains(e.target)) {
            suggestions.classList.remove('show');
        }
    });
}

// Init desktop and mobile search
initSearch('searchInput', 'searchSuggestions');
initSearch('mobileSearchInput', 'mobileSearchSuggestions');

// ============================================================
// Auto-dismiss Alerts
// ============================================================
document.querySelectorAll('.alert-dismissible').forEach(alert => {
    setTimeout(() => {
        const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
        bsAlert.close();
    }, 5000);
});

// ============================================================
// Add to Cart — Button Feedback
// ============================================================
document.querySelectorAll('.btn-add-cart, .btn-add-to-cart').forEach(btn => {
    btn.addEventListener('click', function () {
        const original = this.innerHTML;
        this.innerHTML = '<i class="bi bi-check-lg me-1"></i> Added!';
        this.classList.add('btn-success');
        this.style.transform = 'scale(0.95)';
        setTimeout(() => { this.style.transform = ''; }, 100);
        setTimeout(() => {
            this.innerHTML = original;
            this.classList.remove('btn-success');
        }, 1500);
    });
});

// ============================================================
// Quantity Stepper
// ============================================================
document.querySelectorAll('.qty-stepper').forEach(stepper => {
    const input = stepper.querySelector('input');
    const minBtn = stepper.querySelector('.qty-minus');
    const maxBtn = stepper.querySelector('.qty-plus');
    if (minBtn) minBtn.addEventListener('click', () => {
        const val = parseInt(input.value) || 1;
        if (val > 1) input.value = val - 1;
        input.dispatchEvent(new Event('change'));
    });
    if (maxBtn) maxBtn.addEventListener('click', () => {
        const val = parseInt(input.value) || 1;
        const max = parseInt(input.max) || 999;
        if (val < max) input.value = val + 1;
        input.dispatchEvent(new Event('change'));
    });
});

// ============================================================
// Back to Top
// ============================================================
const backToTop = document.getElementById('backToTop');
if (backToTop) {
    window.addEventListener('scroll', () => {
        backToTop.style.display = window.scrollY > 400 ? 'flex' : 'none';
    });
    backToTop.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}

// ============================================================
// Tooltip Init
// ============================================================
document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
    new bootstrap.Tooltip(el);
});

// ============================================================
// Prevent Background Scroll on Category Mega Menu
// ============================================================
const categoryMegaMenu = document.getElementById('categoryMegaMenu');
if (categoryMegaMenu) {
    categoryMegaMenu.addEventListener('show.bs.collapse', () => {
        document.documentElement.style.overflow = 'hidden';
        document.body.style.overflow = 'hidden';
    });
    categoryMegaMenu.addEventListener('hide.bs.collapse', () => {
        document.documentElement.style.overflow = '';
        document.body.style.overflow = '';
    });
}
