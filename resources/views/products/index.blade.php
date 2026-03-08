@extends('layouts.app')
@section('title', 'Products — Premier Shop')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            {{-- Sidebar Filters --}}
            <div class="col-lg-3">
                <div class="card" style="position:sticky;top:90px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-funnel me-2"></i>Filters</h5>
                        <form action="{{ route('products.index') }}" method="GET">
                            {{-- Search --}}
                            <div class="mb-3">
                                <label class="form-label">Search</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                                    <input type="text" name="search" class="form-control border-start-0" placeholder="Product name..." value="{{ request('search') }}">
                                </div>
                            </div>
                            {{-- Category --}}
                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <select name="category" class="form-select">
                                    <option value="">All Categories</option>
                                    @foreach(\App\Models\Category::withCount('products')->get() as $cat)
                                        <option value="{{ $cat->slug }}" {{ request('category') == $cat->slug ? 'selected' : '' }}>
                                            {{ $cat->name }} ({{ $cat->products_count }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- Sort --}}
                            <div class="mb-4">
                                <label class="form-label">Sort By</label>
                                <select name="sort" class="form-select">
                                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low → High</option>
                                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High → Low</option>
                                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name: A-Z</option>
                                </select>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary"><i class="bi bi-filter me-1"></i> Apply Filters</button>
                                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">Clear All</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Product Grid --}}
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="section-title mb-0">
                            @if(request('category'))
                                {{ \App\Models\Category::where('slug', request('category'))->first()?->name ?? 'All' }} Products
                            @else
                                All Products
                            @endif
                        </h2>
                        <p class="text-muted mb-0">{{ $products->total() }} products found</p>
                    </div>
                </div>

                <div class="row g-4">
                    @forelse($products as $index => $product)
                        <div class="col-6 col-md-4 fade-up delay-{{ ($index % 6) + 1 }}">
                            <div class="product-card">
                                @if($product->is_age_restricted)
                                    <span class="product-badge bg-danger">🔞 16+</span>
                                @elseif($product->created_at->diffInDays(now()) < 7)
                                    <span class="product-badge bg-success">NEW</span>
                                @endif
                                <div class="product-img-wrap">
                                    @if($product->images && count($product->images) > 0)
                                        <img src="{{ $product->images[0] }}" alt="{{ $product->name }}" loading="lazy">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center h-100" style="background:linear-gradient(135deg,#f0f0f5,#e8e8f0);">
                                            <i class="bi bi-image text-muted" style="font-size:3rem;"></i>
                                        </div>
                                    @endif
                                    <div class="product-overlay">
                                        <a href="{{ route('products.show', $product->slug) }}" class="btn btn-light btn-sm"><i class="bi bi-eye"></i></a>
                                        <form action="{{ route('cart.add') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <button class="btn btn-primary btn-sm btn-add-to-cart"><i class="bi bi-bag-plus"></i></button>
                                        </form>
                                    </div>
                                </div>
                                <div class="product-body">
                                    <div class="product-category">{{ $product->category?->name }}</div>
                                    <h5 class="product-title">
                                        <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
                                    </h5>
                                    <div class="product-price">£{{ number_format($product->price, 2) }}</div>
                                </div>
                                <div class="product-footer">
                                    <div class="stock-indicator">
                                        <span class="dot {{ $product->stock > 0 ? 'dot-green' : 'dot-red' }}"></span>
                                        {{ $product->stock > 0 ? 'In Stock' : 'Out of Stock' }}
                                    </div>
                                </div>
                            </div>
                        </div>
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
