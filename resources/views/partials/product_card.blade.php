<div class="col-6 col-md-3 fade-up delay-{{ $delay ?? 1 }}">
    <div class="product-card position-relative h-100 d-flex flex-column">
        @auth
            @php
                $inWishlist = \App\Models\UserItem::where('user_id', auth()->id())->where('product_id', $product->id)->where('type', 'wishlist')->exists();
            @endphp
            <form action="{{ route('wishlists.toggle', $product->id) }}" method="POST" class="position-absolute" style="top:8px; right:8px; z-index:11;">
                @csrf
                <button type="submit" class="btn btn-light btn-sm rounded-circle shadow-sm d-flex align-items-center justify-content-center p-0" style="width:32px;height:32px;" title="{{ $inWishlist ? 'Remove from wishlist' : 'Add to wishlist' }}">
                    <i class="bi bi-heart{{ $inWishlist ? '-fill text-danger' : '' }}" style="font-size: 0.9rem;"></i>
                </button>
            </form>
        @endauth
        
        @if($product->created_at->gt(now()->subDays(7)))
            <span class="product-badge" style="background:var(--ps-gradient); padding: 4px 8px; font-size: 0.65rem;">NEW</span>
        @endif
        @if($product->has_offer)
            <span class="product-badge bg-danger" style="padding: 4px 8px; font-size: 0.65rem;">{{ number_format($product->offer_discount_percent) }}% OFF</span>
        @endif

        <div class="product-img-wrap position-relative">
            @if($product->images && count($product->images) > 0)
                <img src="{{ $product->images[0] }}" alt="{{ $product->name }}" loading="lazy" class="img-fluid">
            @else
                <div class="d-flex align-items-center justify-content-center h-100 w-100" style="background:#f0f0f5; min-height: 180px;">
                    <i class="bi bi-image text-muted" style="font-size:2rem;"></i>
                </div>
            @endif
            
            {{-- Quick Actions Overlay --}}
            <div class="product-overlay d-none d-md-flex">
                <a href="{{ route('products.show', $product->slug) }}" class="btn btn-light btn-sm rounded-pill px-3"><i class="bi bi-eye"></i></a>
                <form action="{{ route('cart.add') }}" method="POST" class="ajax-form d-inline">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button class="btn btn-primary btn-sm rounded-pill px-3"><i class="bi bi-bag-plus"></i></button>
                </form>
            </div>
        </div>

        <div class="product-body p-2 p-md-3 flex-grow-1">
            <div class="product-category mb-1 d-none d-md-block">{{ $product->category?->name }}</div>
            <h6 class="product-title mb-1" style="font-size: 0.85rem; line-height: 1.3;">
                <a href="{{ route('products.show', $product->slug) }}" class="text-dark fw-600 text-decoration-none">{{ Str::limit($product->name, 40) }}</a>
            </h6>
            <div class="product-price fw-bold text-primary" style="font-size: 1rem;">
                £{{ number_format($product->price, 2) }}
            </div>
        </div>

        <div class="product-footer p-2 p-md-3 border-top-0 pt-0">
            <div class="d-flex justify-content-between align-items-center">
                <div class="stock-indicator" style="font-size: 0.7rem;">
                    <span class="dot {{ $product->stock > 0 ? 'dot-green' : 'dot-red' }}"></span>
                    <span class="d-none d-sm-inline">{{ $product->stock > 0 ? 'In Stock' : 'Out of Stock' }}</span>
                </div>
                {{-- Buy Now Mini Button for Mobile --}}
                <form action="{{ route('cart.buyNow') }}" method="POST" class="d-md-none">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button class="btn btn-sm p-1 px-2 text-white rounded-pill" style="background: linear-gradient(135deg, #0ba360, #3cba92); border: none; font-size: 0.75rem;">
                        <i class="bi bi-lightning-charge"></i> Buy
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
