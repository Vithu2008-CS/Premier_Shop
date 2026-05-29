{{--
    partials/product_card.blade.php — Reusable storefront product card
    ====================================================================
    Displays product image (with hover swap), name, price (with discount %),
    rating, add-to-cart, wishlist toggle, stock status, and low-stock alert.
    Variable: $product (Product model), $delay (optional animation delay index)
--}}

{{-- Inject styles once per page, even if partial included many times --}}
@once
@push('styles')
<style>
/* ─── Premier Product Card ──────────────────────────────────── */
.pcard {
    position: relative;
    background: #ffffff;
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid rgba(0, 0, 0, 0.055);
    box-shadow: 0 2px 16px rgba(0, 0, 0, 0.04), 0 1px 4px rgba(0,0,0,0.03);
    transition: box-shadow 0.35s cubic-bezier(0.16,1,0.3,1), transform 0.35s cubic-bezier(0.16,1,0.3,1), border-color 0.35s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}
[data-bs-theme="dark"] .pcard {
    background: #17162a;
    border-color: rgba(255,255,255,0.06);
    box-shadow: 0 4px 24px rgba(0,0,0,0.35);
}
.pcard:hover {
    box-shadow: 0 20px 56px rgba(108,92,231,0.12), 0 6px 20px rgba(0,0,0,0.05);
    transform: translateY(-7px);
    border-color: rgba(108,92,231,0.18);
}
[data-bs-theme="dark"] .pcard:hover {
    box-shadow: 0 20px 56px rgba(108,92,231,0.22), 0 4px 16px rgba(0,0,0,0.4);
    border-color: rgba(162,155,254,0.22);
}

/* ── Image ── */
.pcard-img-wrap {
    display: block;
    position: relative;
    overflow: hidden;
    aspect-ratio: 1 / 1;
    background: #f4f3fc;
    flex-shrink: 0;
}
[data-bs-theme="dark"] .pcard-img-wrap {
    background: #100f1e;
}
.pcard-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.55s cubic-bezier(0.16,1,0.3,1), opacity 0.4s ease;
}
.pcard-img-primary {
    position: absolute;
    inset: 0;
    z-index: 1;
}
.pcard-img-hover {
    position: absolute;
    inset: 0;
    z-index: 2;
    opacity: 0;
    transition: opacity 0.45s ease;
}
.pcard:hover .pcard-img-hover { opacity: 1; }
.pcard:hover .pcard-img-primary { transform: scale(1.06); }
.pcard-img-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.2rem;
    color: #c5c3e0;
}

/* ── Badges ── */
.pcard-badges {
    position: absolute;
    top: 11px;
    left: 11px;
    z-index: 10;
    display: flex;
    flex-direction: column;
    gap: 5px;
    pointer-events: none;
}
.pcard-badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 100px;
    font-size: 0.67rem;
    font-weight: 800;
    letter-spacing: 0.6px;
    font-family: 'Outfit', sans-serif;
    line-height: 1.4;
}
.pcard-badge-sale {
    background: linear-gradient(135deg, #e55353 0%, #c0392b 100%);
    color: #fff;
    box-shadow: 0 2px 8px rgba(197,57,52,0.35);
}
.pcard-badge-new {
    background: linear-gradient(135deg, #00b894 0%, #00cec9 100%);
    color: #fff;
    box-shadow: 0 2px 8px rgba(0,184,148,0.3);
}

/* ── Wishlist ── */
.pcard-wishlist {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 10;
}
.pcard-wish-btn {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    border: none;
    background: rgba(255,255,255,0.88);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    color: #b2bec3;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.25s cubic-bezier(0.16,1,0.3,1);
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    font-size: 0.84rem;
    text-decoration: none;
}
[data-bs-theme="dark"] .pcard-wish-btn {
    background: rgba(22,21,38,0.88);
    color: #636e72;
}
.pcard-wish-btn:hover {
    background: #fff;
    color: #e84393;
    transform: scale(1.12);
    box-shadow: 0 4px 16px rgba(232,67,147,0.25);
}
.pcard-wish-btn.active {
    color: #e84393 !important;
    background: #fff;
}
[data-bs-theme="dark"] .pcard-wish-btn:hover,
[data-bs-theme="dark"] .pcard-wish-btn.active {
    background: #2a2840;
    color: #ff79c6 !important;
}

/* ── Quick Add bar (desktop slide-up) ── */
.pcard-quick-add {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 8;
    padding: 0 10px 11px;
    transform: translateY(110%);
    transition: transform 0.32s cubic-bezier(0.16,1,0.3,1);
}
.pcard:hover .pcard-quick-add { transform: translateY(0); }
.pcard-add-btn {
    width: 100%;
    padding: 9px 14px;
    border: none;
    border-radius: 12px;
    background: linear-gradient(135deg, #6C5CE7 0%, #8E2DE2 100%);
    color: #fff;
    font-weight: 700;
    font-size: 0.78rem;
    font-family: 'Outfit', sans-serif;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    transition: filter 0.2s ease, transform 0.15s ease;
    box-shadow: 0 4px 18px rgba(108,92,231,0.3);
}
.pcard-add-btn:hover {
    filter: brightness(1.1);
    transform: scale(1.015);
}

/* ── Body ── */
.pcard-body {
    padding: 11px 13px 7px;
    flex: 1;
    display: flex;
    flex-direction: column;
}
.pcard-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 4px;
}
.pcard-cat {
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 0.9px;
    text-transform: uppercase;
    color: #6C5CE7;
    font-family: 'Outfit', sans-serif;
}
[data-bs-theme="dark"] .pcard-cat { color: #A29BFE; }
.pcard-rating {
    display: flex;
    align-items: center;
    gap: 3px;
    font-size: 0.7rem;
    font-weight: 700;
    color: #f39c12;
}
.pcard-name {
    font-size: 0.85rem;
    font-weight: 600;
    line-height: 1.34;
    margin-bottom: 5px;
    font-family: 'Outfit', sans-serif;
    color: #2D3436;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
[data-bs-theme="dark"] .pcard-name { color: #f0f0f8; }
.pcard-name a {
    color: inherit;
    text-decoration: none;
    transition: color 0.2s;
}
.pcard-name a:hover { color: #6C5CE7; }
[data-bs-theme="dark"] .pcard-name a:hover { color: #A29BFE; }

.pcard-price-row {
    display: flex;
    align-items: baseline;
    gap: 6px;
    margin-top: auto;
}
.pcard-price {
    font-family: 'Outfit', sans-serif;
    font-weight: 800;
    font-size: 1rem;
    color: #2D3436;
    line-height: 1;
}
[data-bs-theme="dark"] .pcard-price { color: #ffffff; }
.pcard-old-price {
    font-size: 0.75rem;
    color: #b2bec3;
    text-decoration: line-through;
    font-weight: 500;
}

/* ── Mobile add button ── */
.pcard-mobile-add {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    width: 100%;
    margin-top: 8px;
    padding: 7px 10px;
    border: 1.5px solid rgba(108,92,231,0.28);
    border-radius: 10px;
    background: transparent;
    color: #6C5CE7;
    font-size: 0.75rem;
    font-weight: 700;
    font-family: 'Outfit', sans-serif;
    cursor: pointer;
    transition: all 0.2s ease;
}
.pcard-mobile-add:active {
    background: #6C5CE7;
    color: #fff;
    border-color: #6C5CE7;
}
[data-bs-theme="dark"] .pcard-mobile-add {
    border-color: rgba(162,155,254,0.28);
    color: #A29BFE;
}

/* ── Footer ── */
.pcard-footer {
    padding: 6px 13px 9px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-top: 1px solid rgba(0,0,0,0.042);
}
[data-bs-theme="dark"] .pcard-footer {
    border-top-color: rgba(255,255,255,0.04);
}
.pcard-stock {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 0.68rem;
    font-weight: 600;
    font-family: 'Outfit', sans-serif;
}
.pcard-stock.in-stock  { color: #00b894; }
.pcard-stock.out-stock { color: #b2bec3; }
.pcard-stock-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: currentColor;
    flex-shrink: 0;
}
.pcard-stock.in-stock .pcard-stock-dot {
    animation: pcard-pulse 2.2s ease-in-out infinite;
}
@keyframes pcard-pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(0,184,148,0.5); }
    50%       { box-shadow: 0 0 0 5px rgba(0,184,148,0); }
}
.pcard-low-stock {
    font-size: 0.65rem;
    font-weight: 700;
    color: #e17055;
    background: rgba(225,112,85,0.1);
    padding: 2px 8px;
    border-radius: 100px;
    font-family: 'Outfit', sans-serif;
}

/* ── Mobile tweaks ── */
@media (max-width: 575px) {
    .pcard { border-radius: 14px; }
    .pcard-body { padding: 8px 10px 5px; }
    .pcard-name { font-size: 0.78rem; }
    .pcard-price { font-size: 0.88rem; }
    .pcard-footer { padding: 5px 10px 8px; }
    .pcard-wish-btn { width: 28px; height: 28px; font-size: 0.78rem; }
}
</style>
@endpush
@endonce

<div class="col-6 col-md-4 col-xl-3 fade-up delay-{{ $delay ?? 1 }}">
    <div class="pcard">
        {{-- ── Badges ── --}}
        <div class="pcard-badges">
            @if($product->retail_offer)
                <span class="pcard-badge pcard-badge-sale">OFFER</span>
            @endif
            @if($product->on_sale)
                @php
                    $discountPct = ($product->original_price > 0 && $product->original_price > $product->price)
                        ? round((($product->original_price - $product->price) / $product->original_price) * 100)
                        : 0;
                @endphp
                <span class="pcard-badge pcard-badge-sale">
                    {{ $discountPct > 0 ? '-'.$discountPct.'%' : 'SALE' }}
                </span>
            @elseif($product->created_at->diffInDays(now()) <= 14)
                <span class="pcard-badge pcard-badge-new">NEW</span>
            @endif
        </div>

        {{-- ── Wishlist ── --}}
        @auth
            @php
                $inWishlist = \App\Models\UserItem::where('user_id', auth()->id())
                    ->where('product_id', $product->id)
                    ->where('type', 'wishlist')
                    ->exists();
            @endphp
            <form action="{{ route('wishlists.toggle', $product->id) }}" method="POST" class="pcard-wishlist">
                @csrf
                <button type="submit" class="pcard-wish-btn {{ $inWishlist ? 'active' : '' }}"
                        title="{{ $inWishlist ? 'Remove from wishlist' : 'Add to wishlist' }}">
                    <i class="bi {{ $inWishlist ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                </button>
            </form>
        @else
            <a href="{{ route('login') }}" class="pcard-wishlist pcard-wish-btn" title="Login to save">
                <i class="bi bi-heart"></i>
            </a>
        @endauth

        {{-- ── Image ── --}}
        <a href="{{ route('products.show', $product->slug) }}" class="pcard-img-wrap">
            @if($product->images && count($product->images) > 0)
                <img src="{{ $product->images[0] }}"
                     alt="{{ $product->name }}"
                     class="pcard-img pcard-img-primary"
                     loading="lazy"
                     decoding="async"
                     onerror="this.closest('.pcard-img-wrap').innerHTML='<div class=\'pcard-img-placeholder\'><i class=\'bi bi-image\'></i></div>'">
                @if(count($product->images) > 1)
                    <img src="{{ $product->images[1] }}"
                         alt="{{ $product->name }}"
                         class="pcard-img pcard-img-hover"
                         loading="lazy"
                         decoding="async">
                @endif
            @else
                <div class="pcard-img-placeholder"><i class="bi bi-image"></i></div>
            @endif
        </a>

        {{-- ── Quick Add (desktop hover slide-up) ── --}}
        <div class="pcard-quick-add d-none d-lg-block">
            <form action="{{ route('cart.add') }}" method="POST" class="ajax-form">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="pcard-add-btn">
                    <i class="bi bi-bag-plus"></i> Add to Cart
                </button>
            </form>
        </div>

        {{-- ── Body ── --}}
        <div class="pcard-body">
            <div class="pcard-meta">
                <span class="pcard-cat">{{ $product->category->name ?? 'Premium' }}</span>
                <div class="pcard-rating">
                    <i class="bi bi-star-fill"></i>
                    <span>{{ number_format($product->reviews_avg_rating ?? 5.0, 1) }}</span>
                </div>
            </div>

            <h3 class="pcard-name">
                <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
            </h3>

            <div class="pcard-price-row">
                <span class="pcard-price">£{{ number_format($product->price, 2) }}</span>
                @if($product->original_price > $product->price)
                    <span class="pcard-old-price">£{{ number_format($product->original_price, 2) }}</span>
                @endif
            </div>

            {{-- Mobile add button --}}
            <div class="d-lg-none">
                <form action="{{ route('cart.add') }}" method="POST" class="ajax-form">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="pcard-mobile-add">
                        <i class="bi bi-bag-plus"></i><span>Add to Cart</span>
                    </button>
                </form>
            </div>
        </div>

        {{-- ── Footer ── --}}
        <div class="pcard-footer">
            <div class="pcard-stock {{ $product->stock > 0 ? 'in-stock' : 'out-stock' }}">
                <span class="pcard-stock-dot"></span>
                {{ $product->stock > 0 ? 'In Stock' : 'Sold Out' }}
            </div>
            @if($product->stock > 0 && $product->stock <= 5)
                <span class="pcard-low-stock">Only {{ $product->stock }} left</span>
            @endif
        </div>

    </div>
</div>
