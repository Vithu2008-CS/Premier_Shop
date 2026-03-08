<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Premier Shop — Your one-stop destination for quality products at unbeatable prices.">
    <title>@yield('title', 'Premier Shop — Quality Products')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
    {{-- Top Bar --}}
    <div class="top-bar d-none d-md-block">
        <div class="container d-flex justify-content-between align-items-center">
            <span><i class="bi bi-truck me-1"></i> Free delivery on orders over £50</span>
            <div class="d-flex gap-3">
                <a href="#"><i class="bi bi-telephone me-1"></i> +44 770 000 0000</a>
                <a href="#"><i class="bi bi-envelope me-1"></i> info@premiershop.com</a>
            </div>
        </div>
    </div>

    {{-- Main Navbar --}}
    <nav class="navbar navbar-expand-lg navbar-premium sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="bi bi-shop"></i> Premier Shop
            </a>

            {{-- Search Bar - Center --}}
            <div class="search-wrapper d-none d-lg-block flex-grow-1 mx-4">
                <div class="search-container position-relative">
                    <input type="text" id="searchInput" class="form-control search-input" placeholder="Search products..." autocomplete="off">
                    <button class="search-btn"><i class="bi bi-search"></i></button>
                    <div id="searchSuggestions" class="search-suggestions"></div>
                </div>
            </div>

            {{-- Mobile Icons --}}
            <div class="d-flex align-items-center gap-2 d-lg-none">
                <button class="btn btn-link text-white p-2" data-bs-toggle="modal" data-bs-target="#searchModal">
                    <i class="bi bi-search fs-5"></i>
                </button>
                @auth
                    <a href="{{ route('cart.index') }}" class="btn btn-link text-white p-2 cart-badge">
                        <i class="bi bi-bag fs-5"></i>
                        @php $cartCount = \App\Models\Cart::where('user_id', auth()->id())->first()?->totalItems ?? 0; @endphp
                        @if($cartCount > 0)<span class="badge">{{ $cartCount }}</span>@endif
                    </a>
                @endauth
                <button class="navbar-toggler border-0 p-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
                    <i class="bi bi-list text-white fs-4"></i>
                </button>
            </div>

            {{-- Desktop Nav --}}
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-1">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('offers') ? 'active' : '' }}" href="{{ route('offers') }}">
                            <i class="bi bi-tag me-1"></i>Offers
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">Products</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Categories</a>
                        <ul class="dropdown-menu">
                            @foreach(\App\Models\Category::all() as $cat)
                                <li><a class="dropdown-item" href="{{ route('products.index', ['category' => $cat->slug]) }}">{{ $cat->name }}</a></li>
                            @endforeach
                        </ul>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a class="nav-link cart-badge" href="{{ route('cart.index') }}">
                                <i class="bi bi-bag"></i> Cart
                                @if($cartCount ?? 0 > 0)<span class="badge">{{ $cartCount }}</span>@endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('orders.index') }}"><i class="bi bi-receipt"></i> Orders</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @if(auth()->user()->isAdmin())
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Admin Panel</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                @endif
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-gear me-2"></i>Profile</a></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
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

    {{-- Mobile Off-Canvas Menu --}}
    <div class="offcanvas offcanvas-end" id="mobileMenu" style="background:var(--ps-gradient-dark);color:#fff;">
        <div class="offcanvas-header border-bottom border-secondary">
            <h5 class="offcanvas-title gradient-text fw-bold">Premier Shop</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="nav flex-column gap-1">
                <li><a class="nav-link text-white" href="{{ route('offers') }}"><i class="bi bi-tag me-2"></i>Offers</a></li>
                <li><a class="nav-link text-white" href="{{ route('products.index') }}"><i class="bi bi-grid me-2"></i>Products</a></li>
                @foreach(\App\Models\Category::all() as $cat)
                    <li><a class="nav-link text-white-50 ps-4" href="{{ route('products.index', ['category' => $cat->slug]) }}">{{ $cat->name }}</a></li>
                @endforeach
                @auth
                    <li><hr class="border-secondary"></li>
                    <li><a class="nav-link text-white" href="{{ route('cart.index') }}"><i class="bi bi-bag me-2"></i>Cart</a></li>
                    <li><a class="nav-link text-white" href="{{ route('orders.index') }}"><i class="bi bi-receipt me-2"></i>Orders</a></li>
                    <li><a class="nav-link text-white" href="{{ route('profile.edit') }}"><i class="bi bi-gear me-2"></i>Profile</a></li>
                    @if(auth()->user()->isAdmin())
                        <li><a class="nav-link text-warning" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Admin Panel</a></li>
                    @endif
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="nav-link text-danger border-0 bg-transparent"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                        </form>
                    </li>
                @else
                    <li><hr class="border-secondary"></li>
                    <li><a class="nav-link text-white" href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right me-2"></i>Login</a></li>
                    <li><a class="nav-link text-primary" href="{{ route('register') }}"><i class="bi bi-person-plus me-2"></i>Sign Up</a></li>
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
                        <input type="text" id="mobileSearchInput" class="form-control search-input" placeholder="Search products..." autocomplete="off">
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
                <div class="col-lg-4">
                    <div class="footer-brand">🛍️ Premier Shop</div>
                    <p class="mb-4" style="font-size:0.9rem;">Your one-stop destination for quality products at unbeatable prices.</p>
                    <div class="social-icons">
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-twitter-x"></i></a>
                        <a href="#"><i class="bi bi-instagram"></i></a>
                        <a href="#"><i class="bi bi-tiktok"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h6 class="footer-heading">Shop</h6>
                    <ul class="footer-links">
                        <li><a href="{{ route('offers') }}">Offers</a></li>
                        <li><a href="{{ route('products.index') }}">All Products</a></li>
                        @foreach(\App\Models\Category::take(4)->get() as $cat)
                            <li><a href="{{ route('products.index', ['category' => $cat->slug]) }}">{{ $cat->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-lg-2 col-md-4">
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
                <div class="col-lg-4 col-md-4">
                    <h6 class="footer-heading">Get In Touch</h6>
                    <ul class="footer-links">
                        <li><i class="bi bi-geo-alt me-2 text-primary"></i>London, United Kingdom</li>
                        <li><i class="bi bi-envelope me-2 text-primary"></i>info@premiershop.com</li>
                        <li><i class="bi bi-telephone me-2 text-primary"></i>+44 770 000 0000</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom text-center">
                &copy; {{ date('Y') }} Premier Shop. All rights reserved.
            </div>
        </div>
    </footer>

    {{-- Back to Top --}}
    <button id="backToTop" class="btn btn-primary" style="display:none;position:fixed;bottom:30px;right:30px;width:48px;height:48px;border-radius:50%;align-items:center;justify-content:center;z-index:999;box-shadow:var(--ps-shadow-lg);">
        <i class="bi bi-arrow-up"></i>
    </button>

    @stack('scripts')
</body>
</html>
