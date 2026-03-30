// ============================================================
// Premier Shop — Main JavaScript
// ============================================================
import 'bootstrap';
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;
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
// 3D Scroll Animations — Enhanced IntersectionObserver
// ============================================================
const revealClasses = '.fade-up, .scale-in, .reveal-3d, .reveal-slide-left, .reveal-slide-right, .reveal-scale, .reveal-flip, .stagger-children';
const observerOptions = { threshold: 0.08, rootMargin: '0px 0px -30px 0px' };
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');

            // For stagger-children: auto-apply delays to children
            if (entry.target.classList.contains('stagger-children')) {
                Array.from(entry.target.children).forEach((child, i) => {
                    child.style.transitionDelay = `${i * 0.07}s`;
                });
            }

            observer.unobserve(entry.target);
        }
    });
}, observerOptions);
document.querySelectorAll(revealClasses).forEach(el => observer.observe(el));

// ============================================================
// 3D Mouse-Follow Tilt Effect
// ============================================================
document.querySelectorAll('.tilt-3d').forEach(el => {
    el.addEventListener('mousemove', (e) => {
        const rect = el.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        const centerX = rect.width / 2;
        const centerY = rect.height / 2;
        const rotateX = ((y - centerY) / centerY) * -4;
        const rotateY = ((x - centerX) / centerX) * 4;
        el.style.transform = `perspective(800px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
    });
    el.addEventListener('mouseleave', () => {
        el.style.transform = `perspective(800px) rotateX(0) rotateY(0)`;
        el.style.transition = 'transform 0.4s ease-out';
        setTimeout(() => { el.style.transition = 'transform 0.1s ease-out'; }, 400);
    });
});

// ============================================================
// Parallax Scroll — subtle depth on hero/profile headers
// ============================================================
const parallaxElements = document.querySelectorAll('.hero-section, .profile-header');
if (parallaxElements.length > 0) {
    window.addEventListener('scroll', () => {
        const scrollY = window.scrollY;
        parallaxElements.forEach(el => {
            const speed = 0.3;
            const yPos = -(scrollY * speed);
            el.style.backgroundPositionY = `${yPos}px`;
        });
    }, { passive: true });
}

// ============================================================
// Search Auto-Suggest
// ============================================================
function initSearch(inputId, suggestionsId) {
    const input = document.getElementById(inputId);
    const suggestions = document.getElementById(suggestionsId);
    const container = input?.closest('.search-container');
    const searchBtn = container?.querySelector('.search-btn');
    
    if (!input || !suggestions) return;

    let debounceTimer;

    const performSearch = () => {
        const query = input.value.trim();
        if (query) {
            window.location.href = `/products?search=${encodeURIComponent(query)}`;
        }
    };

    if (searchBtn) {
        searchBtn.addEventListener('click', performSearch);
    }

    input.addEventListener('input', function () {
        const query = this.value.trim();
        clearTimeout(debounceTimer);

        if (query.length < 2) {
            suggestions.innerHTML = '';
            suggestions.classList.remove('show');
            return;
        }

        debounceTimer = setTimeout(async () => {
            // Add loading indicator
            suggestions.innerHTML = '<div class="suggest-loading"><div class="spinner-border spinner-border-sm text-primary" role="status"></div><span class="ms-2">Searching...</span></div>';
            suggestions.classList.add('show');

            try {
                const response = await axios.get('/products/suggest', { params: { q: query } });
                const data = response.data;

                if (data.length === 0) {
                    suggestions.innerHTML = `<div class="suggest-empty"><i class="bi bi-search me-2"></i>No products found for "${query}"</div>`;
                    return;
                }

                suggestions.innerHTML = data.map(item => `
                    <a href="${item.url}" class="suggest-item">
                        <img src="${item.image}" alt="" class="suggest-img" onerror="this.src='/images/placeholder-product.png'">
                        <div class="suggest-info">
                            <div class="suggest-name">${item.name}</div>
                            <div class="suggest-meta">${item.category || ''} · ${item.price}</div>
                        </div>
                    </a>
                `).join('');
            } catch (e) {
                console.error('Search error:', e);
                suggestions.innerHTML = '<div class="suggest-empty text-danger">Search failed. Please try again.</div>';
            }
        }, 400); // 400ms debounce
    });

    // Submit search on Enter
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            performSearch();
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
if (document.getElementById('mobileSearchInput')) {
    initSearch('mobileSearchInput', 'mobileSearchSuggestions');
}

// Category Floating Box Toggle
document.addEventListener('DOMContentLoaded', function() {
    const trigger = document.getElementById('categoryMenuTrigger');
    const menu = document.getElementById('categoryMegaMenu');
    const backdrop = document.getElementById('menuBackdrop');

    if (trigger && menu) {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            menu.classList.toggle('show');
            trigger.classList.toggle('active');
            if (backdrop) backdrop.classList.toggle('show');
        });

        // Close on click outside
        document.addEventListener('click', function(e) {
            if (!menu.contains(e.target) && !trigger.contains(e.target)) {
                menu.classList.remove('show');
                trigger.classList.remove('active');
                if (backdrop) backdrop.classList.remove('show');
            }
        });

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                menu.classList.remove('show');
                trigger.classList.remove('active');
                if (backdrop) backdrop.classList.remove('show');
            }
        });
    }
});
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
