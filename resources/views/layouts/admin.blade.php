<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin — Premier Shop')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        :root {
            --admin-sidebar-w: 260px;
            --admin-bg: #0F1117;
            --admin-card: #181B23;
            --admin-border: rgba(255, 255, 255, 0.06);
            --admin-text: rgba(255, 255, 255, 0.85);
            --admin-muted: rgba(255, 255, 255, 0.65);
            --admin-placeholder: rgba(255, 255, 255, 0.3);
        }

        body {
            background: var(--admin-bg) !important;
            color: var(--admin-text);
            overflow-x: hidden;
        }

        /* ── Sidebar ── */
        .admin-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--admin-sidebar-w);
            height: 100vh;
            background: linear-gradient(180deg, #13151D 0%, #0D0F15 100%);
            border-right: 1px solid var(--admin-border);
            padding: 0;
            z-index: 1050;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
            overflow-y: auto;
        }

        /* ── Sidebar Scrollbar ── */
        .admin-sidebar::-webkit-scrollbar {
            width: 5px;
        }
        .admin-sidebar::-webkit-scrollbar-track {
            background: transparent;
        }
        .admin-sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }
        .admin-sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .sidebar-brand {
            padding: 20px 24px;
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 1.3rem;
            color: #fff;
            border-bottom: 1px solid var(--admin-border);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-brand i {
            color: #6C5CE7;
            font-size: 1.4rem;
        }

        .sidebar-brand small {
            font-size: 0.65rem;
            font-weight: 500;
            color: var(--admin-muted);
            display: block;
        }

        .sidebar-section {
            padding: 16px 12px 8px;
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--admin-muted);
            font-weight: 600;
        }

        .admin-sidebar .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 20px;
            margin: 2px 10px;
            color: var(--admin-muted);
            font-weight: 500;
            font-size: 0.88rem;
            border-radius: 10px;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .admin-sidebar .nav-link:hover {
            color: #fff;
            background: rgba(108, 92, 231, 0.12);
        }

        .admin-sidebar .nav-link.active {
            color: #fff;
            background: rgba(108, 92, 231, 0.2);
            box-shadow: 0 0 20px rgba(108, 92, 231, 0.1);
        }

        .admin-sidebar .nav-link.active i {
            color: #6C5CE7;
        }

        .admin-sidebar .nav-link i {
            font-size: 1.1rem;
            width: 22px;
            text-align: center;
        }

        .admin-sidebar .nav-link .badge {
            margin-left: auto;
            font-size: 0.65rem;
            padding: 3px 8px;
        }

        .sidebar-footer {
            margin-top: auto;
            padding: 16px 12px;
            border-top: 1px solid var(--admin-border);
        }

        .sidebar-footer .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.03);
        }

        .sidebar-footer .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6C5CE7, #A29BFE);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 0.85rem;
        }

        .sidebar-footer .user-name {
            color: #fff;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .sidebar-footer .user-role {
            color: var(--admin-muted);
            font-size: 0.72rem;
        }

        /* ── Main Content ── */
        .admin-content {
            margin-left: var(--admin-sidebar-w);
            padding: 24px 28px;
            min-height: 100vh;
        }

        /* ── Top Bar ── */
        .admin-topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--admin-border);
        }

        .admin-topbar h2 {
            font-family: 'Outfit', sans-serif;
            color: #fff;
            font-weight: 700;
            font-size: 1.6rem;
            margin: 0;
        }

        .admin-topbar .breadcrumb {
            margin: 0;
            font-size: 0.8rem;
        }

        .admin-topbar .breadcrumb-item a {
            color: var(--admin-muted);
            text-decoration: none;
        }

        .admin-topbar .breadcrumb-item.active {
            color: #fff;
        }

        /* ── Cards ── */
        .admin-card {
            background: var(--admin-card);
            border: 1px solid var(--admin-border);
            border-radius: 14px;
            padding: 24px;
            transition: all 0.2s ease;
        }

        .admin-card:hover {
            border-color: rgba(108, 92, 231, 0.2);
        }

        .admin-card .card-title {
            font-weight: 700;
            font-size: 0.95rem;
            color: #fff;
            margin-bottom: 16px;
        }

        /* ── Stat Cards ── */
        .stat-card {
            border-radius: 14px;
            padding: 22px;
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: -20px;
            right: -20px;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
        }

        .stat-number {
            font-family: 'Outfit', sans-serif;
            font-size: 2rem;
            font-weight: 800;
            line-height: 1.1;
        }

        .stat-label {
            font-size: 0.8rem;
            opacity: 0.8;
            margin-top: 4px;
        }

        .stat-icon {
            font-size: 2rem;
            opacity: 0.6;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #6C5CE7, #A29BFE) !important;
        }

        /* ── Tables ── */
        .admin-table {
            width: 100%;
            color: var(--admin-text);
            --bs-table-bg: transparent;
            --bs-table-color: var(--admin-text);
            --bs-table-hover-bg: rgba(108, 92, 231, 0.04);
            --bs-table-hover-color: var(--admin-text);
        }

        .admin-table thead th {
            background: rgba(255, 255, 255, 0.06) !important;
            border-bottom: 1px solid var(--admin-border) !important;
            padding: 14px 16px;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #fff !important;
            font-weight: 700;
        }

        .admin-table tbody td {
            background: transparent !important;
            color: var(--admin-text) !important;
            padding: 14px 16px;
            border-bottom: 1px solid var(--admin-border) !important;
            font-size: 0.9rem;
            vertical-align: middle;
        }

        .admin-table tbody tr:hover td {
            background: rgba(108, 92, 231, 0.04) !important;
        }

        .admin-table tbody tr:last-child td {
            border-bottom: none !important;
        }

        /* ── Buttons (admin-specific) ── */
        .btn-admin {
            background: linear-gradient(135deg, #6C5CE7, #A29BFE);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            padding: 10px 20px;
            transition: all 0.2s;
        }

        .btn-admin:hover {
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(108, 92, 231, 0.3);
        }

        .btn-admin-outline {
            background: transparent;
            color: #6C5CE7;
            border: 1px solid rgba(108, 92, 231, 0.3);
            border-radius: 10px;
            font-weight: 600;
            padding: 10px 20px;
            transition: all 0.2s;
        }

        .btn-admin-outline:hover {
            background: rgba(108, 92, 231, 0.1);
            color: #A29BFE;
        }

        .btn-icon {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            border: 1px solid var(--admin-border);
            background: transparent;
            color: var(--admin-muted);
            transition: all 0.2s;
        }

        .btn-icon:hover {
            color: #fff;
            border-color: rgba(108, 92, 231, 0.4);
            background: rgba(108, 92, 231, 0.1);
        }

        .btn-icon.btn-icon-danger:hover {
            color: #E17055;
            border-color: rgba(225, 112, 85, 0.4);
            background: rgba(225, 112, 85, 0.1);
        }

        /* ── Badge styles ── */
        .badge-status {
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.72rem;
        }

        /* ── Form controls (dark theme) ── */
        .form-control,
        .form-select {
            background: rgba(255, 255, 255, 0.05) !important;
            border-color: var(--admin-border) !important;
            color: #fff !important;
            transition: all 0.2s ease;
        }

        .form-control::placeholder {
            color: var(--admin-placeholder);
        }

        .form-control:focus,
        .form-select:focus {
            background: rgba(255, 255, 255, 0.08) !important;
            border-color: #6C5CE7 !important;
            box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.2) !important;
        }

        .form-label {
            color: var(--admin-muted);
            font-weight: 600;
            font-size: 0.85rem;
            margin-bottom: 8px;
        }

        .form-check-label {
            color: var(--admin-text);
        }

        /* ── Custom Pagination (Admin) ── */
        .pagination {
            gap: 5px;
            margin: 0;
        }

        .page-link,
        [role="navigation"] a,
        [role="navigation"] span.inline-flex {
            background: var(--admin-card) !important;
            border-color: var(--admin-border) !important;
            color: var(--admin-muted) !important;
            border-radius: 8px !important;
            padding: 8px 16px;
            box-shadow: none !important;
            text-decoration: none !important;
        }

        /* Specific fix for SVG size in Laravel default pagination */
        [role="navigation"] svg {
            width: 1.25rem !important;
            height: 1.25rem !important;
            display: inline-block;
            vertical-align: middle;
        }

        .page-link:hover,
        [role="navigation"] a:hover {
            background: rgba(108, 92, 231, 0.1) !important;
            color: #fff !important;
            border-color: #6C5CE7 !important;
        }

        .page-item.active .page-link,
        [aria-current="page"] span {
            background: #6C5CE7 !important;
            border-color: #6C5CE7 !important;
            color: #fff !important;
        }

        .page-item.disabled .page-link,
        [aria-disabled="true"] span {
            background: rgba(255, 255, 255, 0.02) !important;
            border-color: var(--admin-border) !important;
            color: rgba(255, 255, 255, 0.1) !important;
            cursor: not-allowed;
        }

        .text-muted {
            color: var(--admin-muted) !important;
        }

        /* ── Dropdown menus (dark theme) ── */
        .admin-content .dropdown-menu,
        .admin-sidebar .dropdown-menu {
            background: #1E2130 !important;
            border: 1px solid var(--admin-border) !important;
            box-shadow: 0 10px 30px rgba(0,0,0,0.4);
        }

        .admin-content .dropdown-item,
        .admin-sidebar .dropdown-item {
            color: var(--admin-text) !important;
        }

        .admin-content .dropdown-item:hover,
        .admin-content .dropdown-item:focus,
        .admin-sidebar .dropdown-item:hover,
        .admin-sidebar .dropdown-item:focus {
            background: rgba(108, 92, 231, 0.15) !important;
            color: #fff !important;
        }

        .admin-content .dropdown-item.active,
        .admin-sidebar .dropdown-item.active {
            background: rgba(108, 92, 231, 0.25) !important;
            color: #fff !important;
        }

        /* ── Select dropdown fix ── */
        .admin-content select option {
            background: #1E2130;
            color: #fff;
        }

        /* ── Form switch / checkbox dark fix ── */
        .admin-content .form-check-input {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .admin-content .form-check-input:checked {
            background-color: #6C5CE7;
            border-color: #6C5CE7;
        }

        /* ── Alerts ── */
        .admin-content .alert {
            border-radius: 12px;
            border: none;
        }

        /* ── Mobile ── */
        @media (max-width: 991.98px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }

            .admin-sidebar.show {
                transform: translateX(0);
                box-shadow: 20px 0 60px rgba(0, 0, 0, 0.5);
            }

            .admin-content {
                margin-left: 0;
                padding: 16px;
            }
        }
    </style>
    @stack('styles')
