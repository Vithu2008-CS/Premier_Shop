@extends('layouts.app')
@section('title', 'Verify Email — Premier Shop')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="text-center mb-4 fade-up">
                    <div class="d-inline-flex align-items-center justify-content-center mb-3" style="width:70px;height:70px;background:var(--ps-gradient);border-radius:20px;">
                        <i class="bi bi-envelope-check-fill text-white" style="font-size:2rem;"></i>
                    </div>
                    <h2 class="fw-bold">Check Your Inbox</h2>
                    <p class="text-muted">Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you?</p>
                </div>

                <div class="card fade-up delay-1">
                    <div class="card-body p-4 p-md-5">
                        @if (session('status') == 'verification-link-sent')
                            <div class="alert alert-success mb-4" role="alert">
                                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                            </div>
                        @endif

                        <div class="d-grid gap-3">
                            <form method="POST" action="{{ route('verification.send') }}">
                                @csrf
                                <button type="submit" class="btn btn-add-cart w-100">
                                    <i class="bi bi-send-fill me-2"></i> Resend Verification Email
                                </button>
                            </form>

                            <form method="POST" action="{{ route('logout') }}" class="text-center">
                                @csrf
                                <button type="submit" class="btn btn-link text-decoration-none text-muted small fw-bold">
                                    <i class="bi bi-box-arrow-right me-1"></i> Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
