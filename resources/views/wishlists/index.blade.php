@extends('layouts.app')
@section('title', 'My Wishlist — Premier Shop')

@section('content')
<section class="py-5">
    <div class="container">
        <h2 class="section-title mb-4 reveal-3d">My <span class="gradient-text">Wishlist</span></h2>

        @if($wishlists->isEmpty())
            <div class="text-center py-5 reveal-3d">
                <i class="bi bi-heart text-muted mb-3 d-block" style="font-size: 3rem;"></i>
                <h4>Your wishlist is empty</h4>
                <p class="text-muted">Start saving your favorite items for later.</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary mt-2 rounded-pill px-4">Browse Products</a>
            </div>
        @else
            <div class="row g-4 stagger-children">
                @foreach($wishlists as $index => $wishlist)
                    @php $product = $wishlist->product; @endphp
                    <div class="col-6 col-md-4 col-lg-3 fade-up delay-{{ ($index % 8) + 1 }}">
                        <div class="product-card position-relative">
                            {{-- Wishlist Remove Button --}}
                            <form action="{{ route('wishlists.toggle', $product->id) }}" method="POST" class="position-absolute" style="top:10px; right:10px; z-index:10;">
                                @csrf
                                <button type="submit" class="btn btn-light btn-sm rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width:35px;height:35px;" title="Remove from wishlist">
                                    <i class="bi bi-heart-fill text-danger fs-6"></i>
                                </button>
                            </form>

                            <div class="product-img-wrap pt-3">
                                @if($product->images && count($product->images) > 0)
                                    <img src="{{ $product->images[0] }}" alt="{{ $product->name }}" loading="lazy">
                                @else
                                    <div class="d-flex align-items-center justify-content-center h-100" style="background:#f0f0f5;">
                                        <i class="bi bi-image text-muted" style="font-size:3rem;"></i>
                                    </div>
                                @endif
                                <div class="product-overlay">
                                    <a href="{{ route('products.show', $product->slug) }}" class="btn btn-light btn-sm"><i class="bi bi-eye"></i></a>
                                    
                                    @if($product->stock > 0)
                                    <form action="{{ route('cart.add') }}" method="POST" class="d-inline ajax-form">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button class="btn btn-primary btn-sm btn-add-to-cart" title="Add to Cart"><i class="bi bi-bag-plus"></i></button>
                                    </form>
                                    @endif
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
                @endforeach
            </div>
            <div class="mt-4">{{ $wishlists->links() }}</div>
        @endif
    </div>
</section>
@endsection