</head>

<body>
    {{-- Sidebar --}}
    <div class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-brand">
            <i class="bi bi-shop"></i>
            <div>
                Premier Shop
                <small>Admin Dashboard</small>
            </div>
        </div>

        <div class="sidebar-section">Main</div>
        <a href="{{ route('admin.dashboard') }}"
            class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2"></i> Dashboard
        </a>

        <div class="sidebar-section">Store Management</div>
        <a href="{{ route('admin.products.index') }}"
            class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i> Products
            <span class="badge bg-primary">{{ \App\Models\Product::count() }}</span>
        </a>
        <a href="{{ route('admin.categories.index') }}"
            class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
            <i class="bi bi-tags"></i> Categories
            <span class="badge bg-primary">{{ \App\Models\Category::count() }}</span>
        </a>
        <a href="{{ route('admin.orders.index') }}"
            class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
            <i class="bi bi-receipt"></i> Orders
            <span class="badge bg-success">{{ \App\Models\Order::where('status', 'pending')->count() }}</span>
        </a>
        <a href="{{ route('admin.customers.index') }}"
            class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Customers
        </a>
        <a href="{{ route('admin.reports.index') }}"
            class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-fill"></i> Reports
        </a>

        <div class="sidebar-section">Marketing</div>
        <a href="{{ route('admin.sliders.index') }}"
            class="nav-link {{ request()->routeIs('admin.sliders.*') ? 'active' : '' }}">
            <i class="bi bi-images"></i> Sliders
        </a>
        <a href="{{ route('admin.coupons.index') }}"
            class="nav-link {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
            <i class="bi bi-tag"></i> Coupons
        </a>

        <div class="sidebar-section">System</div>
        <a href="{{ route('admin.roles.index') }}"
            class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
            <i class="bi bi-shield-lock"></i> Roles
        </a>
        <a href="{{ route('admin.settings.index') }}"
            class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
            <i class="bi bi-gear"></i> Settings
        </a>

        <div class="sidebar-section">Tools</div>
        <a href="{{ route('admin.scanner') }}"
            class="nav-link {{ request()->routeIs('admin.scanner') ? 'active' : '' }}">
            <i class="bi bi-qr-code-scan"></i> QR Scanner
        </a>
        <a href="{{ url('/') }}" class="nav-link">
            <i class="bi bi-globe"></i> View Store
        </a>

        {{-- Sidebar Footer --}}
        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
                <div>
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-role">{{ auth()->user()->role?->display_name ?? 'Staff' }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent"
                    style="color:#E17055;margin:0;padding:10px 12px;">
                    <i class="bi bi-box-arrow-right"></i> Sign Out
                </button>
            </form>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="admin-content">
        {{-- Mobile Toggle --}}
        <button class="btn btn-admin d-lg-none mb-3"
            onclick="document.getElementById('adminSidebar').classList.toggle('show')">
            <i class="bi bi-list me-1"></i> Menu
        </button>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    {{-- Close sidebar on overlay click (mobile) --}}
    <script>
        document.addEventListener('click', (e) => {
            const sidebar = document.getElementById('adminSidebar');
            if (window.innerWidth < 992 && sidebar.classList.contains('show') && !sidebar.contains(e.target) && !e.target.closest('.d-lg-none')) {
                sidebar.classList.remove('show');
            }
        });
    </script>
    @stack('scripts')
</body>

</html>