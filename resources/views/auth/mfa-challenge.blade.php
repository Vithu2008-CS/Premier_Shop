{{--
    auth/mfa-challenge.blade.php — second-factor login code entry
    ==============================================================
    Shown after a correct password when the account requires MFA.
    POST otp → mfa.challenge (MfaChallengeController::store) completes login.
    Resend → POST mfa.resend (throttle:login). Not yet authenticated here.
--}}
@extends('layouts.app')
@section('title', 'Verify Sign-In - Premier Shop')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card">
                <div class="card-body p-4 p-md-5 text-center">
                    {{-- Icon --}}
                    <div class="mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width:80px;height:80px;background:#743089;">
                            <i class="bi bi-shield-lock text-white" style="font-size:2rem;"></i>
                        </div>
                    </div>

                    <h2 class="fw-bold mb-2" style="color:#743089;">Two-Factor Sign-In</h2>
                    <p class="text-muted mb-4">
                        We've sent a 6-digit code to<br>
                        <strong class="text-dark">{{ $maskedEmail }}</strong>
                    </p>

                    @if (session('error'))
                        <div class="alert alert-danger py-2">{{ session('error') }}</div>
                    @endif
                    @if (session('status'))
                        <div class="alert alert-success py-2">{{ session('status') }}</div>
                    @endif

                    <form method="POST" action="{{ route('mfa.challenge') }}">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-bold">Enter Sign-In Code</label>
                            <input
                                type="text"
                                name="otp"
                                inputmode="numeric"
                                autocomplete="one-time-code"
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
                            <i class="bi bi-check-circle me-1"></i> Verify & Sign In
                        </button>
                    </form>

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Didn't receive the code?</span>
                        <form method="POST" action="{{ route('mfa.resend') }}">
                            @csrf
                            <button type="submit" class="btn btn-link btn-sm p-0 fw-bold">Resend Code</button>
                        </form>
                    </div>

                    <hr class="my-3">
                    <a href="{{ route('login') }}" class="text-muted small">
                        <i class="bi bi-arrow-left me-1"></i> Back to login
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
