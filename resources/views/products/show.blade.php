@extends('layouts.app')
@section('title', $product->name . ' — Premier Shop')

@section('content')
<section class="py-5">
    <div class="container">
        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-4 fade-up">
            <ol class="breadcrumb" style="font-size:0.9rem;">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('products.index') }}" class="text-decoration-none">Products</a></li>
                @if($product->category)
                    <li class="breadcrumb-item"><a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="text-decoration-none">{{ $product->category->name }}</a></li>
                @endif
                <li class="breadcrumb-item active">{{ $product->name }}</li>
            </ol>
        </nav>

        <div class="row g-5">
            {{-- Product Images --}}
            <div class="col-lg-6 fade-up">
                <div class="card border-0" style="border-radius:20px;overflow:hidden;">
                    @if($product->images && count($product->images) > 0)
                        <div id="productCarousel" class="carousel slide" data-bs-ride="false">
                            <div class="carousel-inner">
                                @foreach($product->images as $i => $img)
                                    <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                                        <img src="{{ $img }}" class="d-block w-100" alt="{{ $product->name }}" style="aspect-ratio:1;object-fit:cover;">
                                    </div>
                                @endforeach
                            </div>
                            @if(count($product->images) > 1)
                                <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon"></span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                                    <span class="carousel-control-next-icon"></span>
                                </button>
                            @endif
                        </div>
                        {{-- Thumbnail Strip --}}
                        @if(count($product->images) > 1)
                            <div class="d-flex gap-2 mt-3">
                                @foreach($product->images as $i => $img)
                                    <img src="{{ $img }}" class="rounded-3" style="width:70px;height:70px;object-fit:cover;cursor:pointer;opacity:{{ $i === 0 ? '1' : '0.5' }};border:2px solid {{ $i === 0 ? '#6C5CE7' : 'transparent' }};" onclick="document.querySelector('#productCarousel').querySelectorAll('.carousel-item')[{{ $i }}].classList.add('active'); bootstrap.Carousel.getOrCreateInstance(document.querySelector('#productCarousel')).to({{ $i }});">
                                @endforeach
                            </div>
                        @endif
                    @else
                        <div class="d-flex align-items-center justify-content-center" style="aspect-ratio:1;background:linear-gradient(135deg,#f0f0f5,#e8e8f0);">
                            <i class="bi bi-image text-muted" style="font-size:5rem;"></i>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Product Details --}}
            <div class="col-lg-6 fade-up delay-2">
                {{-- Category + Badges --}}
                <div class="d-flex gap-2 mb-2">
                    @if($product->category)
                        <span class="badge" style="background:rgba(108,92,231,0.1);color:#6C5CE7;">{{ $product->category->name }}</span>
                    @endif
                    @if($product->is_age_restricted)
                        <span class="badge bg-danger">🔞 Age 16+ Only</span>
                    @endif
                    @if($product->stock > 0 && $product->stock <= 10)
                        <span class="badge bg-warning text-dark">Only {{ $product->stock }} left!</span>
                    @endif
                </div>

                <h1 class="fw-bold mb-3" style="font-size:2rem;letter-spacing:-0.5px;">{{ $product->name }}</h1>

                {{-- Price --}}
                <div class="d-flex align-items-baseline gap-3 mb-4">
                    <span class="gradient-text" style="font-size:2.5rem;font-weight:800;font-family:'Outfit',sans-serif;">£{{ number_format($product->price, 2) }}</span>
                    @if($product->wholesale_price)
                        <span class="text-muted" style="text-decoration:line-through;">£{{ number_format($product->wholesale_price, 2) }}</span>
                    @endif
                </div>

                {{-- Stock Status --}}
                <div class="mb-4">
                    @if($product->stock > 10)
                        <div class="d-flex align-items-center gap-2">
                            <span class="dot dot-green" style="width:10px;height:10px;border-radius:50;display:inline-block;background:#00B894;"></span>
                            <span class="text-success fw-bold">In Stock</span>
                        </div>
                    @elseif($product->stock > 0)
                        <div class="d-flex align-items-center gap-2">
                            <span style="width:10px;height:10px;border-radius:50%;display:inline-block;background:#FDCB6E;"></span>
                            <span class="text-warning fw-bold">Low Stock — {{ $product->stock }} remaining</span>
                        </div>
                    @else
                        <div class="d-flex align-items-center gap-2">
                            <span style="width:10px;height:10px;border-radius:50%;display:inline-block;background:#E17055;"></span>
                            <span class="text-danger fw-bold">Out of Stock</span>
                        </div>
                    @endif
                </div>

                {{-- Description --}}
                @if($product->description)
                    <div class="mb-4">
                        <h6 class="fw-bold mb-2">Description</h6>
                        <p class="text-muted" style="line-height:1.8;">{{ $product->description }}</p>
                    </div>
                @endif

                {{-- Add to Cart --}}
                @if($product->stock > 0)
                    <form action="{{ route('cart.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="d-flex gap-3 mb-4">
                            <div class="qty-stepper d-flex align-items-center border rounded-3">
                                <button type="button" class="btn btn-light qty-minus px-3 border-0">−</button>
                                <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}" class="form-control text-center border-0" style="width:60px;">
                                <button type="button" class="btn btn-light qty-plus px-3 border-0">+</button>
                            </div>
                            <button type="submit" class="btn btn-add-cart flex-grow-1">
                                <i class="bi bi-bag-plus me-2"></i> Add to Cart
                            </button>
                        </div>
                    </form>
                @else
                    <button class="btn btn-secondary btn-lg w-100 mb-4" disabled style="border-radius:50px;">
                        <i class="bi bi-x-circle me-2"></i> Out of Stock
                    </button>
                @endif

                {{-- Features --}}
                <div class="row g-3">
                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2 p-3 rounded-3" style="background:#f8f9fe;">
                            <i class="bi bi-truck text-primary"></i>
                            <small class="fw-600">Free delivery over £50</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2 p-3 rounded-3" style="background:#f8f9fe;">
                            <i class="bi bi-shield-check text-primary"></i>
                            <small class="fw-600">Secure checkout</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2 p-3 rounded-3" style="background:#f8f9fe;">
                            <i class="bi bi-arrow-counterclockwise text-primary"></i>
                            <small class="fw-600">30-day returns</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2 p-3 rounded-3" style="background:#f8f9fe;">
                            <i class="bi bi-award text-primary"></i>
                            <small class="fw-600">Quality guaranteed</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Related Products --}}
        @if(isset($relatedProducts) && $relatedProducts->count() > 0)
            <div class="mt-5 pt-5 border-top">
                <h3 class="section-title fade-up">You May Also <span class="gradient-text">Like</span></h3>
                <div class="row g-4 mt-2">
                    @foreach($relatedProducts as $i => $rel)
                        <div class="col-6 col-md-3 fade-up delay-{{ $i + 1 }}">
                            <div class="product-card">
                                <div class="product-img-wrap">
                                    @if($rel->images && count($rel->images) > 0)
                                        <img src="{{ $rel->images[0] }}" alt="{{ $rel->name }}" loading="lazy">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center h-100" style="background:#f0f0f5;">
                                            <i class="bi bi-image text-muted" style="font-size:2rem;"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="product-body">
                                    <h5 class="product-title"><a href="{{ route('products.show', $rel->slug) }}">{{ $rel->name }}</a></h5>
                                    <div class="product-price">£{{ number_format($rel->price, 2) }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
