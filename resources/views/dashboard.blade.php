@extends('layouts.app')
@section('title', 'Dashboard — Premier Shop')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm fade-up">
                    <div class="card-body p-5 text-center">
                        <div class="d-inline-flex align-items-center justify-content-center mb-4" style="width:100px;height:100px;background:var(--ps-gradient);border-radius:30px;">
                            <i class="bi bi-person-check-fill text-white" style="font-size:3rem;"></i>
                        </div>
                        <h2 class="fw-bold mb-3">Welcome, {{ auth()->user()->name }}!</h2>
                        <p class="text-muted mb-4 fs-5">{{ __("You're successfully logged in to your Premier Shop account.") }}</p>
                        
                        <div class="row g-3 justify-content-center mt-2">
                            <div class="col-sm-4">
                                <a href="{{ route('home') }}" class="btn btn-outline-primary w-100 p-3">
                                    <i class="bi bi-house-door d-block fs-3 mb-2"></i>
                                    Shop Now
                                </a>
                            </div>
                            <div class="col-sm-4">
                                <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary w-100 p-3">
                                    <i class="bi bi-person-gear d-block fs-3 mb-2"></i>
                                    Manage Profile
                                </a>
                            </div>
                            <div class="col-sm-4">
                                <a href="{{ route('orders.index') }}" class="btn btn-outline-primary w-100 p-3">
                                    <i class="bi bi-receipt d-block fs-3 mb-2"></i>
                                    My Orders
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
