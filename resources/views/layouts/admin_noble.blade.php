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
    
    <!-- core:css -->
    <link rel="stylesheet" href="{{ asset('admin_assets/vendors/core/core.css') }}">
    <!-- endinject -->
    
    <!-- plugin css for this page -->
    @stack('plugin-styles')
    <!-- end plugin css for this page -->
    
    <!-- inject:css -->
    <link rel="stylesheet" href="{{ asset('admin_assets/fonts/feather-font/css/iconfont.css') }}">
    <link rel="stylesheet" href="{{ asset('admin_assets/vendors/flag-icon-css/css/flag-icon.min.css') }}">
    <!-- endinject -->
    
    {{-- Pre-render Theme Logic & Background to prevent Flash of Unstyled Content (FOUC) --}}
    <script>
        (function() {
            const savedTheme = localStorage.getItem('admin_theme') || 'light';
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
    <script>
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
                localStorage.setItem('admin_theme', newTheme);
                updateThemeIcon(newTheme);
            });

            // Notifications Logic
            $('#adminNotificationMenuTrigger').on('show.bs.dropdown', function () {
                $.get('{{ route("notifications.latest") }}', function(html) {
                    $('#adminNotificationListContent').html(html);
                }).fail(function() {
                    $('#adminNotificationListContent').html('<div class="p-3 text-center text-danger">Failed to load.</div>');
                });
            });
        });
        
        function showToast(message, type = 'success') {
            alert(message); // fallback for admin
        }
    </script>
    @stack('scripts')
    <!-- end custom js for this page -->
</body>
</html>
