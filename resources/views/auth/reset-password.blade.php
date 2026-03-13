@extends('layouts.app')
@section('title', 'Reset Password — Premier Shop')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="text-center mb-4 fade-up">
                    <div class="d-inline-flex align-items-center justify-content-center mb-3" style="width:70px;height:70px;background:var(--ps-gradient);border-radius:20px;">
                        <i class="bi bi-key-fill text-white" style="font-size:2rem;"></i>
                    </div>
                    <h2 class="fw-bold">Reset Password</h2>
                    <p class="text-muted">Enter your new password below</p>
                </div>

                <div class="card fade-up delay-1">
                    <div class="card-body p-4 p-md-5">
                        <form method="POST" action="{{ route('password.store') }}">
                            @csrf

                            <!-- Password Reset Token -->
                            <input type="hidden" name="token" value="{{ $request->route('token') }}">

                            <!-- Email Address -->
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $request->email) }}" required autofocus placeholder="your@email.com">
                                    @error('email') 
                                        <div class="invalid-feedback">{{ $message }}</div> 
                                    @enderror
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required placeholder="••••••••">
                                    @error('password') 
                                        <div class="invalid-feedback">{{ $message }}</div> 
                                    @enderror
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-4">
                                <label class="form-label">Confirm New Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-lock-check"></i></span>
                                    <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" required placeholder="••••••••">
                                    @error('password_confirmation') 
                                        <div class="invalid-feedback">{{ $message }}</div> 
                                    @enderror
                                </div>
                            </div>

                            <button type="submit" class="btn btn-add-cart w-100">
                                <i class="bi bi-check2-circle me-2"></i> Reset Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
