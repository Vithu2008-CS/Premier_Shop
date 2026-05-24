{{--
    layouts/app.blade.php — Main storefront layout
    ================================================
    Used by all customer-facing pages (home, products, cart, checkout, orders, profile, etc.)

    Provides:
     - Head: meta tags, OG/Twitter cards, Vite assets (Bootstrap + app.js), dark/light theme pre-render
     - Sticky navbar: search bar, category mega-menu, cart badge, wishlist, notification dropdown,
       user dropdown (admin link if admin role), login/signup for guests, dark mode toggle
     - Mobile: off-canvas menu, search modal
     - Flash messages: session('success') and session('error') alert banners
     - @yield('content') — page-specific content slot
     - Newsletter section (shown only on home page)
     - Footer: shop links, account links, trading hours from settings, social links
     - Back-to-top scroll-progress circle
     - Global JS: wishlist AJAX toggle, ajax-form handler, cart badge sync, toast helper,
       theme persist, notification bell fetch, newsletter AJAX
     - @stack('scripts') — page scripts injected here
--}}
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"></noscript>
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    @stack('seo')
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @stack('styles')
    
    <style>
        /* Premium Mobile Bottom Dock (App-Like Sticky Nav) */
        .mobile-bottom-dock {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(25px) saturate(200%);
            -webkit-backdrop-filter: blur(25px) saturate(200%);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 28px;
            padding: 10px 16px;
            box-shadow: 0 15px 35px rgba(108, 92, 231, 0.08), 0 5px 15px rgba(0, 0, 0, 0.03);
            z-index: 1050;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        
        [data-bs-theme="dark"] .mobile-bottom-dock {
            background: rgba(15, 14, 23, 0.75);
            border-color: rgba(255, 255, 255, 0.06);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5), inset 0 1px 1px rgba(255, 255, 255, 0.1);
        }
        
        .dock-wrapper {
            width: 100%;
        }
        
        .dock-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #8E8E93;
            text-decoration: none;
            font-size: 0.7rem;
            font-weight: 600;
            font-family: 'Outfit', sans-serif;
            padding: 8px 12px;
            border-radius: 20px;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            gap: 2px;
            position: relative;
        }
        
        .dock-item i {
            font-size: 1.3rem;
            line-height: 1;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            color: #8E8E93;
        }
        
        .dock-icon-wrapper {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 4px;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        
        .dock-item:hover, .dock-item.active {
            color: #6C5CE7;
        }
        
        [data-bs-theme="dark"] .dock-item:hover, [data-bs-theme="dark"] .dock-item.active {
            color: #A29BFE;
        }
        
        .dock-item:hover i, .dock-item.active i {
            color: #6C5CE7;
        }
        
        [data-bs-theme="dark"] .dock-item:hover i, [data-bs-theme="dark"] .dock-item.active i {
            color: #A29BFE;
            text-shadow: 0 0 10px rgba(162, 155, 254, 0.5);
        }
        
        .dock-item.active .dock-icon-wrapper {
            transform: scale(1.2) translateY(-4px);
        }
        
        /* Subtle glowing dot beneath active item */
        .dock-item.active::after {
            content: '';
            position: absolute;
            bottom: 2px;
            width: 5px;
            height: 5px;
            background-color: #6C5CE7;
            border-radius: 50%;
            box-shadow: 0 0 8px #6C5CE7;
            transition: all 0.3s ease;
        }
        
        [data-bs-theme="dark"] .dock-item.active::after {
            background-color: #A29BFE;
            box-shadow: 0 0 8px #A29BFE;
        }
        
        /* Beautiful Premium Footer Enhancements */
        .footer-premium {
            position: relative;
            background: linear-gradient(180deg, #0b0a11 0%, #040307 100%) !important;
            border-top: 1px solid rgba(162, 155, 254, 0.08) !important;
            overflow: hidden;
            padding: 80px 0 0 !important;
            font-family: 'Inter', sans-serif;
        }
        
        .footer-premium::before {
            content: '';
            position: absolute;
            top: -200px;
            left: 50%;
            transform: translateX(-50%);
            width: 600px;
            height: 400px;
            background: radial-gradient(circle, rgba(162, 155, 254, 0.08) 0%, rgba(162, 155, 254, 0) 70%);
            pointer-events: none;
            z-index: 0;
        }
        
        .footer-heading {
            position: relative;
            padding-bottom: 12px;
            margin-bottom: 24px;
            font-family: 'Outfit', sans-serif;
            letter-spacing: 0.5px;
            font-weight: 700;
            color: #ffffff !important;
            text-transform: uppercase;
            font-size: 0.9rem;
        }
        
        .footer-heading::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 35px;
            height: 2.5px;
            background: linear-gradient(90deg, #6C5CE7, #A29BFE);
            border-radius: 5px;
        }
        
        .footer-links {
            list-style: none;
            padding-left: 0;
        }
        
        .footer-links li {
            margin-bottom: 12px;
        }
        
        .footer-links li a {
            position: relative;
            padding-left: 0;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            display: inline-block;
            color: rgba(255, 255, 255, 0.6) !important;
            text-decoration: none;
            font-size: 0.88rem;
        }
        
        .footer-links li a:hover {
            padding-left: 12px;
            color: #A29BFE !important;
        }
        
        .footer-links li a::before {
            content: '→';
            position: absolute;
            left: -12px;
            opacity: 0;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            color: #A29BFE;
            font-weight: bold;
        }
        
        .footer-links li a:hover::before {
            left: 0px;
            opacity: 1;
        }
        
        .footer-brand {
            font-family: 'Outfit', sans-serif;
            font-size: 1.6rem;
            font-weight: 900;
            background: linear-gradient(135deg, #ffffff 30%, #A29BFE 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 16px;
        }
        
        .social-icons {
            display: flex;
            gap: 12px;
        }
        
        .social-icons a {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255, 255, 255, 0.7) !important;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        
        .social-icons a:hover {
            background: rgba(162, 155, 254, 0.15);
            border-color: rgba(162, 155, 254, 0.3);
            color: #A29BFE !important;
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(162, 155, 254, 0.2);
        }
        
        .footer-bottom {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 20px;
            padding: 30px !important;
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.04);
            margin-top: 60px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        
        /* Mobile Categories Popover Drawer */
        .mobile-categories-drawer {
            position: fixed;
            bottom: -100%;
            left: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(25px) saturate(200%);
            -webkit-backdrop-filter: blur(25px) saturate(200%);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 24px;
            padding: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
            z-index: 1045;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            max-height: 50vh;
            overflow-y: auto;
        }
        
        [data-bs-theme="dark"] .mobile-categories-drawer {
            background: rgba(20, 19, 30, 0.95);
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 15px 45px rgba(0, 0, 0, 0.6);
        }
        
        .mobile-categories-drawer.show {
            bottom: 95px;
        }
        
        .category-drawer-tile {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(0, 0, 0, 0.02);
            border: 1px solid rgba(0, 0, 0, 0.05);
            padding: 10px 12px;
            border-radius: 16px;
            transition: all 0.2s ease;
        }
        
        [data-bs-theme="dark"] .category-drawer-tile {
            background: rgba(255, 255, 255, 0.03);
            border-color: rgba(255, 255, 255, 0.05);
        }
        
        .category-drawer-tile:hover {
            background: rgba(108, 92, 231, 0.08);
            border-color: rgba(108, 92, 231, 0.2);
        }
        
        [data-bs-theme="dark"] .category-drawer-tile:hover {
            background: rgba(162, 155, 254, 0.15);
            border-color: rgba(162, 155, 254, 0.3);
        }
        
        .category-drawer-tile .tile-icon {
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: rgba(108, 92, 231, 0.1);
            color: #6C5CE7;
        }
        
        [data-bs-theme="dark"] .category-drawer-tile .tile-icon {
            background: rgba(162, 155, 254, 0.15);
            color: #A29BFE;
        }
        
        .category-drawer-tile .tile-icon img {
            width: 18px;
            height: 18px;
            object-fit: contain;
        }
        
        .category-drawer-tile .tile-name {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--bs-body-color);
            font-family: 'Outfit', sans-serif;
        }
        
        .btn-close-custom {
            background: none;
            border: none;
            color: var(--bs-body-color);
            font-size: 1.1rem;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s;
        }
        
        .btn-close-custom:hover {
            color: #6C5CE7;
        }
        
        /* Compact Milestone & Trust cards on mobile */
        @media (max-width: 576px) {
            .milestone-section {
                padding: 40px 0 !important;
            }
            .milestone-card {
                padding: 15px 10px !important;
                border-radius: 16px !important;
            }
            .milestone-card .milestone-icon {
                width: 40px !important;
                height: 40px !important;
                font-size: 1.2rem !important;
                margin-bottom: 8px !important;
            }
            .milestone-card .counter-num {
                font-size: 1.3rem !important;
                margin-bottom: 4px !important;
            }
            .milestone-card h6 {
                font-size: 0.75rem !important;
                margin-bottom: 2px !important;
            }
            .milestone-card p {
                font-size: 0.6rem !important;
                line-height: 1.2 !important;
            }
            
            .trust-bar-modern {
                padding: 30px 0 !important;
            }
            .trust-card {
                padding: 12px 8px !important;
                border-radius: 16px !important;
            }
            .trust-card-icon {
                width: 36px !important;
                height: 36px !important;
                font-size: 1.1rem !important;
                margin-bottom: 6px !important;
            }
            .trust-card h6 {
                font-size: 0.75rem !important;
                margin-bottom: 2px !important;
            }
            .trust-card small {
                font-size: 0.6rem !important;
                line-height: 1.2 !important;
            }
            
            /* Neat single-row footer column settings */
            .footer-premium {
                padding: 50px 0 0 !important;
            }
            .footer-heading {
                font-size: 0.75rem !important;
                margin-bottom: 12px !important;
                padding-bottom: 6px !important;
            }
            .footer-heading::after {
                width: 20px !important;
                height: 1.5px !important;
            }
            .footer-links li {
                margin-bottom: 6px !important;
            }
            .footer-links li a {
                font-size: 0.72rem !important;
            }
            .footer-links li a:hover {
                padding-left: 6px !important;
            }
            .footer-links li a::before {
                left: -8px !important;
            }
            .footer-bottom {
                margin-top: 30px !important;
                padding: 20px !important;
            }
        }

        /* Layout Padding Offset for Sticky Dock */
        body {
            padding-bottom: 95px !important;
        }
        
        @media (min-width: 992px) {
            body {
                padding-bottom: 0 !important;
            }
        }

        /* Avatar Upload Premium Styles */
        .avatar-upload-wrapper {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            cursor: pointer;
            overflow: hidden;
            position: relative;
        }
        
        .avatar-preview-container {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            overflow: hidden;
            background: #f8f9fa;
        }
        
        [data-bs-theme="dark"] .avatar-preview-container {
            background: #1e1d2c;
        }
        
        .avatar-upload-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            border-radius: 50%;
            opacity: 0;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .avatar-upload-wrapper:hover .avatar-upload-overlay {
            opacity: 1;
        }

        /* Premium Brand Gradient Buttons */
        .btn-premium-gradient {
            background: linear-gradient(135deg, #6C5CE7 0%, #8E2DE2 100%) !important;
            border: none !important;
            color: #ffffff !important;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        
        .btn-premium-gradient:hover {
            background: linear-gradient(135deg, #8E2DE2 0%, #6C5CE7 100%) !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 20px rgba(108, 92, 231, 0.35) !important;
            color: #ffffff !important;
        }

        /* Elite Professional Status Badges */
        .status-badge.status-pending { background: rgba(212, 175, 55, 0.1) !important; color: #8A6D1C !important; }
        [data-bs-theme="dark"] .status-badge.status-pending { background: rgba(212, 175, 55, 0.18) !important; color: #E5C158 !important; }

        .status-badge.status-processing { background: rgba(2, 128, 144, 0.08) !important; color: #028090 !important; }
        [data-bs-theme="dark"] .status-badge.status-processing { background: rgba(2, 128, 144, 0.18) !important; color: #00E5FF !important; }

        .status-badge.status-shipped { background: rgba(108, 92, 231, 0.08) !important; color: #6C5CE7 !important; }
        [data-bs-theme="dark"] .status-badge.status-shipped { background: rgba(162, 155, 254, 0.18) !important; color: #A29BFE !important; }

        .status-badge.status-delivered { background: rgba(42, 157, 143, 0.08) !important; color: #2A9D8F !important; }
        [data-bs-theme="dark"] .status-badge.status-delivered { background: rgba(42, 157, 143, 0.18) !important; color: #4ADBB3 !important; }

        .status-badge.status-cancelled { background: rgba(127, 140, 141, 0.1) !important; color: #5E6A75 !important; }
        [data-bs-theme="dark"] .status-badge.status-cancelled { background: rgba(255, 255, 255, 0.08) !important; color: #BDC3C7 !important; }
    </style>
    
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
                @if(!auth()->user()?->isDriver())
                <button class="theme-toggle-btn" data-bs-toggle="modal" data-bs-target="#searchModal" title="Search">
                    <i class="bi bi-search"></i>
                </button>
                @endif
                <button class="theme-toggle-btn" id="mobileThemeToggleTop" title="Toggle Theme">
                    <i class="bi bi-moon-stars"></i>
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

                        {{-- Notifications Dropdown --}}
                        <li class="nav-item dropdown notification-dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false" id="notificationMenuTrigger">
                                <i class="bi bi-bell"></i>
                                @php $unreadNotifs = auth()->user()->unreadNotifications()->count(); @endphp
                                <span class="badge notification-count badge-danger bg-danger rounded-circle position-absolute top-0 start-100 translate-middle" style="font-size: 0.6rem; padding: 0.25em 0.4em; {{ $unreadNotifs > 0 ? '' : 'display:none;' }}" id="notificationBadgeBadge">{{ $unreadNotifs }}</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end p-0 shadow-lg border-0" style="width: 350px; border-radius: 12px; overflow: hidden;" id="notificationDropdownMenu">
                                <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light">
                                    <h6 class="mb-0 fw-bold">Notifications</h6>
                                </div>
                                <div id="notificationListContent">
                                    <div class="text-center p-4"><div class="spinner-border spinner-border-sm text-primary"></div></div>
                                </div>
                            </div>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
                                <div class="overflow-hidden rounded-circle border" style="width: 26px; height: 26px; background: #fff;">
                                    <img src="{{ auth()->user()->profile_photo_url }}" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
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
            <div class="container-fluid px-lg-5 py-4">
                <div class="row g-4">
                    <!-- Left Side Spotlight Column -->
                    <div class="col-lg-3 d-none d-lg-block border-end-dynamic">
                        <div class="mega-spotlight-card h-100 p-4 d-flex flex-column justify-content-between rounded-4 position-relative overflow-hidden">
                            <div class="spotlight-glow"></div>
                            <div class="position-relative z-index-2 d-flex flex-column h-100">
                                <div class="mb-3">
                                    <span class="badge badge-spotlight mb-3 px-3 py-1.5 rounded-pill small-caps fw-semibold text-primary">Spotlight</span>
                                </div>
                                <h4 class="fw-bold mb-3 spotlight-title">Handpicked Collections</h4>
                                <p class="spotlight-desc mb-4 lh-lg">
                                    Discover our top premium goods curated for your lifestyle. Sourced with a commitment to local quality and absolute excellence.
                                </p>
                                <div class="mt-auto pt-4">
                                    <a href="{{ route('products.index') }}" class="btn btn-premium-gradient w-100 rounded-pill py-2.5 px-4 d-flex align-items-center justify-content-center gap-2 text-white">
                                        <span class="fw-bold">Browse All Products</span>
                                        <i class="bi bi-arrow-right-short fs-5 transition-transform"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side Categories Grid -->
                    <div class="col-12 col-lg-9">
                        <div class="row g-4 stagger-children">
                            @foreach($globalCategories as $cat)
                                <div class="col-6 col-md-4 col-xl-3 fade-up">
                                    <div class="category-mega-tile p-3 rounded-4 h-100 d-flex flex-column justify-content-between">
                                        <div>
                                            <div class="tile-header mb-3 pb-2.5 d-flex align-items-center gap-2">
                                                <div class="tile-icon-frame d-flex align-items-center justify-content-center shadow-sm">
                                                    @if($cat->image)
                                                        <img src="{{ $cat->image }}" alt="" class="tile-icon-img" loading="lazy" decoding="async">
                                                    @else
                                                        <i class="bi bi-grid text-primary"></i>
                                                    @endif
                                                </div>
                                                <a href="{{ route('products.index', ['category' => $cat->slug]) }}" class="tile-title text-decoration-none fw-bold hover-primary text-truncate" title="{{ $cat->name }}">
                                                    {{ $cat->name }}
                                                </a>
                                            </div>
                                            <ul class="list-unstyled ps-0 mega-product-list">
                                                @php
                                                    $topProducts = $cat->products()->where('is_active', true)->take(4)->get();
                                                @endphp
                                                @foreach($topProducts as $prod)
                                                    <li class="mb-2">
                                                        <a href="{{ route('products.show', $prod->slug) }}" class="mega-product-link text-decoration-none d-flex align-items-center gap-2" title="{{ $prod->name }}">
                                                            <span class="bullet-dot"></span>
                                                            <span class="text-truncate">{{ Str::limit($prod->name, 22) }}</span>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div class="mt-3 pt-2 border-top-dynamic">
                                            <a href="{{ route('products.index', ['category' => $cat->slug]) }}" class="view-all-link text-decoration-none fw-bold small d-inline-flex align-items-center gap-1 text-primary">
                                                <span>Explore All</span>
                                                <i class="bi bi-chevron-right small transition-transform"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </nav>

    {{-- Full-Page Menu Backdrop --}}
    <div class="menu-backdrop-overlay" id="menuBackdrop"></div>

    {{-- Smart Floating Categories Popover Drawer --}}
    <div class="mobile-categories-drawer d-lg-none" id="mobileCategoriesDrawer">
        <div class="drawer-header d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
            <h6 class="mb-0 fw-bold" style="font-family: 'Outfit', sans-serif;"><i class="bi bi-tags-fill text-primary me-2"></i>Categories</h6>
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-link p-1 text-decoration-none text-reset" id="mobileThemeToggle" title="Toggle Theme" style="font-size: 1.15rem; line-height: 1;">
                    <i class="bi bi-moon-stars text-primary"></i>
                </button>
                <button type="button" class="btn-close-custom" id="btnCloseCategoriesDrawer" aria-label="Close">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>
        <div class="drawer-body">
            <div class="row g-2">
                @foreach($globalCategories as $cat)
                    <div class="col-6">
                        <a href="{{ route('products.index', ['category' => $cat->slug]) }}" class="category-drawer-tile text-decoration-none">
                            <div class="tile-icon">
                                @if($cat->image)
                                    <img src="{{ $cat->image }}" alt="" loading="lazy" decoding="async">
                                @else
                                    <i class="bi bi-tag text-primary"></i>
                                @endif
                            </div>
                            <span class="tile-name text-truncate">{{ $cat->name }}</span>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Mobile Search Modal --}}
    <div class="modal fade" id="searchModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content search-modal-content" style="border:none;">
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
                <div class="col-4 col-lg-2 col-md-4 mb-4 mb-lg-0">
                    <h6 class="footer-heading text-truncate">Shop</h6>
                    <ul class="footer-links">
                        <li><a href="{{ route('offers') }}">Offers</a></li>
                        <li><a href="{{ route('products.index') }}">Products</a></li>
                        <li><a href="{{ route('categories') }}">Categories</a></li>
                        @foreach(\App\Models\Category::take(3)->get() as $cat)
                            <li><a href="{{ route('products.index', ['category' => $cat->slug]) }}">{{ Str::limit($cat->name, 10) }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-4 col-lg-2 col-md-4 mb-4 mb-lg-0">
                    <h6 class="footer-heading text-truncate">Account</h6>
                    <ul class="footer-links">
                        <li><a href="{{ route('login') }}">Login</a></li>
                        <li><a href="{{ route('register') }}">Sign Up</a></li>
                        <li><a href="{{ route('contact') }}">Contact</a></li>
                        @auth
                            <li><a href="{{ route('orders.index') }}">Orders</a></li>
                            <li><a href="{{ route('cart.index') }}">Cart</a></li>
                        @endauth
                    </ul>
                </div>
                <div class="col-4 col-lg-3 col-md-4 mb-4 mb-lg-0">
                    <h6 class="footer-heading text-truncate">Hours</h6>
                     <ul class="footer-links" style="font-size:0.8rem;">
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
                            <li class="d-flex justify-content-between mb-1" style="font-size: 0.75rem;">
                                <span class="text-capitalize text-white-50">{{ substr($day, 0, 3) }}:</span>
                                <span class="text-light text-end">
                                    @if($isClosed || (!$open && !$close))
                                        <span class="text-danger fw-bold" style="font-size: 0.7rem;">Closed</span>
                                    @else
                                        {{ $open ? \Carbon\Carbon::parse($open)->format('H:i') : '' }}-{{ $close ? \Carbon\Carbon::parse($close)->format('H:i') : '' }}
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
            <div class="footer-bottom text-center" style="margin-top: 50px; padding-top: 30px; border-top: 1px solid rgba(255,255,255,0.06);">
                <p class="mb-0 text-white-50">&copy; {{ date('Y') }} Premier Shop. All rights reserved.</p>
                <div class="d-inline-flex gap-3 mt-2 small text-white-50">
                    <a href="#" class="text-decoration-none hover-primary text-reset">Privacy Policy</a>
                    <span>•</span>
                    <a href="#" class="text-decoration-none hover-primary text-reset">Terms of Service</a>
                    <span>•</span>
                    <a href="#" class="text-decoration-none hover-primary text-reset">Sitemap</a>
                </div>
            </div>
        </div>
    </footer>

    {{-- Back to Top with Scroll Progress --}}
    <div id="scrollProgress" class="scroll-progress-wrap">
        <svg class="progress-circle" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"/>
        </svg>
        <i class="bi bi-arrow-up-short"></i>
    </div>

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
            
            const btn = e.submitter || form.querySelector('button[type="submit"]');
            const originalBtnHtml = btn ? btn.innerHTML : null;
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
            }

            const formData = new FormData(form);
            const actionUrl = (btn && btn.hasAttribute('formaction')) ? btn.getAttribute('formaction') : form.action;
            
            fetch(actionUrl, {
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
                    if (data.redirect) {
                        window.location.href = data.redirect;
                        return;
                    }
                    
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
            const mobileThemeToggleTop = document.getElementById('mobileThemeToggleTop');
            const htmlElement = document.documentElement;
            const themeIcon = themeToggle ? themeToggle.querySelector('i') : null;
            const mobileThemeIcon = mobileThemeToggle ? mobileThemeToggle.querySelector('i') : null;

            function updateThemeUI(theme) {
                const isDark = theme === 'dark';
                
                // Update Desktop Toggle
                if (themeIcon) {
                    themeIcon.className = isDark ? 'bi bi-sun' : 'bi bi-moon-stars';
                }
                
                // Update Mobile Drawer Toggle
                if (mobileThemeIcon) {
                    mobileThemeIcon.className = isDark ? 'bi bi-sun text-warning' : 'bi bi-moon-stars text-primary';
                }
                
                // Update Mobile Top Toggle
                const topMobileToggle = document.getElementById('mobileThemeToggleTop');
                if (topMobileToggle) {
                    const topIcon = topMobileToggle.querySelector('i');
                    if (topIcon) {
                        topIcon.className = isDark ? 'bi bi-sun text-warning' : 'bi bi-moon-stars text-primary';
                    }
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

            if (themeToggle) themeToggle.addEventListener('click', toggleTheme);
            if (mobileThemeToggle) mobileThemeToggle.addEventListener('click', toggleTheme);
            if (mobileThemeToggleTop) mobileThemeToggleTop.addEventListener('click', toggleTheme);

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

            // Mobile Categories Popover Drawer Interactivity
            const dockOpenCategories = document.getElementById('dockOpenCategories');
            const categoriesDrawer = document.getElementById('mobileCategoriesDrawer');
            const btnCloseDrawer = document.getElementById('btnCloseCategoriesDrawer');
            
            function toggleCategoriesDrawer() {
                if (categoriesDrawer) {
                    const isShown = categoriesDrawer.classList.toggle('show');
                    if (dockOpenCategories) {
                        if (isShown) {
                            document.querySelectorAll('.dock-item').forEach(el => el.classList.remove('active'));
                            dockOpenCategories.classList.add('active');
                        } else {
                            dockOpenCategories.classList.remove('active');
                            restoreActiveDockState();
                        }
                    }
                }
            }

            function restoreActiveDockState() {
                const currentPath = window.location.pathname;
                document.querySelectorAll('.dock-item').forEach(el => {
                    const href = el.getAttribute('href');
                    if (href && href !== 'javascript:void(0)') {
                        try {
                            const urlObj = new URL(href, window.location.origin);
                            if (currentPath === urlObj.pathname) {
                                el.classList.add('active');
                            }
                        } catch(e) {}
                    }
                });
            }
            
            if (dockOpenCategories) {
                dockOpenCategories.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    toggleCategoriesDrawer();
                });
            }
            
            if (btnCloseDrawer) {
                btnCloseDrawer.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (categoriesDrawer) categoriesDrawer.classList.remove('show');
                    if (dockOpenCategories) dockOpenCategories.classList.remove('active');
                    restoreActiveDockState();
                });
            }
            
            // Close drawer when clicking outside
            document.addEventListener('click', function(e) {
                if (categoriesDrawer && categoriesDrawer.classList.contains('show')) {
                    if (!categoriesDrawer.contains(e.target) && e.target !== dockOpenCategories && !dockOpenCategories.contains(e.target)) {
                        categoriesDrawer.classList.remove('show');
                        if (dockOpenCategories) dockOpenCategories.classList.remove('active');
                        restoreActiveDockState();
                    }
                }
            });
        });
    </script>
    
    {{-- Premium Mobile Bottom Dock --}}
    <div class="mobile-bottom-dock d-lg-none">
        <div class="dock-wrapper d-flex justify-content-around align-items-center">
            <a href="{{ route('home') }}" class="dock-item {{ request()->routeIs('home') ? 'active' : '' }}">
                <div class="dock-icon-wrapper">
                    <i class="bi bi-house-door"></i>
                </div>
                <span>Home</span>
            </a>
            <a href="javascript:void(0)" id="dockOpenCategories" class="dock-item">
                <div class="dock-icon-wrapper">
                    <i class="bi bi-grid-3x3-gap"></i>
                </div>
                <span>Categories</span>
            </a>
            <a href="{{ auth()->check() ? route('cart.index') : route('login') }}" class="dock-item {{ request()->routeIs('cart.index') ? 'active' : '' }} position-relative">
                <div class="dock-icon-wrapper">
                    <i class="bi bi-bag"></i>
                </div>
                <span>Cart</span>
                @auth
                    @php $cartCount = auth()->user()->cartItems()->sum('quantity'); @endphp
                    <span class="badge cart-count-badge position-absolute top-0 start-50 translate-middle-y bg-danger rounded-circle" id="cartCountBadgeDock" style="font-size: 0.65rem; padding: 0.3em 0.5em; min-width: 18px; {{ $cartCount > 0 ? '' : 'display:none;' }}">{{ $cartCount }}</span>
                @else
                    <span class="badge cart-count-badge position-absolute top-0 start-50 translate-middle-y bg-danger rounded-circle" id="cartCountBadgeDock" style="font-size: 0.65rem; padding: 0.3em 0.5em; min-width: 18px; display:none;">0</span>
                @endauth
            </a>
            <a href="{{ auth()->check() ? route('wishlists.index') : route('login') }}" class="dock-item {{ request()->routeIs('wishlists.index') ? 'active' : '' }}">
                <div class="dock-icon-wrapper">
                    <i class="bi bi-heart"></i>
                </div>
                <span>Wishlist</span>
            </a>
            @auth
            <div class="dropdown" style="display: contents;">
                <a href="#" class="dock-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}" data-bs-toggle="dropdown" aria-expanded="false" data-bs-offset="0,15">
                    <div class="dock-icon-wrapper">
                        <div class="overflow-hidden rounded-circle border dock-avatar" style="width: 20px; height: 20px; background: #fff;">
                            <img src="{{ auth()->user()->profile_photo_url }}" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    </div>
                    <span>Profile</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 bg-white" style="border-radius: 16px; min-width: 180px; z-index: 1100; margin-bottom: 10px;">
                    <li><a class="dropdown-item py-2.5 fw-semibold text-dark small" href="{{ route('profile.edit') }}"><i class="bi bi-gear me-2 text-muted"></i>Profile Settings</a></li>
                    @if(auth()->user()->isAdmin())
                        <li><a class="dropdown-item py-2.5 fw-semibold text-dark small" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2 text-muted"></i>Admin Dashboard</a></li>
                    @endif
                    <li><hr class="dropdown-divider my-1 opacity-10"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item py-2.5 text-danger small" style="border: none; background: none; width: 100%; text-align: left; font-weight: 600;"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
            @else
            <a href="{{ route('login') }}" class="dock-item">
                <div class="dock-icon-wrapper">
                    <i class="bi bi-person-circle"></i>
                </div>
                <span>Profile</span>
            </a>
            @endauth
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const notificationMenuTrigger = document.getElementById('notificationMenuTrigger');
            const notificationListContent = document.getElementById('notificationListContent');

            if (notificationMenuTrigger && notificationListContent) {
                notificationMenuTrigger.addEventListener('show.bs.dropdown', function () {
                    fetch('{{ route("notifications.latest") }}')
                        .then(response => response.text())
                        .then(html => {
                            notificationListContent.innerHTML = html;
                        })
                        .catch(error => {
                            console.error('Error fetching notifications:', error);
                            notificationListContent.innerHTML = '<div class="p-3 text-center text-danger">Failed to load.</div>';
                        });
                });
            }
        });
    </script>
    @stack('scripts')
</body>

</html>