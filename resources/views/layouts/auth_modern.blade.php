{{--
    layouts/auth_modern.blade.php — Authentication pages layout
    =============================================================
    Used by: login, register, OTP verify, forgot-password, reset-password, confirm-password.

    Two-column split design:
     - Left: auth-side-form — the actual form (scrollable, hidden scrollbar)
     - Right: auth-side-illustration — decorative image panel (hidden below lg breakpoint)

    Inline CSS handles the split layout and all auth component styles.
    Dark/light theme pre-render script syncs with the user's saved theme preference.
    @yield('content') — entire page content (form + illustration) rendered by each auth view.
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Laravel'))</title>
    <script src="{{ asset('js/csp-shim.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Styles -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    {{-- Pre-render Theme Logic --}}
    <script nonce="{{ Vite::cspNonce() }}">
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>

    <style>
        :root {
            --auth-primary: #743089;
            --auth-bg-light: var(--ps-surface-secondary);
            --auth-text-dark: var(--ps-text);
            --auth-text-muted: var(--ps-text-muted);
            --auth-input-bg: var(--ps-card-bg);
            --auth-border: var(--ps-border);
            --auth-form-bg: var(--ps-surface-bg);
            --auth-glass-bg: var(--ps-surface-glass);
        }

        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Inter', sans-serif;
            overflow: hidden;
        }

        .auth-wrapper {
            display: flex;
            height: 100vh;
            width: 100vw;
        }

        .auth-side-form {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--auth-form-bg);
            padding: 2.5rem;
            position: relative;
            overflow-y: auto;
            max-height: 100vh;
        }

        @media (max-width: 576px) {
            .auth-side-form {
                padding: 1.5rem;
                align-items: flex-start;
                padding-top: 80px;
            }
        }

        .auth-side-form::-webkit-scrollbar {
            display: none;
        }

        .auth-side-form {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .auth-side-illustration {
            flex: 1;
            background:
                radial-gradient(circle at 18% 22%, rgba(255,221,0,0.18), transparent 42%),
                radial-gradient(circle at 82% 80%, rgba(168,95,191,0.45), transparent 45%),
                linear-gradient(140deg, #8E3CA6 0%, #743089 52%, #5A1E6B 100%);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        .auth-side-illustration::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(rgba(255,255,255,0.06) 1px, transparent 1px);
            background-size: 22px 22px;
            opacity: 0.5;
            pointer-events: none;
        }
        .auth-side-illustration::after {
            content: '';
            position: absolute;
            width: 320px; height: 320px;
            right: -90px; top: -90px;
            background: radial-gradient(circle, rgba(255,221,0,0.22), transparent 70%);
            filter: blur(8px);
            border-radius: 50%;
            pointer-events: none;
        }

        @media (max-width: 992px) {
            .auth-side-illustration {
                display: none;
            }
        }

        .auth-card {
            width: 100%;
            max-width: 420px;
        }

        .auth-brand {
            position: absolute;
            top: 40px;
            left: 40px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: 'Outfit', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--auth-text-dark);
            text-decoration: none;
        }

        @media (max-width: 576px) {
            .auth-brand {
                left: 24px;
                top: 24px;
                font-size: 1.25rem;
            }
        }

        .auth-brand i {
            font-size: 1.8rem;
            color: var(--auth-primary);
        }

        .auth-title {
            font-family: 'Outfit', sans-serif;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--auth-text-dark);
            text-align: center;
        }

        @media (max-width: 576px) {
            .auth-title {
                font-size: 2rem;
            }
        }

        .auth-subtitle {
            color: var(--auth-text-muted);
            margin-bottom: 2.5rem;
            font-size: 0.95rem;
            text-align: center;
        }

        .auth-tabs {
            display: flex;
            background: var(--auth-bg-light);
            padding: 5px;
            border-radius: 12px;
            margin-bottom: 2rem;
        }

        .auth-tab {
            flex: 1;
            text-align: center;
            padding: 10px;
            border-radius: 9px;
            text-decoration: none;
            color: var(--auth-text-muted);
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .auth-tab.active {
            background: var(--auth-input-bg);
            color: var(--auth-text-dark);
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .auth-input-group {
            position: relative;
            margin-bottom: 1.25rem;
        }

        .auth-input {
            width: 100%;
            padding: 15px 15px 15px 50px;
            border: 1.5px solid var(--auth-border);
            border-radius: 12px;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.3s ease;
            box-sizing: border-box;
        }

        .auth-input:focus {
            border-color: var(--auth-primary);
            box-shadow: 0 0 0 4px var(--ps-accent-soft);
        }

        .auth-input-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--auth-text-muted);
            font-size: 1.2rem;
        }

        .auth-btn-primary {
            width: 100%;
            padding: 16px;
            background: var(--auth-primary);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
            margin-top: 1rem;
        }

        .auth-btn-primary {
            box-shadow: 0 8px 22px var(--ps-primary-glow);
        }

        .auth-btn-primary:hover {
            background: #5A1E6B;
            transform: translateY(-2px);
            box-shadow: 0 12px 30px var(--ps-primary-glow);
        }

        .auth-footer-text {
            margin-top: 3rem;
            font-size: 0.8rem;
            color: var(--auth-text-muted);
            line-height: 1.5;
            text-align: justify;
        }

        .auth-illustration-img {
            max-width: 80%;
            height: auto;
            position: relative;
            z-index: 2;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }

        .illustration-bg-decor {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            background-image: none;
        }

        /* Branded showcase panel (white text + yellow accents on purple) */
        .auth-showcase {
            position: relative;
            z-index: 2;
            max-width: 440px;
            padding: 2rem;
            color: #fff;
        }
        .auth-showcase-badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 100px;
            background: var(--ps-accent);
            color: var(--ps-on-accent);
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 0.75rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            margin-bottom: 1.5rem;
        }
        .auth-showcase-title {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 2.6rem;
            line-height: 1.12;
            margin-bottom: 1rem;
            color: #fff;
        }
        .auth-showcase-title .accent { color: var(--ps-accent); }
        .auth-showcase-sub {
            font-size: 1.02rem;
            line-height: 1.6;
            color: rgba(255,255,255,0.82);
            margin-bottom: 2rem;
        }
        .auth-showcase-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: grid;
            gap: 0.9rem;
        }
        .auth-showcase-list li {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.98rem;
            font-weight: 500;
            color: rgba(255,255,255,0.92);
        }
        .auth-showcase-list i {
            color: var(--ps-accent);
            font-size: 1.25rem;
            flex-shrink: 0;
        }
    </style>
</head>
<body>
    @yield('content')
    
    <script nonce="{{ Vite::cspNonce() }}">
        document.addEventListener('DOMContentLoaded', function() {
            // Re-apply theme checks if needed, but pre-render script handles it.
            // This is mainly to ensure all components react if we added switchers here.
        });
    </script>
</body>
</html>
