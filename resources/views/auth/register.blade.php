{{--
    auth/register.blade.php — Registration form
    =============================================
    Uses auth_modern split layout.
    Fields: name, email, DOB, password, confirm password.
    POST → RegisteredUserController::store() → sends OTP email → redirects to verify-otp.
    Password strength rules enforced (min 12, mixed case, symbols, uncompromised).
--}}
@extends('layouts.auth_modern')

@section('title', 'Register - Premier Shop')

@section('content')
<div class="auth-wrapper">
    <div class="auth-side-form">
        <a href="{{ url('/') }}" class="auth-brand">
            <i class="bi bi-shop"></i> Premier<span>Shop</span>
        </a>

        <div class="auth-card" style="margin-top: 100px; margin-bottom: 50px;">
            <h2 class="auth-title">Create Account</h2>
            <p class="auth-subtitle">Join Premier Shop today!</p>

            <div class="auth-tabs">
                <a href="{{ route('login') }}" class="auth-tab">Sign In</a>
                <a href="{{ route('register') }}" class="auth-tab active">Signup</a>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf
                
                <div class="auth-input-group">
                    <i class="bi bi-person auth-input-icon"></i>
                    <input type="text" name="name" class="auth-input @error('name') is-invalid @enderror" 
                           placeholder="Full Name" value="{{ old('name') }}" required autofocus>
                    @error('name')
                        <span class="invalid-feedback d-block mt-1 small" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="auth-input-group">
                    <i class="bi bi-envelope auth-input-icon"></i>
                    <input type="email" name="email" class="auth-input @error('email') is-invalid @enderror" 
                           placeholder="Email Address" value="{{ old('email') }}" required>
                    @error('email')
                        <span class="invalid-feedback d-block mt-1 small" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <div class="auth-input-group mb-0">
                            <i class="bi bi-calendar auth-input-icon"></i>
                            <input type="date" name="dob" class="auth-input @error('dob') is-invalid @enderror" 
                                   value="{{ old('dob') }}" required style="padding-left: 45px;">
                        </div>
                        @error('dob')
                            <span class="invalid-feedback d-block mt-1 small" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <div class="auth-input-group mb-0">
                            <i class="bi bi-telephone auth-input-icon"></i>
                            <input type="text" name="phone" class="auth-input @error('phone') is-invalid @enderror" 
                                   placeholder="Phone Number" value="{{ old('phone') }}" style="padding-left: 45px;">
                        </div>
                        @error('phone')
                            <span class="invalid-feedback d-block mt-1 small" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="auth-input-group">
                    <i class="bi bi-geo-alt auth-input-icon"></i>
                    <textarea name="address" class="auth-input @error('address') is-invalid @enderror" 
                              placeholder="Full Address" rows="2" style="height: auto; padding-top: 15px;">{{ old('address') }}</textarea>
                    @error('address')
                        <span class="invalid-feedback d-block mt-1 small" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <div class="auth-input-group mb-0">
                            <i class="bi bi-lock auth-input-icon"></i>
                            <input type="password" name="password" class="auth-input @error('password') is-invalid @enderror" 
                                   placeholder="Password" required style="padding-left: 45px;">
                        </div>
                        @error('password')
                            <span class="invalid-feedback d-block mt-1 small" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <div class="auth-input-group mb-0">
                            <i class="bi bi-lock-fill auth-input-icon"></i>
                            <input type="password" name="password_confirmation" class="auth-input" 
                                   placeholder="Confirm" required style="padding-left: 45px;">
                        </div>
                    </div>
                </div>

                <button type="submit" class="auth-btn-primary">
                    Create Account
                </button>
            </form>

            <div class="auth-footer-text">
                By creating an account, you agree to our <a href="{{ route('terms') }}" class="text-primary text-decoration-none fw-bold">Terms of Service</a> and <a href="{{ route('privacy') }}" class="text-primary text-decoration-none fw-bold">Privacy Policy</a>. Join our community and enjoy a seamless shopping experience.
            </div>
        </div>
    </div>

    <div class="auth-side-illustration">
        <div class="auth-showcase">
            <span class="auth-showcase-badge">Join Premier Shop</span>
            <h2 class="auth-showcase-title">Start shopping<br><span class="accent">smarter today.</span></h2>
            <p class="auth-showcase-sub">Create your free account and unlock member pricing, faster checkout, and rewards on every order.</p>
            <ul class="auth-showcase-list">
                <li><i class="bi bi-star-fill"></i> Earn loyalty points on every purchase</li>
                <li><i class="bi bi-truck"></i> Track orders &amp; deliveries in real time</li>
                <li><i class="bi bi-heart-fill"></i> Save favourites to your wishlist</li>
            </ul>
        </div>
    </div>
</div>
@endsection