@extends('layouts.app')
@section('title', 'Premier Shop — Your One-Stop Shop for Quality Products')

@section('content')
{{-- Hero --}}
<section class="hero-section">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="container hero-content">
        <div class="row align-items-center">
            <div class="col-lg-7 fade-up">
                <div class="hero-promo-badge">🔥 Free delivery on orders over £50</div>
                <h1>Your One-Stop Shop<br>for <span class="gradient-text">Premium Quality</span></h1>
                <p class="hero-text">Discover amazing products from electronics to groceries — all at unbeatable prices with fast, reliable delivery to your door.</p>
                <div class="d-flex gap-3 mt-4 flex-wrap">
                    <a href="{{ route('offers') }}" class="btn btn-add-cart"><i class="bi bi-tag me-2"></i>View Offers</a>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-light" style="border-radius:50px;"><i class="bi bi-grid me-2"></i>Browse Products</a>
                </div>
                <div class="hero-stats">
                    <div class="stat"><h3>{{ \App\Models\Product::where('is_active', true)->count() }}+</h3><span>Products</span></div>
                    <div class="stat"><h3>{{ \App\Models\Category::count() }}</h3><span>Categories</span></div>
                    <div class="stat"><h3>24/7</h3><span>Support</span></div>
                    <div class="stat"><h3>Free</h3><span>Over £50</span></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Offers Banner --}}
@if(isset($offerProducts) && $offerProducts->count() > 0)
<section class="py-5 fade-up">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="section-title"><i class="bi bi-lightning-fill text-warning me-2"></i>Hot <span class="gradient-text">Offers</span></h2>
                <p class="section-subtitle mb-0">Buy in bulk and save big!</p>
            </div>
            <a href="{{ route('offers') }}" class="btn btn-outline-primary" style="border-radius:50px;">See All Offers <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
        <div class="row g-4">
            @foreach($offerProducts as $i => $product)
                <div class="col-6 col-md-3 fade-up delay-{{ $i + 1 }}">
                    <div class="product-card">
                        <span class="product-badge bg-danger"><i class="bi bi-lightning-fill"></i> {{ number_format($product->offer_discount_percent) }}% OFF</span>
                        <div class="product-img-wrap">
                            @if($product->images && count($product->images) > 0)
                                <img src="{{ $product->images[0] }}" alt="{{ $product->name }}" loading="lazy">
                            @else
                                <div class="d-flex align-items-center justify-content-center h-100" style="background:#f0f0f5;"><i class="bi bi-image text-muted" style="font-size:2.5rem;"></i></div>
                            @endif
                            <div class="product-overlay">
                                <a href="{{ route('products.show', $product->slug) }}" class="btn btn-light btn-sm"><i class="bi bi-eye"></i> View</a>
                            </div>
                        </div>
                        <div class="product-body">
                            <div class="product-category">{{ $product->category?->name }}</div>
                            <h5 class="product-title"><a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a></h5>
                            <div class="product-price">
                                £{{ number_format($product->offer_price, 2) }}
                                <span class="original-price">£{{ number_format($product->price, 2) }}</span>
                            </div>
                            <span class="badge mt-1" style="background:rgba(0,206,201,0.1);color:#00CEC9;font-size:0.7rem;">Buy {{ $product->offer_min_qty }}+ save {{ number_format($product->offer_discount_percent) }}%</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Categories --}}
<section class="py-5 bg-light fade-up">
    <div class="container">
        <h2 class="section-title text-center mb-4">Browse by <span class="gradient-text">Category</span></h2>
        <div class="row g-3 justify-content-center">
            @php
                $icons = ['Electronics' => 'bi-laptop', 'Clothing' => 'bi-bag', 'Groceries' => 'bi-basket3', 'Beverages' => 'bi-cup-straw', 'Home & Garden' => 'bi-house-heart'];
            @endphp
            @foreach($categories as $cat)
                <div class="col-4 col-md-2 fade-up delay-{{ $loop->index + 1 }}">
                    <a href="{{ route('products.index', ['category' => $cat->slug]) }}" class="category-card">
                        <div class="cat-icon"><i class="bi {{ $icons[$cat->name] ?? 'bi-box' }}"></i></div>
                        <div class="cat-name">{{ $cat->name }}</div>
                        <div class="cat-count">{{ $cat->products_count }} items</div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Featured Products --}}
<section class="py-5 fade-up">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title">Featured <span class="gradient-text">Products</span></h2>
            <a href="{{ route('products.index') }}" class="btn btn-outline-primary" style="border-radius:50px;">View All <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
        <div class="row g-4">
            @foreach($featuredProducts as $i => $product)
                <div class="col-6 col-md-3 fade-up delay-{{ ($i % 4) + 1 }}">
                    <div class="product-card">
                        @if($product->created_at->gt(now()->subDays(7)))
                            <span class="product-badge" style="background:var(--ps-gradient);">NEW</span>
                        @endif
                        @if($product->has_offer)
                            <span class="product-badge bg-danger">{{ number_format($product->offer_discount_percent) }}% OFF</span>
                        @endif
                        <div class="product-img-wrap">
                            @if($product->images && count($product->images) > 0)
                                <img src="{{ $product->images[0] }}" alt="{{ $product->name }}" loading="lazy">
                            @else
                                <div class="d-flex align-items-center justify-content-center h-100" style="background:#f0f0f5;"><i class="bi bi-image text-muted" style="font-size:2.5rem;"></i></div>
                            @endif
                            <div class="product-overlay">
                                <a href="{{ route('products.show', $product->slug) }}" class="btn btn-light btn-sm"><i class="bi bi-eye me-1"></i>View</a>
                                <form action="{{ route('cart.add') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button class="btn btn-primary btn-sm btn-add-to-cart"><i class="bi bi-bag-plus me-1"></i>Add</button>
                                </form>
                            </div>
                        </div>
                        <div class="product-body">
                            <div class="product-category">{{ $product->category?->name }}</div>
                            <h5 class="product-title"><a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a></h5>
                            <div class="product-price">
                                £{{ number_format($product->price, 2) }}
                            </div>
                        </div>
                        <div class="product-footer">
                            <div class="stock-indicator">
                                <span class="dot {{ $product->stock > 0 ? 'dot-green' : 'dot-red' }}"></span>
                                {{ $product->stock > 0 ? 'In Stock' : 'Out of Stock' }}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Trust Bar --}}
<section class="trust-bar fade-up">
    <div class="container">
        <div class="row g-4">
            <div class="col-6 col-md-3">
                <div class="trust-item">
                    <div class="trust-icon"><i class="bi bi-truck"></i></div>
                    <h6>Free Delivery</h6>
                    <small>On orders over £50</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="trust-item">
                    <div class="trust-icon"><i class="bi bi-shield-check"></i></div>
                    <h6>Secure Checkout</h6>
                    <small>100% secure payments</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="trust-item">
                    <div class="trust-icon"><i class="bi bi-arrow-counterclockwise"></i></div>
                    <h6>Easy Returns</h6>
                    <small>30-day return policy</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="trust-item">
                    <div class="trust-icon"><i class="bi bi-headset"></i></div>
                    <h6>24/7 Support</h6>
                    <small>Dedicated customer care</small>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
