{{--
    layouts/driver.blade.php — Driver portal layout
    =================================================
    Deep dark premium theme. Sticky glass navbar with live duty status dot,
    avatar dropdown, flash messages, @yield('content'), minimal footer.
--}}
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Driver Panel — Premier Shop')</title>
    <script src="{{ asset('js/csp-shim.js') }}" defer></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" media="print" data-media-onload="all">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @stack('styles')
    <style>
        /* ── Driver Portal Base ───────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; }

        body {
            background: #0b0a18;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            color: #e0dff5;
            background-image:
                radial-gradient(ellipse 80% 50% at 20% -10%, rgba(116, 48, 137,0.08) 0%, transparent 60%),
                radial-gradient(ellipse 60% 40% at 80% 110%, rgba(90, 30, 107,0.05) 0%, transparent 60%);
        }

        /* ── Navbar ───────────────────────────────────────────── */
        .driver-nav {
            background: rgba(11,10,24,0.92);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-bottom: 1px solid rgba(255,255,255,0.055);
            padding: 10px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .driver-brand {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 1.1rem;
            color: #ffffff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .driver-brand:hover { color: #A45FBF; }
        .brand-icon {
            width: 36px;
            height: 36px;
            border-radius: 11px;
            background: linear-gradient(135deg, #743089 0%, #5A1E6B 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            box-shadow: 0 4px 14px rgba(116, 48, 137,0.45);
            flex-shrink: 0;
        }
        .driver-role-badge {
            background: linear-gradient(135deg, #743089, #5A1E6B);
            color: #fff;
            font-size: 0.58rem;
            font-weight: 800;
            letter-spacing: 1.2px;
            padding: 2px 8px;
            border-radius: 100px;
            font-family: 'Outfit', sans-serif;
            vertical-align: middle;
        }

        /* duty pill */
        .duty-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 100px;
            font-size: 0.75rem;
            font-weight: 700;
            font-family: 'Outfit', sans-serif;
        }
        .duty-pill.on  { background: rgba(0,184,148,0.12); color: #00cec9; border: 1px solid rgba(0,184,148,0.25); }
        .duty-pill.off { background: rgba(99,110,114,0.12); color: #b2bec3; border: 1px solid rgba(99,110,114,0.2); }
        .duty-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: currentColor;
            flex-shrink: 0;
        }
        .duty-pill.on .duty-dot {
            animation: duty-pulse 2s ease-in-out infinite;
        }
        @keyframes duty-pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(0,184,148,0.5); }
            50%       { box-shadow: 0 0 0 5px rgba(0,184,148,0); }
        }

        /* avatar btn */
        .driver-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #743089, #A45FBF);
            border: 2px solid rgba(164, 95, 191,0.35);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.82rem;
            cursor: pointer;
            font-family: 'Outfit', sans-serif;
            transition: transform 0.2s ease, border-color 0.2s ease;
        }
        .driver-avatar:hover {
            transform: scale(1.08);
            border-color: rgba(164, 95, 191,0.6);
        }

        /* dropdown */
        .driver-dropdown {
            background: #16152a;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 18px;
            box-shadow: 0 24px 64px rgba(0,0,0,0.7), 0 0 0 1px rgba(116, 48, 137,0.08);
            overflow: hidden;
            min-width: 220px;
            padding: 6px;
        }
        .driver-dropdown .dd-header {
            padding: 12px 14px;
            border-bottom: 1px solid rgba(255,255,255,0.055);
            margin-bottom: 4px;
        }
        .driver-dropdown .dropdown-item {
            color: rgba(255,255,255,0.7);
            padding: 9px 14px;
            border-radius: 12px;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.18s;
        }
        .driver-dropdown .dropdown-item:hover { background: rgba(255,255,255,0.06); color: #fff; }
        .driver-dropdown .dropdown-item.text-danger { color: #ff7675 !important; }
        .driver-dropdown .dropdown-item.text-danger:hover { background: rgba(255,118,117,0.1); color: #ff6b6b !important; }

        /* ── Flash ────────────────────────────────────────────── */
        .driver-flash-success {
            background: rgba(0,184,148,0.1);
            border: 1px solid rgba(0,184,148,0.2);
            color: #55efc4;
            border-radius: 14px;
            padding: 14px 18px;
            font-size: 0.88rem;
        }
        .driver-flash-error {
            background: rgba(255,118,117,0.1);
            border: 1px solid rgba(255,118,117,0.2);
            color: #ff7675;
            border-radius: 14px;
            padding: 14px 18px;
            font-size: 0.88rem;
        }

        /* ── Footer ───────────────────────────────────────────── */
        .driver-footer {
            border-top: 1px solid rgba(255,255,255,0.04);
            padding: 22px 0;
            margin-top: 60px;
            text-align: center;
            color: rgba(255,255,255,0.2);
            font-size: 0.78rem;
        }

        /* ── Navbar mobile ─────────────────────────────────────── */
        @media (max-width: 575px) {
            .driver-nav { padding: 8px 0; }
            .driver-brand { font-size: 0.95rem; }
            .brand-icon { width: 30px; height: 30px; font-size: 0.85rem; border-radius: 9px; }
            .driver-role-badge { font-size: 0.52rem; padding: 2px 6px; }
            .driver-avatar { width: 32px; height: 32px; font-size: 0.75rem; }
        }
    </style>
</head>
<body>

    {{-- ── Navbar ── --}}
    <nav class="driver-nav">
        <div class="container d-flex align-items-center justify-content-between gap-3">
            <a href="{{ route('driver.dashboard') }}" class="driver-brand">
                <div class="brand-icon"><i class="bi bi-truck-front-fill"></i></div>
                <div class="d-flex align-items-center gap-2">
                    <span>Premier Shop</span>
                    <span class="driver-role-badge">DRIVER</span>
                </div>
            </a>

            <div class="d-flex align-items-center gap-3">
                @auth
                    @if(auth()->user()->is_on_duty ?? false)
                        <span class="duty-pill on d-none d-sm-inline-flex">
                            <span class="duty-dot"></span>On Duty
                        </span>
                    @else
                        <span class="duty-pill off d-none d-sm-inline-flex">
                            <span class="duty-dot"></span>Off Duty
                        </span>
                    @endif

                    <div class="dropdown">
                        <button class="driver-avatar border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </button>
                        <div class="dropdown-menu dropdown-menu-end driver-dropdown mt-2">
                            <div class="dd-header">
                                <div class="fw-bold small text-white">{{ auth()->user()->name }}</div>
                                <div style="font-size:0.72rem;color:rgba(255,255,255,0.35);">{{ auth()->user()->email }}</div>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item text-danger w-100 text-start border-0 bg-transparent">
                                    <i class="bi bi-box-arrow-right"></i>Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    {{-- ── Flash ── --}}
    @if(session('success'))
        <div class="container mt-3">
            <div class="driver-flash-success d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill"></i>{{ session('success') }}
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="container mt-3">
            <div class="driver-flash-error d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-circle-fill"></i>{{ session('error') }}
            </div>
        </div>
    @endif

    <main>
        @yield('content')
    </main>

    <footer class="driver-footer">
        &copy; {{ date('Y') }} Premier Shop Driver Network
    </footer>

    @stack('scripts')
</body>
</html>
