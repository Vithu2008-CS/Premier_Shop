@extends('layouts.app')
@section('title', 'Verify Email - Premier Shop')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card">
                <div class="card-body p-4 p-md-5 text-center">
                    {{-- Icon --}}
                    <div class="mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width:80px;height:80px;background:linear-gradient(135deg,#6C5CE7,#A29BFE);">
                            <i class="bi bi-envelope-check text-white" style="font-size:2rem;"></i>
                        </div>
                    </div>

                    <h2 class="fw-bold mb-2" style="background:linear-gradient(135deg,#6C5CE7,#00CEC9);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Check Your Email</h2>
                    <p class="text-muted mb-4">
                        We've sent a 6-digit verification code to<br>
                        <strong class="text-dark">{{ session('registration_data.email') }}</strong>
                    </p>

                    <form method="POST" action="{{ route('register.verify.submit') }}">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-bold">Enter Verification Code</label>
                            <input
                                type="text"
                                name="otp"
                                class="form-control form-control-lg text-center fw-bold @error('otp') is-invalid @enderror"
                                maxlength="6"
                                placeholder="● ● ● ● ● ●"
                                style="letter-spacing:8px;font-size:1.5rem;"
                                autofocus
                                required
                            >
                            @error('otp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100 btn-lg mb-3">
                            <i class="bi bi-check-circle me-1"></i> Verify & Create Account
                        </button>
                    </form>

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Didn't receive the code?</span>
                        <form method="POST" action="{{ route('register.resendOtp') }}">
                            @csrf
                            <button type="submit" class="btn btn-link btn-sm p-0 fw-bold">Resend Code</button>
                        </form>
                    </div>

                    <hr class="my-3">
                    <a href="{{ route('register') }}" class="text-muted small">
                        <i class="bi bi-arrow-left me-1"></i> Back to registration
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
