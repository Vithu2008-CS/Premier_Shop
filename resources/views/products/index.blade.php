@extends('layouts.app')
@section('title', 'Products — Premier Shop')

@section('content')
    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                {{-- Mobile Filter Toggle --}}
                <div class="col-12 d-lg-none reveal-fade">
                    <button class="btn btn-outline-primary w-100 d-flex justify-content-between align-items-center py-3 rounded-4" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas">
                        <span><i class="bi bi-funnel me-2"></i>Filters</span>
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>

                {{-- Sidebar Filters (Desktop) --}}
                <div class="col-lg-3 d-none d-lg-block reveal-slide-left">
                    <div class="card" style="position:sticky;top:90px;overflow:visible;">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3"><i class="bi bi-funnel me-2"></i>Filters</h5>
                            @include('products.partials.filters', ['isMobile' => false])
                        </div>
                    </div>
                </div>

                {{-- Filter Off-canvas (Mobile) --}}
                <div class="offcanvas offcanvas-end filter-offcanvas d-lg-none" tabindex="-1" id="filterOffcanvas">
                    <div class="offcanvas-header">
                        <h5 class="fw-bold mb-0"><i class="bi bi-funnel me-2"></i>Filters</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">
                        @include('products.partials.filters', ['isMobile' => true])
                    </div>
                </div>

                {{-- Product Grid --}}
                <div class="col-lg-9">
                    <div class="d-flex justify-content-between align-items-center mb-4 reveal-slide-right">
                        <div>
                            <h2 class="section-title mb-0">
                                @if(request('category'))
                                    {{ \App\Models\Category::where('slug', request('category'))->first()?->name ?? 'All' }}
                                    Products
                                @else
                                    All Products
                                @endif
                            </h2>
                            <p class="text-muted mb-0">{{ $products->total() }} products found</p>
                        </div>
                    </div>

                    <div class="row g-4 stagger-children">
                        @forelse($products as $index => $product)
                            @include('partials.product_card', ['delay' => ($index % 6) + 1])
                        @empty
                            <div class="col-12 text-center py-5">
                                <i class="bi bi-search text-muted" style="font-size:4rem;"></i>
                                <h5 class="mt-3 fw-bold">No products found</h5>
                                <p class="text-muted">Try adjusting your search or filters</p>
                                <a href="{{ route('products.index') }}" class="btn btn-primary">View All Products</a>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-4">{{ $products->withQueryString()->links() }}</div>
                </div>
            </div>
        </div>
    </section>
@endsection