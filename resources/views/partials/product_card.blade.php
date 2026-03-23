<div class="col-6 col-md-4 col-xl-3 fade-up delay-{{ $delay ?? 1 }}">
    <div class="product-card glass-card premium-shadow h-100 d-flex flex-column transition-up">
        {{-- Wishlist Toggle --}}
        @auth
            @php
                $inWishlist = \App\Models\UserItem::where('user_id', auth()->id())->where('product_id', $product->id)->where('type', 'wishlist')->exists();
            @endphp
            <form action="{{ route('wishlists.toggle', $product->id) }}" method="POST" class="position-absolute top-0 end-0 m-3 z-3">
                @csrf
                <button type="submit" class="btn btn-wishlist glass-pill shadow-sm p-2 rounded-circle d-flex align-items-center justify-content-center" 
                        style="width:36px;height:36px;border:1px solid rgba(255,255,255,0.2);"
                        title="{{ $inWishlist ? 'Remove from wishlist' : 'Add to wishlist' }}">
                    <i class="bi {{ $inWishlist ? 'bi-heart-fill text-danger' : 'bi-heart' }} fs-6"></i>
                </button>
            </form>
        @else
            <a href="{{ route('login') }}" class="position-absolute top-0 end-0 m-3 z-3 btn btn-wishlist glass-pill shadow-sm p-2 rounded-circle d-flex align-items-center justify-content-center" 
               style="width:36px;height:36px;border:1px solid rgba(255,255,255,0.2);" title="Login to save">
                <i class="bi bi-heart fs-6"></i>
            </a>
        @endauth

        {{-- Product Image --}}
        <div class="product-img-wrap">
            @if($product->on_sale)
                <span class="product-badge bg-danger shadow-sm">SALE</span>
            @endif
            <a href="{{ route('products.show', $product->slug) }}" class="d-block h-100">
                @if($product->images && count($product->images) > 0)
                    <img src="{{ $product->images[0] }}" 
                         alt="{{ $product->name }}" 
                         class="product-image-main"
                         loading="lazy"
                         onerror="this.onerror=null; this.src='/images/placeholder-product.png'">
                @else
                    <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                        <i class="bi bi-image text-muted fs-1"></i>
                    </div>
                @endif
            </a>
            
            {{-- Quick View Overlay (Desktop Only) --}}
            <div class="product-overlay d-none d-lg-flex">
                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="ajax-form">
                    @csrf
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="btn btn-add-cart rounded-pill shadow">
                        <i class="bi bi-bag-plus me-2"></i>Add to Cart
                    </button>
                </form>
            </div>
        </div>

        {{-- Product Body --}}
        <div class="product-body">
            <div class="d-flex justify-content-between align-items-start mb-1">
                <span class="product-category">{{ $product->category->name ?? 'Uncategorized' }}</span>
                <div class="product-rating small text-warning">
                    <i class="bi bi-star-fill"></i>
                    <span class="text-muted ms-1">{{ number_format($product->reviews_avg_rating ?? 5.0, 1) }}</span>
                </div>
            </div>
            
            <h3 class="product-title">
                <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
            </h3>

            <div class="d-flex align-items-center mt-2">
                <span class="product-price">£{{ number_format($product->price, 2) }}</span>
                @if($product->original_price > $product->price)
                    <span class="original-price">£{{ number_format($product->original_price, 2) }}</span>
                @endif
            </div>

            {{-- Mobile Action Bar (Always Visible on Mobile) --}}
            <div class="d-lg-none mt-3">
                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="ajax-form w-100">
                    @csrf
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 small fw-bold">
                        <i class="bi bi-bag-plus me-1"></i> Add to Cart
                    </button>
                </form>
            </div>
        </div>

        {{-- Product Footer (Stock Status) --}}
        <div class="product-footer mt-auto">
            <div class="d-flex justify-content-between align-items-center">
                <div class="stock-indicator">
                    @if($product->stock > 0)
                        <span class="dot dot-green"></span>
                        <span class="text-success x-small fw-bold">In Stock</span>
                    @else
                        <span class="dot dot-red"></span>
                        <span class="text-danger x-small fw-bold">Out of Stock</span>
                    @endif
                </div>
                <a href="{{ route('products.show', $product->slug) }}" class="text-primary x-small fw-bold text-decoration-none">
                    Details <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>
