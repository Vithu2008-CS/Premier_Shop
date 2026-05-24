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
// Unified RAF-Throttled Scroll Handler
// (navbar + parallax + scroll-progress in one rAF loop)
// ============================================================
const navbar = document.querySelector('.navbar-premium');
const parallaxElements = document.querySelectorAll('.hero-section, .profile-header');
const progressWrap = document.getElementById('scrollProgress');
const progressPath = document.querySelector('.scroll-progress-wrap path');
let progressPathLength = 0;

if (progressWrap && progressPath) {
    progressPathLength = progressPath.getTotalLength();
    progressPath.style.transition = progressPath.style.WebkitTransition = 'none';
    progressPath.style.strokeDasharray = `${progressPathLength} ${progressPathLength}`;
    progressPath.style.strokeDashoffset = progressPathLength;
    progressPath.getBoundingClientRect();
    progressPath.style.transition = progressPath.style.WebkitTransition = 'stroke-dashoffset 10ms linear';
    progressWrap.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}

const handleScroll = () => {
    const y = window.scrollY;
    if (navbar) navbar.classList.toggle('scrolled', y > 50);
    if (parallaxElements.length) {
        const yPos = -(y * 0.3);
        parallaxElements.forEach(el => { el.style.backgroundPositionY = `${yPos}px`; });
    }
    if (progressWrap && progressPathLength) {
        const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        progressPath.style.strokeDashoffset = progressPathLength - (y * progressPathLength / height);
        progressWrap.classList.toggle('active', y > 300);
    }
};

let scrollRAF = null;
window.addEventListener('scroll', () => {
    if (scrollRAF) return;
    scrollRAF = requestAnimationFrame(() => { handleScroll(); scrollRAF = null; });
}, { passive: true });
handleScroll();

// ============================================================
// 3D Scroll Animations — Enhanced IntersectionObserver
// ============================================================
const revealClasses = '.fade-up, .scale-in, .reveal-3d, .reveal-slide-left, .reveal-slide-right, .reveal-scale, .reveal-flip, .stagger-children';
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');

            if (entry.target.classList.contains('stagger-children')) {
                Array.from(entry.target.children).forEach((child, i) => {
                    child.style.transitionDelay = `${i * 0.07}s`;
                });
            }

            observer.unobserve(entry.target);
        }
    });
}, { threshold: 0.08, rootMargin: '0px 0px -30px 0px' });
document.querySelectorAll(revealClasses).forEach(el => observer.observe(el));

// ============================================================
// 3D Mouse-Follow Tilt Effect — RAF throttled
// ============================================================
document.querySelectorAll('.tilt-3d').forEach(el => {
    let tiltRAF = null;
    el.addEventListener('mousemove', (e) => {
        if (tiltRAF) return;
        tiltRAF = requestAnimationFrame(() => {
            const rect = el.getBoundingClientRect();
            const rotateX = ((e.clientY - rect.top - rect.height / 2) / (rect.height / 2)) * -4;
            const rotateY = ((e.clientX - rect.left - rect.width / 2) / (rect.width / 2)) * 4;
            el.style.transform = `perspective(800px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
            tiltRAF = null;
        });
    });
    el.addEventListener('mouseleave', () => {
        if (tiltRAF) { cancelAnimationFrame(tiltRAF); tiltRAF = null; }
        el.style.transform = `perspective(800px) rotateX(0) rotateY(0)`;
        el.style.transition = 'transform 0.4s ease-out';
        setTimeout(() => { el.style.transition = 'transform 0.1s ease-out'; }, 400);
    });
});

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
                        <img src="${item.image}" alt="" class="suggest-img" loading="lazy" decoding="async" onerror="this.src='/images/placeholder-product.png'">
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
        }, 400);
    });

    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            performSearch();
        }
    });

    document.addEventListener('click', (e) => {
        if (!input.contains(e.target) && !suggestions.contains(e.target)) {
            suggestions.classList.remove('show');
        }
    });
}

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

        document.addEventListener('click', function(e) {
            if (!menu.contains(e.target) && !trigger.contains(e.target)) {
                menu.classList.remove('show');
                trigger.classList.remove('active');
                if (backdrop) backdrop.classList.remove('show');
            }
        });

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
// Smart Flash Sale Countdown Timer (Rolling 48-Hour Baseline)
// ============================================================
const initFlashCountdown = () => {
    const countdownContainer = document.getElementById('flashCountdown');
    if (!countdownContainer) return;

    const dEl = document.getElementById('cd-days');
    const hEl = document.getElementById('cd-hours');
    const mEl = document.getElementById('cd-mins');
    const sEl = document.getElementById('cd-secs');

    let targetTime = parseInt(localStorage.getItem('flash_deal_target'));
    const now = new Date().getTime();

    if (!targetTime || targetTime < now) {
        targetTime = now + (48 * 60 * 60 * 1000);
        localStorage.setItem('flash_deal_target', targetTime);
    }

    const updateTimer = () => {
        const currentTime = new Date().getTime();
        const difference = targetTime - currentTime;

        if (difference <= 0) {
            targetTime = currentTime + (48 * 60 * 60 * 1000);
            localStorage.setItem('flash_deal_target', targetTime);
            return;
        }

        const days = Math.floor(difference / (1000 * 60 * 60 * 24));
        const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((difference % (1000 * 60)) / 1000);

        if (dEl) dEl.textContent = String(days).padStart(2, '0');
        if (hEl) hEl.textContent = String(hours).padStart(2, '0');
        if (mEl) mEl.textContent = String(minutes).padStart(2, '0');
        if (sEl) sEl.textContent = String(seconds).padStart(2, '0');
    };

    updateTimer();
    setInterval(updateTimer, 1000);
};
initFlashCountdown();

// ============================================================
// Interactive Milestone Statistics (Viewport Triggered Count-Up)
// ============================================================
const initMilestoneCounters = () => {
    const counterElements = document.querySelectorAll('.milestone-card .counter-num');
    if (counterElements.length === 0) return;

    const countUp = (el) => {
        const target = parseFloat(el.getAttribute('data-target'));
        const decimals = parseInt(el.getAttribute('data-decimals') || '0');
        const suffix = el.getAttribute('data-suffix') || '';
        const duration = 1500;
        const frameRate = 1000 / 60;
        const totalFrames = Math.round(duration / frameRate);
        let frame = 0;

        const updateCount = () => {
            frame++;
            const progress = frame / totalFrames;
            const easeProgress = progress * (2 - progress);
            el.textContent = (easeProgress * target).toFixed(decimals) + suffix;

            if (frame < totalFrames) {
                requestAnimationFrame(updateCount);
            } else {
                el.textContent = target.toFixed(decimals) + suffix;
            }
        };

        requestAnimationFrame(updateCount);
    };

    const milestoneObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                countUp(entry.target);
                milestoneObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    counterElements.forEach((el) => milestoneObserver.observe(el));
};
initMilestoneCounters();

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
