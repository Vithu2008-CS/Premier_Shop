<div class="col-6 col-md-4 col-xl-3 fade-up delay-{{ $delay ?? 1 }}">
    <div class="product-card glass-premium-card h-100 transition-float">
        {{-- Wishlist Toggle --}}
        @auth
            @php
                $inWishlist = \App\Models\UserItem::where('user_id', auth()->id())->where('product_id', $product->id)->where('type', 'wishlist')->exists();
            @endphp
            <form action="{{ route('wishlists.toggle', $product->id) }}" method="POST" class="wishlist-overlay">
                @csrf
                <button type="submit" class="wishlist-btn {{ $inWishlist ? 'active' : '' }}" title="{{ $inWishlist ? 'Remove from wishlist' : 'Add to wishlist' }}">
                    <i class="bi {{ $inWishlist ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                </button>
            </form>
        @else
            <a href="{{ route('login') }}" class="wishlist-overlay wishlist-btn" title="Login to save">
                <i class="bi bi-heart"></i>
            </a>
        @endauth

        {{-- Product Image --}}
        <div class="product-img-wrap">
            <div class="img-glow"></div>
            @if($product->on_sale)
                <span class="premium-badge badge-sale">SALE</span>
            @endif
            <a href="{{ route('products.show', $product->slug) }}" class="d-block h-100">
                @if($product->images && count($product->images) > 0)
                    <img src="{{ $product->images[0] }}" 
                         alt="{{ $product->name }}" 
                         class="product-image-main"
                         loading="lazy"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="img-placeholder" style="display: none;">
                        <i class="bi bi-image"></i>
                    </div>
                @else
                    <div class="img-placeholder">
                        <i class="bi bi-image"></i>
                    </div>
                @endif
            </a>
            
            {{-- Quick Action Overlay --}}
            <div class="product-overlay-glass d-none d-lg-flex">
                <form action="{{ route('cart.add') }}" method="POST" class="ajax-form w-100 p-3">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="btn-premium-add">
                        <i class="bi bi-bag-plus"></i> Add to Cart
                    </button>
                </form>
            </div>
        </div>

        {{-- Product Body --}}
        <div class="product-info-glass">
            <div class="cat-row">
                <span class="cat-pill-mini">{{ $product->category->name ?? 'Premium' }}</span>
                <div class="rating-mini">
                    <i class="bi bi-star-fill"></i>
                    <span>{{ number_format($product->reviews_avg_rating ?? 5.0, 1) }}</span>
                </div>
            </div>
            
            <h3 class="product-name">
                <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
            </h3>

            <div class="price-row">
                <span class="current-price">£{{ number_format($product->price, 2) }}</span>
                @if($product->original_price > $product->price)
                    <span class="old-price">£{{ number_format($product->original_price, 2) }}</span>
                @endif
            </div>

            {{-- Mobile Action (Simplified) --}}
            <div class="d-lg-none mt-3">
                <form action="{{ route('cart.add') }}" method="POST" class="ajax-form">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="btn-mobile-add">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </form>
            </div>
        </div>

        {{-- Product Footer --}}
        <div class="product-meta">
            <div class="stock-status {{ $product->stock > 0 ? 'in-stock' : 'out-of-stock' }}">
                <span class="status-dot"></span>
                <span>{{ $product->stock > 0 ? 'Available' : 'Sold Out' }}</span>
            </div>
        </div>
    </div>
</div>
