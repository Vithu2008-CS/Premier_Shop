@extends('layouts.app')
@section('title', 'Register — Premier Shop')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="text-center mb-4 reveal-3d">
                    <div class="d-inline-flex align-items-center justify-content-center mb-3" style="width:70px;height:70px;background:var(--ps-gradient);border-radius:20px;">
                        <i class="bi bi-person-plus-fill text-white" style="font-size:2rem;"></i>
                    </div>
                    <h2 class="fw-bold">Create Account</h2>
                    <p class="text-muted">Join Premier Shop and start shopping!</p>
                </div>
                <div class="card reveal-3d delay-1">
                    <div class="card-body p-4 p-md-5">
                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Full Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="John Doe">
                                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="your@email.com">
                                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Date of Birth</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-calendar"></i></span>
                                        <input type="date" name="dob" class="form-control @error('dob') is-invalid @enderror" value="{{ old('dob') }}" required>
                                        @error('dob') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-telephone"></i></span>
                                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="+44...">
                                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-geo-alt"></i></span>
                                        <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2" placeholder="Your full address">{{ old('address') }}</textarea>
                                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required placeholder="Min 8 chars">
                                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Confirm Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-lock-fill"></i></span>
                                        <input type="password" name="password_confirmation" class="form-control" required placeholder="Repeat password">
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-add-cart w-100 mt-4 mb-3">
                                <i class="bi bi-person-plus me-2"></i> Create Account
                            </button>
                        </form>
                        <div class="text-center">
                            <span class="text-muted">Already have an account?</span>
                            <a href="{{ route('login') }}" class="fw-bold text-decoration-none ms-1">Sign In</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection