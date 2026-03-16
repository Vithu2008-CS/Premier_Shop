<div class="col-6 col-md-4 col-xl-3 fade-up delay-{{ $delay ?? 1 }}">
    <div class="product-card glass-card premium-shadow h-100 d-flex flex-column transition-up">
        {{-- Wishlist Toggle --}}
        @auth
            @php
                $inWishlist = \App\Models\UserItem::where('user_id', auth()->id())->where('product_id', $product->id)->where('type', 'wishlist')->exists();
            @endphp
            <form action="{{ route('wishlists.toggle', $product->id) }}" method="POST" class="position-absolute" style="top:12px; right:12px; z-index:11;">
                @csrf
                <button type="submit" class="btn btn-white btn-sm rounded-circle d-flex align-items-center justify-content-center p-0 shadow-sm" style="width:34px;height:34px;" title="{{ $inWishlist ? 'Remove from wishlist' : 'Add to wishlist' }}">
                    <i class="bi bi-heart{{ $inWishlist ? '-fill text-danger' : '' }}" style="font-size: 1rem;"></i>
                </button>
            </form>
        @endauth
        
        {{-- Badges --}}
        <div class="position-absolute" style="top:12px; left:12px; z-index:11; display:flex; flex-direction:column; gap:6px;">
            @if($product->created_at->gt(now()->subDays(7)))
                <span class="badge bg-primary text-white px-2 py-1 rounded-pill fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">NEW</span>
            @endif
            @if($product->has_offer)
                <span class="badge bg-danger text-white px-2 py-1 rounded-pill fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">{{ number_format($product->offer_discount_percent) }}% OFF</span>
            @endif
        </div>

        <div class="product-img-wrap position-relative p-2" style="min-height: 180px;">
            <a href="{{ route('products.show', $product->slug) }}" class="d-block h-100 w-100 rounded-4 overflow-hidden shadow-inner bg-light">
                @if($product->images && count($product->images) > 0)
                    <img src="{{ $product->images[0] }}" alt="{{ $product->name }}" loading="lazy" class="img-fluid w-100 h-100" style="object-fit: cover; transition: transform 0.5s ease;">
                @else
                    <div class="d-flex align-items-center justify-content-center h-100 w-100 bg-secondary bg-opacity-10 text-muted">
                        <i class="bi bi-image" style="font-size:2.5rem;"></i>
                    </div>
                @endif
            </a>
            
            {{-- Quick Buy Overlay (Desktop Only) --}}
            <div class="product-overlay d-none d-md-flex align-items-center justify-content-center gap-2">
                <form action="{{ route('cart.add') }}" method="POST" class="ajax-form">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button class="btn btn-primary rounded-pill px-3 shadow-sm"><i class="bi bi-bag-plus-fill me-1"></i> Add</button>
                </form>
            </div>
        </div>

        <div class="product-body p-3 flex-grow-1">
            <div class="text-muted mb-1 d-flex align-items-center gap-1" style="font-size: 0.75rem; font-weight: 500;">
                <i class="bi bi-tag-fill text-primary opacity-50"></i>
                {{ $product->category?->name ?? 'Uncategorized' }}
            </div>
            <h6 class="product-title mb-2">
                <a href="{{ route('products.show', $product->slug) }}" class="text-dark fw-bold text-decoration-none" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4;">
                    {{ $product->name }}
                </a>
            </h6>
            <div class="d-flex align-items-center gap-2 mt-auto">
                <div class="product-price fw-bold text-primary fs-5">
                    £{{ number_format($product->price, 2) }}
                </div>
                @if($product->has_offer)
                    <small class="text-muted text-decoration-line-through">£{{ number_format($product->original_price, 2) }}</small>
                @endif
            </div>
        </div>

        {{-- Mobile Action Bar (Always Visible on Mobile) --}}
        <div class="product-footer p-2 p-md-3 pt-0 border-0">
            <div class="row g-2 d-md-none">
                <div class="col-12">
                    <form action="{{ route('cart.add') }}" method="POST" class="ajax-form">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button class="btn btn-outline-primary w-100 btn-sm rounded-pill fw-bold py-2">
                            <i class="bi bi-bag-plus"></i> Add to Cart
                        </button>
                    </form>
                </div>
                <div class="col-12">
                    <form action="{{ route('cart.buyNow') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button class="btn btn-primary w-100 btn-sm rounded-pill fw-bold py-2 shadow-sm" style="background: linear-gradient(135deg, #00B894, #00CEC9); border: none;">
                            <i class="bi bi-lightning-charge-fill"></i> Buy Now
                        </button>
                    </form>
                </div>
            </div>
            
            {{-- Desktop Status --}}
            <div class="d-none d-md-flex justify-content-between align-items-center pt-2 border-top border-light">
                <div class="stock-indicator" style="font-size: 0.7rem;">
                    <span class="dot {{ $product->stock > 0 ? 'dot-green' : 'dot-red' }}"></span>
                    <span class="text-muted fw-500">{{ $product->stock > 0 ? 'In Stock' : 'Out of Stock' }}</span>
                </div>
                <a href="{{ route('products.show', $product->slug) }}" class="text-primary x-small fw-bold text-decoration-none">
                    Details <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>
