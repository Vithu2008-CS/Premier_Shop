<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description"
        content="Premier Shop — Your one-stop destination for quality products at unbeatable prices.">
    <title>@yield('title', 'Premier Shop — Quality Products')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @stack('styles')
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
                        <a class="nav-link {{ request('category') ? 'active' : '' }}" href="#categoryMegaMenu"
                            data-bs-toggle="collapse" role="button">
                            <i class="bi bi-grid-3x3-gap me-1"></i>Categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('products.*') && !request('category') ? 'active' : '' }}"
                            href="{{ route('products.index') }}">Products</a>
                    </li>
                    @endif
                    @auth
                        @if(!auth()->user()->isDriver())
                        <li class="nav-item">
                            <a class="nav-link cart-badge" href="{{ route('cart.index') }}">
                                <i class="bi bi-bag"></i> Cart
                                <span class="badge cart-count-badge" id="cartCountBadge" style="{{ ($cartCount ?? 0) > 0 ? '' : 'display:none;' }}">{{ $cartCount ?? 0 }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('wishlists.index') }}"><i class="bi bi-heart"></i> Wishlist</a>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link" href="{{ auth()->user()->isDriver() ? route('driver.dashboard') : route('orders.index') }}">
                                <i class="bi bi-receipt"></i> 
                                {{ auth()->user()->isDriver() ? 'My Deliveries' : 'Orders' }}
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @if(auth()->user()->isAdmin())
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i
                                                class="bi bi-speedometer2 me-2"></i>Admin Panel</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
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
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                        <li class="nav-item"><a class="nav-link btn-signup" href="{{ route('register') }}">Sign Up</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    {{-- Category Mega Menu --}}
    @if(!auth()->user()?->isDriver())
    <div class="collapse category-mega-menu sticky-top" id="categoryMegaMenu" style="top: 72px; z-index: 1030;">
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

    {{-- Mobile Off-Canvas Menu --}}
    <div class="offcanvas offcanvas-end" id="mobileMenu" style="background:var(--ps-gradient-dark);color:#fff;">
        <div class="offcanvas-header border-bottom border-secondary">
            <h5 class="offcanvas-title gradient-text fw-bold">Premier Shop</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="nav flex-column gap-1">
                @if(!auth()->user()?->isDriver())
                <li><a class="nav-link text-white" href="{{ route('offers') }}"><i class="bi bi-tag me-2"></i>Offers</a>
                </li>
                <li><a class="nav-link text-white" href="{{ route('products.index') }}"><i
                            class="bi bi-grid me-2"></i>Products</a></li>
                @foreach(\App\Models\Category::all() as $cat)
                    <li>
                        <a class="nav-link text-white-50 ps-4 d-flex align-items-center"
                            href="{{ route('products.index', ['category' => $cat->slug]) }}">
                            @if($cat->image)
                                <img src="{{ $cat->image }}" alt=""
                                    style="width: 18px; height: 18px; object-fit: cover; border-radius: 4px; margin-right: 8px;">
                            @else
                                <i class="bi bi-tag me-2" style="font-size: 0.9rem;"></i>
                            @endif
                            {{ $cat->name }}
                        </a>
                    </li>
                @endforeach
                @endif
                @auth
                    <li>
                        <hr class="border-secondary">
                    </li>
                    @if(!auth()->user()->isDriver())
                    <li><a class="nav-link text-white" href="{{ route('cart.index') }}"><i
                                class="bi bi-bag me-2"></i>Cart</a></li>
                    @endif
                    <li><a class="nav-link text-white" href="{{ auth()->user()->isDriver() ? route('driver.dashboard') : route('orders.index') }}">
                        <i class="bi bi-receipt me-2"></i>{{ auth()->user()->isDriver() ? 'My Deliveries' : 'Orders' }}</a></li>
                    @if(!auth()->user()->isDriver())
                    <li><a class="nav-link text-white" href="{{ route('wishlists.index') }}"><i
                                class="bi bi-heart me-2"></i>Wishlist</a></li>
                    @endif
                    <li><a class="nav-link text-white" href="{{ route('profile.edit') }}"><i
                                class="bi bi-gear me-2"></i>Profile</a></li>
                    @if(auth()->user()->isAdmin())
                        <li><a class="nav-link text-warning" href="{{ route('admin.dashboard') }}"><i
                                    class="bi bi-speedometer2 me-2"></i>Admin Panel</a></li>
                    @endif
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="nav-link text-danger border-0 bg-transparent"><i
                                    class="bi bi-box-arrow-right me-2"></i>Logout</button>
                        </form>
                    </li>
                @else
                    <li>
                        <hr class="border-secondary">
                    </li>
                    <li><a class="nav-link text-white" href="{{ route('login') }}"><i
                                class="bi bi-box-arrow-in-right me-2"></i>Login</a></li>
                    <li><a class="nav-link text-primary" href="{{ route('register') }}"><i
                                class="bi bi-person-plus me-2"></i>Sign Up</a></li>
                @endauth
            </ul>
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
                        @foreach(\App\Models\Category::take(4)->get() as $cat)
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
            
            window.addEventListener('scroll', () => {
                if (window.scrollY > 20) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });
        });
    </script>
    @stack('scripts')
</body>

</html>