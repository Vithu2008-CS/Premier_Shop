@extends('layouts.app')
@section('title', '404 - Page Not Found — Premier Shop')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <div class="mb-5 fade-up">
                    <div class="d-inline-flex align-items-center justify-content-center mb-4" style="width:120px;height:120px;background:var(--ps-gradient);border-radius:35px;box-shadow:var(--ps-shadow-lg);">
                        <i class="bi bi-search text-white" style="font-size:3.5rem;"></i>
                    </div>
                    <h1 class="display-1 fw-bold gradient-text mb-2">404</h1>
                    <h2 class="fw-bold text-dark mb-3">Oops! Page Not Found</h2>
                    <p class="text-muted fs-5 mb-5 px-lg-5">The page you're looking for might have been moved, deleted, or never existed in the first place.</p>
                    
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="{{ route('home') }}" class="btn btn-add-cart px-4 py-3">
                            <i class="bi bi-house-door me-2"></i> Back to Homepage
                        </a>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-primary px-4 py-3">
                            <i class="bi bi-shop me-2"></i> Browse Products
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
