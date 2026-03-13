@extends('layouts.app')
@section('title', '500 - Server Error — Premier Shop')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <div class="mb-5 fade-up">
                    <div class="d-inline-flex align-items-center justify-content-center mb-4" style="width:120px;height:120px;background:linear-gradient(135deg,#e17055,#d63031);border-radius:35px;box-shadow:var(--ps-shadow-lg);">
                        <i class="bi bi-exclamation-triangle text-white" style="font-size:3.5rem;"></i>
                    </div>
                    <h1 class="display-1 fw-bold text-danger mb-2">500</h1>
                    <h2 class="fw-bold text-dark mb-3">Something Went Wrong</h2>
                    <p class="text-muted fs-5 mb-5 px-lg-5">We're experiencing some technical difficulties on our end. Please try again later or contact support if the issue persists.</p>
                    
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="{{ route('home') }}" class="btn btn-add-cart px-4 py-3">
                            <i class="bi bi-house-door me-2"></i> Back to Homepage
                        </a>
                        <button onclick="location.reload()" class="btn btn-outline-secondary px-4 py-3">
                            <i class="bi bi-arrow-clockwise me-2"></i> Refresh Page
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
