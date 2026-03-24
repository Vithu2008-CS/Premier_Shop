<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description"
        content="Premier Shop — Your one-stop destination for quality products at unbeatable prices.">
    <meta property="og:title" content="@yield('title', 'Premier Shop — Quality Products')">
    <meta property="og:description" content="Your one-stop destination for quality products at unbeatable prices.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'Premier Shop — Quality Products')">
    <meta name="twitter:description" content="Your one-stop destination for quality products at unbeatable prices.">
    <title>@yield('title', 'Premier Shop — Quality Products')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @stack('seo')
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @stack('styles')
    
    {{-- Pre-render Theme Logic --}}
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>
</head>

<body>
    {{-- Toast Container --}}
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 2000;">
        <div id="liveToast" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="toastMessage"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    {{-- Main Navbar --}}
    <nav class="navbar navbar-expand-lg navbar-premium sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="bi bi-shop"></i> Premier Shop
            </a>

            @if(!auth()->user()?->isDriver())
            {{-- Search Bar - Center --}}
            <div class="search-wrapper d-none d-lg-block flex-grow-1 mx-4">
                <div class="search-container position-relative">
                    <input type="text" id="searchInput" class="form-control search-input"
                        placeholder="Search products..." autocomplete="off">
                    <button class="search-btn"><i class="bi bi-search"></i></button>
                    <div id="searchSuggestions" class="search-suggestions"></div>
                </div>
            </div>
            @endif

            {{-- Mobile Icons --}}
            <div class="d-flex align-items-center gap-2 d-lg-none">
                <button class="btn btn-link text-white p-2" data-bs-toggle="modal" data-bs-target="#searchModal">
                    <i class="bi bi-search fs-5"></i>
                </button>
                @auth
                    @if(!auth()->user()->isDriver())
                    <a href="{{ route('cart.index') }}" class="btn btn-link text-white p-2 cart-badge">
                        <i class="bi bi-bag fs-5"></i>
                        @php $cartCount = auth()->user()->cartItems()->sum('quantity'); @endphp
                        <span class="badge cart-count-badge" id="cartCountBadgeMobile" style="{{ $cartCount > 0 ? '' : 'display:none;' }}">{{ $cartCount }}</span>
                    </a>
                    @endif
                @endauth
                <button class="navbar-toggler border-0 p-2" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#mobileMenu">
                    <i class="bi bi-list text-white fs-4"></i>
                </button>
            </div>

            {{-- Desktop Nav --}}
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-1">
                    @if(!auth()->user()?->isDriver())
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('offers') ? 'active' : '' }}"
                            href="{{ route('offers') }}">
                            <i class="bi bi-tag me-1"></i>Offers
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('category') ? 'active' : '' }}" href="javascript:void(0)"
                            id="categoryMenuTrigger">
                            <i class="bi bi-grid-3x3-gap me-1"></i>Categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('products.*') && !request('category') ? 'active' : '' }}"
                            href="{{ route('products.index') }}">
                            <i class="bi bi-box-seam me-1"></i>Products
                        </a>
                    </li>
                                       @endif
                    @auth
                        @if(!auth()->user()->isDriver())
                        <li class="nav-item">
                            <a class="nav-link cart-badge" href="{{ route('cart.index') }}">
                                <i class="bi bi-bag"></i>
                                <span class="d-none d-xl-inline">Cart</span>
                                <span class="badge cart-count-badge" id="cartCountBadge" style="{{ ($cartCount ?? 0) > 0 ? '' : 'display:none;' }}">{{ $cartCount ?? 0 }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('wishlists.index') }}" title="Wishlist">
                                <i class="bi bi-heart"></i>
                                <span class="d-none d-xl-inline">Wishlist</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ auth()->user()->isDriver() ? route('driver.dashboard') : route('orders.index') }}" title="Orders">
                                <i class="bi bi-receipt"></i> 
                                <span class="d-none d-xl-inline">{{ auth()->user()->isDriver() ? 'Deliveries' : 'Orders' }}</span>
                            </a>
                        </li>
                        @endif
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> 
                                <span class="d-none d-lg-inline">{{ explode(' ', auth()->user()->name)[0] }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @if(auth()->user()->isAdmin())
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i
                                                 class="bi bi-speedometer2 me-2"></i>Admin</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                @endif
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i
                                             class="bi bi-gear me-2"></i>Profile</a></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button class="dropdown-item text-danger"><i
                                                 class="bi bi-box-arrow-right me-2"></i>Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link cart-badge" href="{{ route('login') }}">
                                <i class="bi bi-bag"></i>
                                <span class="d-none d-xl-inline">Cart</span>
                                <span class="badge cart-count-badge" style="display: none;">0</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right me-1"></i>Login
                            </a>
                        </li>
                        <li class="nav-item ms-lg-1">
                             <a class="nav-link btn-signup px-3 py-2" href="{{ route('register') }}">Sign Up</a>
                        </li>
                    @endauth
                    <li class="nav-item ms-lg-2">
                        <button class="theme-toggle-btn" id="themeToggle" title="Toggle Dark/Light Mode">
                            <i class="bi bi-moon-stars"></i>
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Category Mega Menu (Moved inside nav for relative positioning) --}}
        @if(!auth()->user()?->isDriver())
        <div class="category-mega-menu" id="categoryMegaMenu">
            <div class="container py-4">
                <div class="row g-4 stagger-children">
                    @foreach($globalCategories as $cat)
                        <div class="col-6 col-md-4 col-lg-3 fade-up">
                            <div class="category-list-group">
                                <h6 class="fw-bold mb-3 border-bottom pb-2">
                                    <a href="{{ route('products.index', ['category' => $cat->slug]) }}" class="text-dark text-decoration-none hover-primary d-flex align-items-center">
                                        @if($cat->image)
                                            <img src="{{ $cat->image }}" alt="" style="width:20px;height:20px;object-fit:cover;margin-right:8px;border-radius:4px;">
                                        @endif
                                        {{ $cat->name }}
                                    </a>
                                </h6>
                                <ul class="list-unstyled ps-0" style="font-size: 0.85rem;">
                                    @php
                                        $topProducts = $cat->products()->where('is_active', true)->take(5)->get();
                                    @endphp
                                    @foreach($topProducts as $prod)
                                        <li class="mb-2">
                                            <a href="{{ route('products.show', $prod->slug) }}" class="text-muted text-decoration-none hover-link">
                                                {{ Str::limit($prod->name, 28) }}
                                            </a>
                                        </li>
                                    @endforeach
                                    <li class="mt-2">
                                        <a href="{{ route('products.index', ['category' => $cat->slug]) }}" class="text-primary text-decoration-none fw-bold small">
                                            View all results <i class="bi bi-chevron-right small"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </nav>

    {{-- Full-Page Menu Backdrop --}}
    <div class="menu-backdrop-overlay" id="menuBackdrop"></div>

    {{-- Mobile Off-Canvas Menu --}}
    {{-- Mobile Off-Canvas Menu (Premium Redesign) --}}
    <div class="offcanvas offcanvas-end mobile-offcanvas" id="mobileMenu" tabindex="-1">
        <div class="offcanvas-header border-bottom border-white border-opacity-10 py-4">
            <div class="d-flex align-items-center gap-3">
                @auth
                    <div class="user-avatar-mini bg-primary bg-opacity-20 text-primary border border-primary border-opacity-25 shadow-sm">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold text-white small">Hello, {{ explode(' ', auth()->user()->name)[0] }}</h6>
                        <span class="text-white-50 x-small">Welcome back</span>
                    </div>
                @else
                    <div class="user-avatar-mini bg-white bg-opacity-10 text-white">
                        <i class="bi bi-person"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold text-white small">Guest User</h6>
                        <a href="{{ route('login') }}" class="text-primary text-decoration-none x-small fw-bold">Login / Sign Up</a>
                    </div>
                @endauth
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0">
            {{-- Mobile Search --}}
            <div class="px-4 py-3">
                <form action="{{ route('products.index') }}" method="GET" class="mobile-search-form">
                    <div class="input-group bg-white bg-opacity-10 rounded-pill overflow-hidden border border-white border-opacity-10">
                        <span class="input-group-text bg-transparent border-0 text-white-50 ps-3">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control bg-transparent border-0 text-white small py-2 ps-1" placeholder="Search products...">
                    </div>
                </form>
            </div>

            <div class="mobile-nav-groups px-4 pb-5">
                {{-- Main Links --}}
                <div class="nav-group mb-4">
                    <h6 class="nav-group-title">Main Menu</h6>
                    <ul class="nav flex-column gap-2 mt-2">
                        <li><a class="mobile-nav-link" href="{{ route('home') }}"><i class="bi bi-house-door"></i>Home</a></li>
                        <li><a class="mobile-nav-link" href="{{ route('products.index') }}"><i class="bi bi-grid-fill"></i>All Products</a></li>
                        <li><a class="mobile-nav-link" href="{{ route('offers') }}"><i class="bi bi-brightness-high-fill text-warning"></i>Hot Offers</a></li>
                    </ul>
                </div>

                {{-- Shopping --}}
                <div class="nav-group mb-4">
                    <h6 class="nav-group-title">Shopping Tools</h6>
                    <ul class="nav flex-column gap-2 mt-2">
                        @auth
                            <li><a class="mobile-nav-link" href="{{ route('cart.index') }}"><i class="bi bi-bag-check"></i>My Cart</a></li>
                            <li><a class="mobile-nav-link" href="{{ route('wishlists.index') }}"><i class="bi bi-heart"></i>Wishlist</a></li>
                            <li><a class="mobile-nav-link" href="{{ route('orders.index') }}"><i class="bi bi-receipt"></i>My Orders</a></li>
                        @else
                            <li><a class="mobile-nav-link" href="{{ route('login') }}"><i class="bi bi-bag"></i>View Cart</a></li>
                        @endauth
                    </ul>
                </div>

                {{-- Categories Section --}}
                <div class="nav-group mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="nav-group-title">Top Categories</h6>
                        <a href="{{ route('products.index') }}" class="text-primary x-small text-decoration-none fw-bold">See All</a>
                    </div>
                    <div class="mobile-cat-grid mt-3">
                        @foreach($globalCategories->take(6) as $cat)
                            <a href="{{ route('products.index', ['category' => $cat->slug]) }}" class="mobile-cat-pill">
                                @if($cat->image)
                                    <img src="{{ $cat->image }}" alt="">
                                @else
                                    <div class="pill-icon-fallback"><i class="bi bi-tag"></i></div>
                                @endif
                                <span>{{ $cat->name }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Account & Settings --}}
                <div class="nav-group mt-5 border-top border-white border-opacity-10 pt-4">
                    <ul class="nav flex-column gap-2">
                        @auth
                            <li><a class="mobile-nav-link" href="{{ route('profile.edit') }}"><i class="bi bi-person-gear"></i>Settings</a></li>
                            @if(auth()->user()->isAdmin())
                                <li><a class="mobile-nav-link text-warning" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i>Admin Dashboard</a></li>
                            @endif
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="mobile-nav-link w-100 text-start border-0 bg-transparent text-danger">
                                        <i class="bi bi-box-arrow-right"></i>Sign Out
                                    </button>
                                </form>
                            </li>
                        @else
                            <li><a class="mobile-nav-link" href="{{ route('login') }}"><i class="bi bi-shield-lock"></i>Sign In</a></li>
                        @endauth
                    </ul>
                </div>
            </div>
        </div>
        <div class="px-4 py-3 border-top border-white border-opacity-10">
            <button class="btn btn-outline-light w-100 rounded-pill d-flex align-items-center justify-content-center gap-2" id="mobileThemeToggle">
                <i class="bi bi-moon-stars"></i>
                <span id="mobileThemeText">Switch Theme</span>
            </button>
        </div>
    </div>

    {{-- Mobile Search Modal --}}
    <div class="modal fade" id="searchModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background:var(--ps-gradient-dark);border:none;">
                <div class="modal-body p-4">
                    <div class="search-container position-relative">
                        <input type="text" id="mobileSearchInput" class="form-control search-input"
                            placeholder="Search products..." autocomplete="off">
                        <button class="search-btn"><i class="bi bi-search"></i></button>
                        <div id="mobileSearchSuggestions" class="search-suggestions"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="container mt-3">
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    {{-- Page Content --}}
    <main class="page-enter">
        @yield('content')
    </main>

    {{-- Newsletter Section --}}
    @if(request()->routeIs('home'))
    <section class="newsletter-section">
        <div class="container">
            <div class="newsletter-card">
                <div class="newsletter-glow"></div>
                <div class="row align-items-center g-4">
                    <div class="col-lg-5">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-envelope-heart-fill text-primary fs-4"></i>
                            <span class="badge rounded-pill" style="background: rgba(108,92,231,0.2); color: #A29BFE; font-size: 0.75rem;">Newsletter</span>
                        </div>
                        <h3 class="fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">Stay in the loop</h3>
                        <p class="text-muted small mb-0">Get exclusive offers, new arrivals, and special discounts delivered to your inbox.</p>
                    </div>
                    <div class="col-lg-7">
                        <form id="newsletterForm" class="newsletter-form">
                            <div class="input-group newsletter-input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control" placeholder="Enter your email address" required id="newsletterEmail">
                                <button type="submit" class="btn btn-newsletter" id="newsletterBtn">
                                    <span class="newsletter-btn-text">Subscribe</span>
                                    <i class="bi bi-arrow-right ms-1"></i>
                                </button>
                            </div>
                            <div id="newsletterMsg" class="newsletter-msg mt-2 small" style="display:none;"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- Footer --}}
    <footer class="footer-premium">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <div class="footer-brand">🛍️ Premier Shop</div>
                    <p class="mb-4" style="font-size:0.9rem;">Your one-stop destination for quality products at
                        unbeatable prices.</p>
                    <div class="social-icons">
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-twitter-x"></i></a>
                        <a href="#"><i class="bi bi-instagram"></i></a>
                        <a href="#"><i class="bi bi-tiktok"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 mb-4 mb-lg-0">
                    <h6 class="footer-heading">Shop</h6>
                    <ul class="footer-links">
                        <li><a href="{{ route('offers') }}">Offers</a></li>
                        <li><a href="{{ route('products.index') }}">All Products</a></li>
                        <li><a href="{{ route('categories') }}">Categories</a></li>
                        @foreach(\App\Models\Category::take(3)->get() as $cat)
                            <li><a href="{{ route('products.index', ['category' => $cat->slug]) }}">{{ $cat->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-lg-2 col-md-3 mb-4 mb-lg-0">
                    <h6 class="footer-heading">Account</h6>
                    <ul class="footer-links">
                        <li><a href="{{ route('login') }}">Login</a></li>
                        <li><a href="{{ route('register') }}">Sign Up</a></li>
                        <li><a href="{{ route('contact') }}">Contact Us</a></li>
                        @auth
                            <li><a href="{{ route('orders.index') }}">My Orders</a></li>
                            <li><a href="{{ route('cart.index') }}">My Cart</a></li>
                        @endauth
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
                    <h6 class="footer-heading">Shop Hours</h6>
                     <ul class="footer-links" style="font-size:0.85rem;">
                        @php
                            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                            $settings = \App\Models\Setting::first();
                            $shopHours = $settings->other_settings['shop_hours'] ?? [];
                        @endphp
                        @foreach($days as $day)
                            @php
                                $hours = $shopHours[$day] ?? [];
                                $open = $hours['open'] ?? '';
                                $close = $hours['close'] ?? '';
                                $isClosed = $hours['closed'] ?? false;
                            @endphp
                            <li class="d-flex justify-content-between mb-1">
                                <span class="text-capitalize text-muted">{{ substr($day, 0, 3) }}:</span>
                                <span>
                                    @if($isClosed || (!$open && !$close))
                                        <span class="text-danger">Closed</span>
                                    @else
                                        {{ $open ? \Carbon\Carbon::parse($open)->format('H:i') : '' }} - {{ $close ? \Carbon\Carbon::parse($close)->format('H:i') : '' }}
                                    @endif
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0 text-md-start text-center">
                    <h6 class="footer-heading">Get In Touch</h6>
                    <ul class="footer-links list-unstyled">
                        <li class="mb-2"><i class="bi bi-geo-alt me-2 text-primary"></i>London, United Kingdom</li>
                        <li class="mb-2"><i class="bi bi-envelope me-2 text-primary"></i>info@premiershop.com</li>
                        <li class="mb-2"><i class="bi bi-telephone me-2 text-primary"></i>+44 770 000 0000</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom text-center">
                &copy; {{ date('Y') }} Premier Shop. All rights reserved.
            </div>
        </div>
    </footer>

    {{-- Back to Top --}}
    <button id="backToTop" class="btn btn-primary"
        style="display:none;position:fixed;bottom:30px;right:30px;width:48px;height:48px;border-radius:50%;align-items:center;justify-content:center;z-index:999;box-shadow:var(--ps-shadow-lg);">
        <i class="bi bi-arrow-up"></i>
    </button>

    @push('scripts')
    <script>
    document.addEventListener('submit', function(e) {
        const form = e.target;
        
        // Wishlist Toggle Handler
        if (form && form.action && form.action.includes('/wishlists/') && form.action.includes('/toggle')) {
            e.preventDefault();
            
            const btn = form.querySelector('button');
            if(!btn) return;
            
            const icon = btn.querySelector('i');
            btn.disabled = true;

            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                if (data.success) {
                    if (window.location.pathname.includes('/wishlists')) {
                        const cardParent = form.closest('.product-card').parentElement;
                        if (cardParent) {
                            cardParent.style.transition = 'all 0.4s ease';
                            cardParent.style.opacity = '0';
                            cardParent.style.transform = 'translateY(20px)';
                            setTimeout(() => cardParent.remove(), 400);
                        } else {
                            window.location.reload();
                        }
                    } else {
                        if (data.status === 'added') {
                            if(icon.classList.contains('bi-heart')) icon.classList.remove('bi-heart');
                            icon.classList.add('bi-heart-fill', 'text-danger');
                            btn.title = 'Remove from wishlist';
                            showToast('Added to wishlist', 'bg-success');
                        } else {
                            if(icon.classList.contains('bi-heart-fill')) icon.classList.remove('bi-heart-fill');
                            if(icon.classList.contains('text-danger')) icon.classList.remove('text-danger');
                            icon.classList.add('bi-heart');
                            btn.title = 'Add to wishlist';
                            showToast('Removed from wishlist', 'bg-info');
                        }
                    }
                } else {
                    showToast(data.message || 'Error updating wishlist.', 'bg-danger');
                }
            })
            .catch(err => {
                btn.disabled = false;
                console.error(err);
                showToast('Something went wrong.', 'bg-danger');
            });
            return;
        }

        // Global AJAX Form Handler (Cart, Coupons, etc.)
        if (form && form.classList.contains('ajax-form')) {
            e.preventDefault();
            
            const btn = form.querySelector('button[type="submit"]');
            const originalBtnHtml = btn ? btn.innerHTML : null;
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
            }

            const formData = new FormData(form);
            
            fetch(form.action, {
                method: form.method || 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(res => res.json())
            .then(data => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalBtnHtml;
                }

                if (data.success) {
                    showToast(data.message, 'bg-success');
                    
                    // Update Cart Badge globally
                    if (data.cartCount !== undefined) {
                        document.querySelectorAll('.cart-count-badge').forEach(el => {
                            el.textContent = data.cartCount;
                            el.style.display = data.cartCount > 0 ? 'inline-block' : 'none';
                        });
                    }

                    // Custom Event for page-specific logic
                    const event = new CustomEvent('ajax-form-success', { detail: { form, data } });
                    document.dispatchEvent(event);
                    
                    // Clear input if needed
                    if (form.dataset.clearOnSuccess) {
                        form.reset();
                    }
                } else {
                    showToast(data.message || 'An error occurred.', 'bg-danger');
                }
            })
            .catch(err => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalBtnHtml;
                }
                if (err.response && err.response.status === 401) {
                    window.location.href = '/login';
                    return;
                }
                console.error(err);
                showToast('Something went wrong. Please try again.', 'bg-danger');
            });
        }
    });

    function showToast(message, bgColor) {
        const toastEl = document.getElementById('liveToast');
        const toastMessage = document.getElementById('toastMessage');
        const toast = new bootstrap.Toast(toastEl);
        
        toastEl.className = `toast align-items-center text-white border-0 ${bgColor}`;
        toastMessage.textContent = message;
        toast.show();
    }
    </script>
    @endpush
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const navbar = document.querySelector('.navbar-premium');
            const themeToggle = document.getElementById('themeToggle');
            const mobileThemeToggle = document.getElementById('mobileThemeToggle');
            const htmlElement = document.documentElement;
            const themeIcon = themeToggle.querySelector('i');
            const mobileThemeIcon = mobileThemeToggle.querySelector('i');
            const mobileThemeText = document.getElementById('mobileThemeText');

            function updateThemeUI(theme) {
                if (theme === 'dark') {
                    themeIcon.className = 'bi bi-sun';
                    mobileThemeIcon.className = 'bi bi-sun';
                    mobileThemeText.textContent = 'Light Mode';
                } else {
                    themeIcon.className = 'bi bi-moon-stars';
                    mobileThemeIcon.className = 'bi bi-moon-stars';
                    mobileThemeText.textContent = 'Dark Mode';
                }
            }

            const currentTheme = htmlElement.getAttribute('data-bs-theme');
            updateThemeUI(currentTheme);

            function toggleTheme() {
                const newTheme = htmlElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
                htmlElement.setAttribute('data-bs-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                updateThemeUI(newTheme);
            }

            themeToggle.addEventListener('click', toggleTheme);
            if(mobileThemeToggle) mobileThemeToggle.addEventListener('click', toggleTheme);
            
            window.addEventListener('scroll', () => {
                if (window.scrollY > 20) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });

            // Newsletter AJAX
            const nlForm = document.getElementById('newsletterForm');
            if (nlForm) {
                nlForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const btn = document.getElementById('newsletterBtn');
                    const msgDiv = document.getElementById('newsletterMsg');
                    const email = document.getElementById('newsletterEmail').value;
                    
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';
                    
                    fetch('{{ route("newsletter.subscribe") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ email: email })
                    })
                    .then(r => r.json())
                    .then(data => {
                        btn.disabled = false;
                        btn.innerHTML = '<span class="newsletter-btn-text">Subscribe</span><i class="bi bi-arrow-right ms-1"></i>';
                        msgDiv.style.display = 'block';
                        if (data.success) {
                            msgDiv.className = 'newsletter-msg mt-2 small text-success';
                            msgDiv.textContent = data.message;
                            nlForm.reset();
                        } else {
                            msgDiv.className = 'newsletter-msg mt-2 small text-warning';
                            msgDiv.textContent = data.message;
                        }
                        setTimeout(() => { msgDiv.style.display = 'none'; }, 5000);
                    })
                    .catch(() => {
                        btn.disabled = false;
                        btn.innerHTML = '<span class="newsletter-btn-text">Subscribe</span><i class="bi bi-arrow-right ms-1"></i>';
                        msgDiv.style.display = 'block';
                        msgDiv.className = 'newsletter-msg mt-2 small text-danger';
                        msgDiv.textContent = 'Something went wrong. Please try again.';
                    });
                });
            }
        });
    </script>
    @stack('scripts')
</body>

</html>