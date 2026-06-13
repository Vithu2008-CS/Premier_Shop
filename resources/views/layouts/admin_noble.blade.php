{{--
    layouts/admin_noble.blade.php — Admin panel layout (Noble UI theme)
    =====================================================================
    Used by all admin routes (dashboard, products, orders, customers, reports, mail, etc.)

    Provides:
     - Noble UI vendor CSS/JS (admin_assets/)
     - Dark/light theme pre-render without FOUC — inlines correct CSS path before body paint
     - @include noble_sidebar, noble_navbar, noble_footer partials
     - Flash message alerts for session('success') and session('error')
     - @yield('content') — admin page content slot
     - Feather icons init, theme switcher, admin notification bell fetch via jQuery
     - @stack('plugin-styles'), @stack('styles'), @stack('plugin-scripts'), @stack('scripts')

    AppServiceProvider injects $notificationData (pendingOrdersCount, recentOrders,
    recentCustomers) into this layout via View::composer.
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Admin Dashboard</title>
    <script src="{{ asset('js/csp-shim.js') }}" defer></script>
    
    <!-- core:css -->
    <link rel="stylesheet" href="{{ asset('admin_assets/vendors/core/core.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- endinject -->
    
    <!-- plugin css for this page -->
    @stack('plugin-styles')
    <!-- end plugin css for this page -->
    
    <!-- inject:css -->
    <link rel="stylesheet" href="{{ asset('admin_assets/fonts/feather-font/css/iconfont.css') }}">
    <link rel="stylesheet" href="{{ asset('admin_assets/vendors/flag-icon-css/css/flag-icon.min.css') }}">
    <!-- endinject -->
    
    {{-- Pre-render Theme Logic & Background to prevent Flash of Unstyled Content (FOUC) --}}
    <script nonce="{{ Vite::cspNonce() }}">
        (function() {
            const savedTheme = localStorage.getItem('admin_theme') || 'light';
            document.documentElement.setAttribute('data-admin-theme', savedTheme);
            // Set document background color immediately to match the active theme and prevent the white flash
            if (savedTheme === 'dark') {
                document.documentElement.style.backgroundColor = '#0c1427';
            } else {
                document.documentElement.style.backgroundColor = '#f9fafb';
            }
            
            const cssPath = savedTheme === 'dark' 
                ? "{{ asset('admin_assets/css/demo_2/style.css') }}" 
                : "{{ asset('admin_assets/css/demo_1/style.css') }}";
            
            document.write('<link rel="stylesheet" id="main-stylesheet" href="' + cssPath + '">');
        })();
    </script>
    <noscript>
        <link rel="stylesheet" id="main-stylesheet" href="{{ asset('admin_assets/css/demo_1/style.css') }}">
    </noscript>
    
    <link rel="shortcut icon" href="{{ asset('admin_assets/images/favicon.png') }}" />

    <style>
        .sidebar .sidebar-header .sidebar-brand span {
            color: #727cf5;
        }

        /* Custom styled Choose File buttons with theme support */
        .form-control[type="file"] {
            padding: 0 !important;
            height: calc(1.5em + 0.75rem + 2px) !important;
            line-height: calc(1.5em + 0.75rem) !important;
        }
        .form-control[type="file"]::file-selector-button {
            border: none !important;
            border-right: 1px solid rgba(0, 0, 0, 0.12) !important;
            background: rgba(0, 0, 0, 0.05) !important;
            color: #495057 !important;
            padding: 0 1.25rem !important;
            margin: 0 !important;
            margin-right: 0.75rem !important;
            height: 100% !important;
            border-radius: 9px 0 0 9px !important;
            cursor: pointer !important;
            font-weight: 600 !important;
            font-size: 0.8rem !important;
            transition: all 0.2s ease !important;
        }
        .form-control[type="file"]::file-selector-button:hover {
            background: rgba(0, 0, 0, 0.08) !important;
        }
        html[data-admin-theme="dark"] .form-control[type="file"]::file-selector-button {
            background: rgba(255, 255, 255, 0.08) !important;
            border-color: rgba(255, 255, 255, 0.15) !important;
            color: #c9d2e1 !important;
        }
        html[data-admin-theme="dark"] .form-control[type="file"]::file-selector-button:hover {
            background: rgba(255, 255, 255, 0.12) !important;
        }

        /* Mail Service list date alignment */
        .inbox-wrapper .email-content .email-list .email-list-item .email-list-detail {
            align-items: center !important;
        }
        .inbox-wrapper .email-content .email-list .email-list-item .email-list-detail .date {
            margin-top: 2px !important; /* Place the text a little bit down and align center */
            align-self: center !important;
        }

        /* Global Panel & Card Curved Redesign */
        .card {
            border-radius: 18px !important;
            border: none !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03) !important;
            overflow: hidden;
        }
        html[data-admin-theme="dark"] .card {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15) !important;
        }

        /* Global Navigation Tab Curved Redesign */
        .nav-tabs .nav-link {
            border-radius: 8px !important;
        }

        /* Global Pagination Links Curved Redesign */
        .pagination .page-item .page-link {
            border-radius: 20px !important;
            margin: 0 2px !important;
        }

        /* Global Form Controls & Curved Input Groups */
        .form-control, .form-select {
            border-radius: 10px !important;
        }
        
        .input-group > .form-control,
        .input-group > .form-select,
        .input-group > .input-group-text,
        .input-group > .input-group-append > .btn,
        .input-group > .input-group-prepend > .btn {
            border-radius: 10px !important;
        }
        
        .input-group > :not(:last-child):not(.dropdown-toggle),
        .input-group > .input-group-append:not(:last-child) > .btn,
        .input-group > .input-group-prepend > .btn {
            border-top-right-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
        }
        
        .input-group > :not(:first-child),
        .input-group > .input-group-append > .btn,
        .input-group > .input-group-prepend:not(:first-child) > .btn {
            border-top-left-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
        }

        /* Omni-Search Live Dropdown Results Menu styling */
        .navbar-content .search-form {
            position: relative !important;
            width: 100% !important;
            max-width: 580px !important;
        }
        
        /* High-Fidelity Spacious Curved Global Search Box */
        .navbar-content .search-form .input-group {
            background-color: #f3f4f6 !important;
            border-radius: 30px !important;
            border: 1px solid rgba(0, 0, 0, 0.08) !important;
            padding: 2px 8px 2px 16px !important;
            transition: all 0.25s ease !important;
            align-items: center !important;
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.02) !important;
        }
        html[data-admin-theme="dark"] .navbar-content .search-form .input-group {
            background-color: rgba(255, 255, 255, 0.05) !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
        }
        
        /* Focused search box container glow */
        .navbar-content .search-form .input-group:focus-within {
            background-color: #ffffff !important;
            border-color: #743089 !important;
            box-shadow: 0 0 0 3px rgba(116, 48, 137, 0.15), 0 4px 12px rgba(0,0,0,0.03) !important;
        }
        html[data-admin-theme="dark"] .navbar-content .search-form .input-group:focus-within {
            background-color: #0c1427 !important;
            border-color: #a78bfa !important;
            box-shadow: 0 0 0 3px rgba(167, 139, 250, 0.25), 0 4px 12px rgba(0,0,0,0.2) !important;
        }

        /* Transparent prepend area */
        .navbar-content .search-form .input-group-prepend,
        .navbar-content .search-form .input-group-text {
            background: transparent !important;
            border: none !important;
            padding: 0 !important;
            margin: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        /* The Search Icon itself with a beautiful purple color and proper space */
        .navbar-content .search-form .input-group-text i,
        .navbar-content .search-form .input-group-text svg {
            color: #743089 !important;
            width: 18px !important;
            height: 18px !important;
            margin-right: 20px !important; /* Spacious margin between search icon and text */
            transition: all 0.2s ease !important;
        }
        html[data-admin-theme="dark"] .navbar-content .search-form .input-group-text i,
        html[data-admin-theme="dark"] .navbar-content .search-form .input-group-text svg {
            color: #a78bfa !important;
        }
        .navbar-content .search-form .input-group:focus-within .input-group-text i,
        .navbar-content .search-form .input-group:focus-within .input-group-text svg {
            transform: scale(1.05) !important;
        }

        /* Clean transparent input box */
        .navbar-content .search-form #navbarForm {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            padding: 8px 10px 8px 0 !important;
            height: 38px !important;
            color: #1e293b !important;
            font-size: 0.9rem !important;
            font-weight: 500 !important;
            text-align: left !important;
            position: relative !important;
            top: 2px !important;
        }
        html[data-admin-theme="dark"] .navbar-content .search-form #navbarForm {
            color: #cbd5e1 !important;
        }
        .navbar-content .search-form #navbarForm::placeholder {
            color: #94a3b8 !important;
            font-weight: 400 !important;
        }
        html[data-admin-theme="dark"] .navbar-content .search-form #navbarForm::placeholder {
            color: #64748b !important;
        }
        .omni-search-dropdown-menu {
            position: absolute !important;
            top: 105% !important;
            left: 0 !important;
            width: 100% !important;
            max-width: 580px !important;
            background-color: #ffffff !important;
            border-radius: 14px !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08) !important;
            border: 1.5px solid rgba(0, 0, 0, 0.05) !important;
            z-index: 1090 !important;
            padding: 14px !important;
            max-height: 400px !important;
            overflow-y: auto !important;
        }
        html[data-admin-theme="dark"] .omni-search-dropdown-menu {
            background-color: #0c1427 !important;
            border-color: rgba(255, 255, 255, 0.06) !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3) !important;
        }

        /* Category header blocks */
        .omni-search-category-title {
            font-size: 0.68rem !important;
            font-weight: 800 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.8px !important;
            color: #743089 !important;
            margin-bottom: 8px !important;
            padding-bottom: 4px !important;
            border-bottom: 1px dashed rgba(116, 48, 137, 0.12) !important;
        }
        html[data-admin-theme="dark"] .omni-search-category-title {
            color: #a78bfa !important;
            border-bottom-color: rgba(255, 255, 255, 0.08) !important;
        }

        /* List Item blocks */
        .omni-search-item {
            display: flex !important;
            align-items: center !important;
            padding: 8px 10px !important;
            border-radius: 10px !important;
            text-decoration: none !important;
            color: #475569 !important;
            transition: all 0.2s ease !important;
            margin-bottom: 4px !important;
        }
        html[data-admin-theme="dark"] .omni-search-item {
            color: #cbd5e1 !important;
        }
        .omni-search-item:hover, .omni-search-item.active {
            background-color: rgba(116, 48, 137, 0.06) !important;
            color: #743089 !important;
            text-decoration: none !important;
            transform: translateY(-0.5px) !important;
            outline: none !important;
        }
        html[data-admin-theme="dark"] .omni-search-item:hover, html[data-admin-theme="dark"] .omni-search-item.active {
            background-color: rgba(167, 139, 250, 0.1) !important;
            color: #a78bfa !important;
            text-decoration: none !important;
        }

        /* Thumbnail details */
        .omni-search-thumb {
            width: 36px !important;
            height: 36px !important;
            border-radius: 8px !important;
            object-fit: cover !important;
            margin-right: 10px !important;
        }
        .omni-search-avatar {
            width: 36px !important;
            height: 36px !important;
            border-radius: 50% !important;
            background-color: rgba(116, 48, 137, 0.08) !important;
            color: #743089 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-weight: 700 !important;
            font-size: 0.85rem !important;
            margin-right: 10px !important;
        }
        html[data-admin-theme="dark"] .omni-search-avatar {
            background-color: rgba(167, 139, 250, 0.12) !important;
            color: #a78bfa !important;
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="main-wrapper">

        <!-- partial:partials/_sidebar.html -->
        @include('admin.partials.noble_sidebar')
        <!-- partial -->
    
        <div class="page-wrapper">
                    
            <!-- partial:partials/_navbar.html -->
            @include('admin.partials.noble_navbar')
            <!-- partial -->

            <div class="page-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        {{ session('warning') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @yield('content')
            </div>

            <!-- partial:partials/_footer.html -->
            @include('admin.partials.noble_footer')
            <!-- partial -->
        
        </div>
    </div>

    <!-- core:js -->
    <script src="{{ asset('admin_assets/vendors/core/core.js') }}"></script>
    <!-- endinject -->

    <!-- plugin js for this page -->
    <script src="{{ asset('admin_assets/vendors/feather-icons/feather.min.js') }}"></script>
    @stack('plugin-scripts')
    <!-- end plugin js for this page -->

    <!-- inject:js -->
    <script src="{{ asset('admin_assets/js/template.js') }}"></script>
    <!-- endinject -->

    <!-- custom js for this page -->
    <script nonce="{{ Vite::cspNonce() }}">
        $(function() {
            if ($('i[data-feather]').length > 0) {
                feather.replace();
            }

            // Theme Switcher Logic
            const themeToggle = $('#adminThemeToggle');
            const stylesheet = $('#main-stylesheet');
            
            function updateThemeIcon(theme) {
                const icon = themeToggle.find('i');
                if (theme === 'dark') {
                    icon.attr('data-feather', 'sun');
                } else {
                    icon.attr('data-feather', 'moon');
                }
                if (window.feather) feather.replace();
            }

            const currentTheme = localStorage.getItem('admin_theme') || 'light';
            updateThemeIcon(currentTheme);

            themeToggle.on('click', function(e) {
                e.preventDefault();
                const newTheme = localStorage.getItem('admin_theme') === 'dark' ? 'light' : 'dark';
                const newSheet = newTheme === 'dark' 
                    ? "{{ asset('admin_assets/css/demo_2/style.css') }}" 
                    : "{{ asset('admin_assets/css/demo_1/style.css') }}";
                
                stylesheet.attr('href', newSheet);
                if (newTheme === 'dark') {
                    document.documentElement.style.backgroundColor = '#0c1427';
                } else {
                    document.documentElement.style.backgroundColor = '#f9fafb';
                }
                document.documentElement.setAttribute('data-admin-theme', newTheme);
                localStorage.setItem('admin_theme', newTheme);
                updateThemeIcon(newTheme);
            });

            // Notifications Logic
            function fetchAdminNotifications() {
                $.get('{{ route("notifications.latest") }}', function(html) {
                    $('#adminNotificationListContent').html(html);
                }).fail(function() {
                    $('#adminNotificationListContent').html('<div class="p-3 text-center text-danger">Failed to load.</div>');
                });
            }
            $('.nav-notifications').on('show.bs.dropdown', fetchAdminNotifications);
            $('#adminNotificationMenuTrigger').on('click', fetchAdminNotifications);

            // Live Omni-Search Logic
            const searchInput = $('#navbarForm');
            if (searchInput.length > 0) {
                const searchResults = $('<div id="navbarOmniSearchResults" class="omni-search-dropdown-menu" style="display: none;"></div>');
                searchInput.closest('.search-form').append(searchResults);
                
                let searchTimeout = null;
                
                searchInput.on('input', function() {
                    const query = $(this).val().trim();
                    
                    clearTimeout(searchTimeout);
                    
                    if (query.length < 2) {
                        searchResults.hide().empty();
                        return;
                    }
                    
                    // Show premium spinner
                    searchResults.show().html('<div class="p-4 text-center"><div class="spinner-border spinner-border-sm text-primary" role="status"></div></div>');
                    
                    searchTimeout = setTimeout(function() {
                        $.get('{{ route("admin.omniSearch") }}', { q: query }, function(data) {
                            searchResults.empty();
                            
                            let hasResults = false;
                            
                            // Render Products
                            if (data.products && data.products.length > 0) {
                                hasResults = true;
                                searchResults.append('<div class="omni-search-category-title">Products</div>');
                                data.products.forEach(p => {
                                    searchResults.append(`
                                        <a href="${p.url}" class="omni-search-item">
                                            <img src="${p.image}" class="omni-search-thumb" data-fallback-src="/admin_assets/images/placeholder.png">
                                            <div class="flex-grow-1">
                                                <div class="font-weight-bold" style="font-size: 0.82rem; line-height: 1.2;">${p.name}</div>
                                                <div class="small text-muted" style="font-size: 0.72rem; margin-top: 2px;">Stock: ${p.stock} units</div>
                                            </div>
                                            <div class="font-weight-bold text-primary" style="font-size: 0.82rem;">£${p.price}</div>
                                        </a>
                                    `);
                                });
                            }
                            
                            // Render Orders
                            if (data.orders && data.orders.length > 0) {
                                hasResults = true;
                                if (searchResults.children().length > 0) searchResults.append('<div class="my-3 border-top" style="opacity: 0.08;"></div>');
                                searchResults.append('<div class="omni-search-category-title">Orders</div>');
                                data.orders.forEach(o => {
                                    searchResults.append(`
                                        <a href="${o.url}" class="omni-search-item">
                                            <div class="omni-search-avatar" style="border-radius: 8px !important;"><i class="bi bi-receipt" style="font-size: 0.95rem;"></i></div>
                                            <div class="flex-grow-1">
                                                <div class="font-weight-bold" style="font-size: 0.82rem; line-height: 1.2;">#${o.order_number}</div>
                                                <div class="small text-muted" style="font-size: 0.72rem; margin-top: 2px;">Customer: ${o.customer}</div>
                                            </div>
                                            <div class="text-right">
                                                <div class="font-weight-bold" style="font-size: 0.82rem;">£${o.total}</div>
                                                <span class="badge px-2 py-0.5 mt-1" style="font-size: 0.65rem; border-radius: 20px; background: rgba(116, 48, 137,0.08); color: #743089; font-weight: 700;">${o.status}</span>
                                            </div>
                                        </a>
                                    `);
                                });
                            }
                            
                            // Render Customers
                            if (data.customers && data.customers.length > 0) {
                                hasResults = true;
                                if (searchResults.children().length > 0) searchResults.append('<div class="my-3 border-top" style="opacity: 0.08;"></div>');
                                searchResults.append('<div class="omni-search-category-title">Customers</div>');
                                data.customers.forEach(c => {
                                    const initial = c.name.charAt(0).toUpperCase();
                                    searchResults.append(`
                                        <a href="${c.url}" class="omni-search-item">
                                            <div class="omni-search-avatar">${initial}</div>
                                            <div class="flex-grow-1">
                                                <div class="font-weight-bold" style="font-size: 0.82rem; line-height: 1.2;">${c.name}</div>
                                                <div class="small text-muted" style="font-size: 0.72rem; margin-top: 2px;">${c.email}</div>
                                            </div>
                                            <i class="bi bi-chevron-right text-muted" style="font-size: 0.75rem;"></i>
                                        </a>
                                    `);
                                });
                            }
                            
                            if (!hasResults) {
                                searchResults.html('<div class="p-4 text-center text-muted small"><i class="bi bi-info-circle mr-2"></i> No matching products, orders, or customers found.</div>');
                            }
                        }).fail(function() {
                            searchResults.html('<div class="p-4 text-center text-danger small">Failed to load search results.</div>');
                        });
                    }, 200);
                });
                
                // Keyboard navigation
                searchInput.on('keydown', function(e) {
                    const items = searchResults.find('.omni-search-item');
                    if (items.length === 0 || searchResults.is(':hidden')) {
                        return;
                    }
                    
                    let activeItem = searchResults.find('.omni-search-item.active');
                    let activeIndex = items.index(activeItem);
                    
                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        activeIndex++;
                        if (activeIndex >= items.length) {
                            activeIndex = 0;
                        }
                        items.removeClass('active');
                        const newActive = $(items[activeIndex]).addClass('active');
                        if (newActive.length > 0) {
                            newActive[0].scrollIntoView({ block: 'nearest' });
                        }
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        activeIndex--;
                        if (activeIndex < 0) {
                            activeIndex = items.length - 1;
                        }
                        items.removeClass('active');
                        const newActive = $(items[activeIndex]).addClass('active');
                        if (newActive.length > 0) {
                            newActive[0].scrollIntoView({ block: 'nearest' });
                        }
                    } else if (e.key === 'Enter') {
                        if (activeItem.length > 0) {
                            e.preventDefault();
                            window.location.href = activeItem.attr('href');
                        } else if (items.length > 0) {
                            e.preventDefault();
                            window.location.href = $(items[0]).attr('href');
                        }
                    } else if (e.key === 'Escape') {
                        e.preventDefault();
                        searchResults.hide();
                        searchInput.blur();
                    }
                });
                
                // Hide dropdown when clicking away
                $(document).on('click', function(e) {
                    if (!$(e.target).closest('.search-form').length) {
                        searchResults.hide();
                    }
                });
                
                // Re-show dropdown on focus if input has value
                searchInput.on('focus', function() {
                    if ($(this).val().trim().length >= 2) {
                        searchResults.show();
                    }
                });
            }
        });
        
        function showToast(message, type = 'success') {
            alert(message); // fallback for admin
        }
    </script>
    @stack('scripts')
    <!-- end custom js for this page -->
</body>
</html>
