<div class="col-6 col-md-3 fade-up delay-{{ $delay ?? 1 }}">
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
                <div class="d-flex align-items-center justify-content-center h-100" style="background:#f0f0f5;">
                    <i class="bi bi-image text-muted" style="font-size:2.5rem;"></i>
                </div>
            @endif
            <div class="product-overlay">
                <a href="{{ route('products.show', $product->slug) }}" class="btn btn-light btn-sm"><i class="bi bi-eye me-1"></i>View</a>
                <form action="{{ route('cart.add') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button class="btn btn-primary btn-sm btn-add-to-cart"><i class="bi bi-bag-plus me-1"></i>Add</button>
                </form>
                <form action="{{ route('cart.buyNow') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button class="btn btn-sm text-white" style="background: linear-gradient(135deg, #0ba360, #3cba92); border: none;" title="Buy Now"><i class="bi bi-lightning-charge"></i></button>
                </form>
            </div>
        </div>
        <div class="product-body">
            <div class="product-category">{{ $product->category?->name }}</div>
            <h5 class="product-title">
                <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
            </h5>
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
