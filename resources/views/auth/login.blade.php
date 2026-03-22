@extends('layouts.auth_modern')

@section('title', 'Login - Premier Shop')

@section('content')
<div class="auth-wrapper">
    <div class="auth-side-form">
        <a href="{{ url('/') }}" class="auth-brand">
            <i class="bi bi-shop"></i> Premier<span>Shop</span>
        </a>

        <div class="auth-card">
            <h2 class="auth-title">Welcome Back</h2>
            <p class="auth-subtitle">Welcome Back, Please enter Your details</p>

            <div class="auth-tabs">
                <a href="{{ route('login') }}" class="auth-tab active">Sign In</a>
                <a href="{{ route('register') }}" class="auth-tab">Signup</a>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <div class="auth-input-group">
                    <i class="bi bi-envelope auth-input-icon"></i>
                    <input type="email" name="email" class="auth-input @error('email') is-invalid @enderror" 
                           placeholder="Email Address" value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <span class="invalid-feedback d-block mt-1 small" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="auth-input-group">
                    <i class="bi bi-lock auth-input-icon"></i>
                    <input type="password" name="password" class="auth-input @error('password') is-invalid @enderror" 
                           placeholder="Password" required autocomplete="current-password">
                    @error('password')
                        <span class="invalid-feedback d-block mt-1 small" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label small text-muted" for="remember">
                            {{ __('Remember me') }}
                        </label>
                    </div>
                    @if (Route::has('password.request'))
                        <a class="small text-decoration-none text-primary fw-bold" href="{{ route('password.request') }}">
                            {{ __('Forgot Password?') }}
                        </a>
                    @endif
                </div>

                <button type="submit" class="auth-btn-primary">
                    Continue
                </button>
            </form>

            <div class="auth-footer-text">
                Join the millions of smart shoppers who trust us to manage their orders. Log in to access your personalized dashboard, track your portfolio performance, and stay updated.
            </div>
        </div>
    </div>

    <div class="auth-side-illustration">
        <div class="illustration-bg-decor"></div>
        <img src="{{ asset('images/login_3d_safe.png') }}" alt="Premier Shop Security" class="auth-illustration-img">
    </div>
</div>
@endsection
