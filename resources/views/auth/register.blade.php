@extends('layouts.auth_modern')

@section('title', 'Register - Premier Shop')

@section('content')
<div class="auth-wrapper">
    <div class="auth-side-form">
        <div class="auth-brand">
            <i class="bi bi-shop"></i> Premier<span>Shop</span>
        </div>

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
                By creating an account, you agree to our Terms of Service and Privacy Policy. Join our community and enjoy a seamless shopping experience.
            </div>
        </div>
    </div>

    <div class="auth-side-illustration">
        <div class="illustration-bg-decor"></div>
        <img src="{{ asset('images/login_3d_safe.png') }}" alt="Premier Shop Security" class="auth-illustration-img">
    </div>
</div>
@endsection
on