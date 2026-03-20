@extends('layouts.app')
@section('title', 'Browse Categories — Premier Shop')

@section('content')
<section class="py-5">
    <div class="container">
        {{-- Header --}}
        <div class="text-center mb-5 reveal-3d">
            <span class="glass-pill mb-3 d-inline-flex">
                <i class="bi bi-grid-3x3-gap-fill text-primary"></i>
                <span class="fw-bold small">Browse by category</span>
            </span>
            <h1 class="section-title mt-3">Explore <span class="gradient-text">Categories</span></h1>
            <p class="section-subtitle text-muted mx-auto" style="max-width: 560px;">
                Find exactly what you're looking for — browse our handpicked collections.
            </p>
        </div>

        {{-- Category Grid --}}
        <div class="row g-4 stagger-children">
            @foreach($categories as $i => $category)
                <div class="col-6 col-md-4 col-lg-3 fade-up delay-{{ ($i % 8) + 1 }}">
                    <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="category-showcase-card d-block text-decoration-none">
                        <div class="category-showcase-inner">
                            {{-- Background Image or Gradient --}}
                            @if($category->image)
                                <img src="{{ $category->image }}" alt="{{ $category->name }}" class="category-showcase-bg" loading="lazy">
                            @endif
                            <div class="category-showcase-overlay"></div>

                            {{-- Content --}}
                            <div class="category-showcase-content">
                                <div class="category-showcase-icon">
                                    @if($category->image)
                                        <img src="{{ $category->image }}" alt="">
                                    @else
                                        <i class="bi bi-grid-fill"></i>
                                    @endif
                                </div>
                                <h5 class="category-showcase-title">{{ $category->name }}</h5>
                                <span class="category-showcase-count">
                                    {{ $category->products_count }} {{ Str::plural('product', $category->products_count) }}
                                </span>
                            </div>

                            {{-- Hover Shine --}}
                            <div class="category-showcase-shine"></div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        @if($categories->isEmpty())
            <div class="text-center py-5 reveal-3d">
                <div class="d-inline-flex align-items-center justify-content-center mb-4" style="width:100px;height:100px;background:rgba(108,92,231,0.08);border-radius:50%;">
                    <i class="bi bi-grid text-primary" style="font-size:3rem;"></i>
                </div>
                <h4 class="fw-bold mb-2">No categories yet</h4>
                <p class="text-muted mb-4">Categories will appear here once added.</p>
                <a href="{{ route('products.index') }}" class="btn btn-add-cart">Browse All Products</a>
            </div>
        @endif
    </div>
</section>
@endsection
