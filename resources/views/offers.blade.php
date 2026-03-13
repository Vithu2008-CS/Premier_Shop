@extends('layouts.app')
@section('title', 'Offers — Premier Shop')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5 reveal-3d">
            <span class="badge bg-danger px-3 py-2 mb-3" style="font-size:0.85rem;">
                <i class="bi bi-lightning-fill me-1"></i> Limited Time Deals
            </span>
            <h1 class="section-title">Special <span class="gradient-text">Offers</span></h1>
            <p class="section-subtitle">Buy in bulk and save! The more you buy, the more you save.</p>
        </div>

        @if($offerProducts->isEmpty())
            <div class="text-center py-5 reveal-3d">
                <div class="d-inline-flex align-items-center justify-content-center mb-4" style="width:100px;height:100px;background:rgba(108,92,231,0.08);border-radius:50%;">
                    <i class="bi bi-tag text-primary" style="font-size:3rem;"></i>
                </div>
                <h4 class="fw-bold mb-2">No offers currently available</h4>
                <p class="text-muted mb-4">Check back soon for amazing deals!</p>
                <a href="{{ route('products.index') }}" class="btn btn-add-cart">Browse All Products</a>
            </div>
        @else
            <div class="row g-4 stagger-children">
                @foreach($offerProducts as $index => $product)
                    <div class="col-6 col-md-4 col-lg-3 fade-up delay-{{ ($index % 8) + 1 }}">
                        <div class="product-card position-relative">
                            @auth
                                @php
                                    $inWishlist = \App\Models\Wishlist::where('user_id', auth()->id())->where('product_id', $product->id)->exists();
                                @endphp
                                <form action="{{ route('wishlists.toggle', $product->id) }}" method="POST" class="position-absolute" style="top:10px; right:10px; z-index:10;">
                                    @csrf
                                    <button type="submit" class="btn btn-light btn-sm rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width:35px;height:35px;" title="{{ $inWishlist ? 'Remove from wishlist' : 'Add to wishlist' }}">
                                        <i class="bi bi-heart{{ $inWishlist ? '-fill text-danger' : '' }} fs-6"></i>
                                    </button>
                                </form>
                            @endauth
                            {{-- Offer Badge --}}
                            <span class="product-badge bg-danger">
                                <i class="bi bi-lightning-fill me-1"></i>{{ number_format($product->offer_discount_percent) }}% OFF
                            </span>

                            <div class="product-img-wrap">
                                @if($product->images && count($product->images) > 0)
                                    <img src="{{ $product->images[0] }}" alt="{{ $product->name }}" loading="lazy">
                                @else
                                    <div class="d-flex align-items-center justify-content-center h-100" style="background:linear-gradient(135deg,#f0f0f5,#e8e8f0);">
                                        <i class="bi bi-image text-muted" style="font-size:3rem;"></i>
                                    </div>
                                @endif
                                <div class="product-overlay">
                                    <a href="{{ route('products.show', $product->slug) }}" class="btn btn-light btn-sm"><i class="bi bi-eye me-1"></i>View</a>
                                    <form action="{{ route('cart.add') }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="quantity" value="{{ $product->offer_min_qty }}">
                                        <button class="btn btn-primary btn-sm btn-add-to-cart"><i class="bi bi-bag-plus me-1"></i>Add {{ $product->offer_min_qty }}</button>
                                    </form>
                                    <form action="{{ route('cart.buyNow') }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="quantity" value="{{ $product->offer_min_qty }}">
                                        <button class="btn btn-sm text-white" style="background: linear-gradient(135deg, #0ba360, #3cba92); border: none;" title="Buy Now"><i
                                                class="bi bi-lightning-charge"></i></button>
                                    </form>
                                </div>
                            </div>

                            <div class="product-body">
                                <div class="product-category">{{ $product->category?->name }}</div>
                                <h5 class="product-title">
                                    <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
                                </h5>
                                <div class="product-price">
                                    £{{ number_format($product->offer_price, 2) }}
                                    <span class="original-price">£{{ number_format($product->price, 2) }}</span>
                                </div>
                                <div class="mt-2">
                                    <span class="badge" style="background:rgba(0,206,201,0.1);color:#00CEC9;font-size:0.75rem;">
                                        Buy {{ $product->offer_min_qty }}+ to save {{ number_format($product->offer_discount_percent) }}%
                                    </span>
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
            <div class="mt-4">{{ $offerProducts->links() }}</div>
        @endif
    </div>
</section>
@endsection
