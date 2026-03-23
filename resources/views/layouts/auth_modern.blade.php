<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Styles -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        :root {
            --auth-primary: #0066FF;
            --auth-bg-light: #F8F9FA;
            --auth-text-dark: #1A1A1A;
            --auth-text-muted: #6C757D;
            --auth-input-bg: #FFFFFF;
            --auth-border: #E9ECEF;
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
            background: #fff;
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
            background: linear-gradient(135deg, #E3F2FD 0%, #BBDEFB 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
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
            background: #F1F3F5;
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
            background: #fff;
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

        .auth-btn-primary:hover {
            background: #0052CC;
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
            background-image: radial-gradient(circle at 20% 30%, rgba(255,255,255,0.4) 0%, transparent 40%),
                              radial-gradient(circle at 80% 70%, rgba(255,255,255,0.3) 0%, transparent 40%);
        }
    </style>
</head>
<body>
    @yield('content')
</body>
</html>
