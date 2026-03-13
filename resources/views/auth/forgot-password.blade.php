@extends('layouts.app')
@section('title', 'Forgot Password — Premier Shop')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="text-center mb-4 fade-up">
                    <div class="d-inline-flex align-items-center justify-content-center mb-3" style="width:70px;height:70px;background:var(--ps-gradient);border-radius:20px;">
                        <i class="bi bi-shield-lock text-white" style="font-size:2rem;"></i>
                    </div>
                    <h2 class="fw-bold">Forgot Password?</h2>
                    <p class="text-muted px-lg-4">No problem. Just enter your email and we'll send you a password reset link.</p>
                </div>

                <div class="card fade-up delay-1">
                    <div class="card-body p-4 p-md-5">
                        <!-- Session Status -->
                        @if (session('status'))
                            <div class="alert alert-success mb-4" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <!-- Email Address -->
                            <div class="mb-4">
                                <label class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus placeholder="your@email.com">
                                    @error('email') 
                                        <div class="invalid-feedback">{{ $message }}</div> 
                                    @enderror
                                </div>
                            </div>

                            <button type="submit" class="btn btn-add-cart w-100 mb-3">
                                <i class="bi bi-send-fill me-2"></i> Email Password Reset Link
                            </button>
                        </form>

                        <div class="text-center">
                            <a href="{{ route('login') }}" class="text-decoration-none small fw-bold">
                                <i class="bi bi-arrow-left me-1"></i> Back to Login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
