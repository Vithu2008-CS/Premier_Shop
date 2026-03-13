<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Driver Panel — Premier Shop')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="bg-light">
    {{-- Driver Navbar --}}
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('driver.dashboard') }}">
                <i class="bi bi-truck me-2"></i>Premier Shop <span class="badge bg-primary ms-2 small" style="font-size: 0.7rem;">DRIVER</span>
            </a>
            
            <div class="d-flex align-items-center gap-3">
                @auth
                    <div class="dropdown">
                        <button class="btn btn-link text-white text-decoration-none dropdown-toggle p-0" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle fs-5"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 mt-2">
                            <li class="px-3 py-2 border-bottom">
                                <div class="fw-bold small">{{ auth()->user()->name }}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">{{ auth()->user()->email }}</div>
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item text-danger py-2">
                                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <main>
        @yield('content')
    </main>

    <footer class="py-4 mt-5 bg-white border-top">
        <div class="container text-center">
            <p class="text-muted small mb-0">&copy; {{ date('Y') }} Premier Shop Driver Network. All rights reserved.</p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
