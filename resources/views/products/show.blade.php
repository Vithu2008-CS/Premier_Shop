@extends('layouts.app')
@section('title', $product->name . ' — Premier Shop')

@section('content')
<section class="section-padding">
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
            <div class="col-lg-6 reveal-slide-left">
                <div class="product-gallery-wrapper">
                    @if($product->images && count($product->images) > 0)
                        <div id="productCarousel" class="carousel slide carousel-fade shadow-sm rounded-4 overflow-hidden bg-white" data-bs-ride="false">
                            <div class="carousel-inner">
                                @foreach($product->images as $i => $img)
                                    <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                                        <div class="product-img-main-wrap">
                                            <img src="{{ $img }}" class="img-fluid w-100 h-100" alt="{{ $product->name }}" style="object-fit:contain;">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if(count($product->images) > 1)
                                <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon bg-dark rounded-circle" style="width:30px;height:30px;"></span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                                    <span class="carousel-control-next-icon bg-dark rounded-circle" style="width:30px;height:30px;"></span>
                                </button>
                            @endif
                        </div>
                        {{-- Thumbnail Strip --}}
                        @if(count($product->images) > 1)
                            <div class="d-flex gap-2 mt-3 overflow-auto pb-2 custom-scrollbar">
                                @foreach($product->images as $i => $img)
                                    <div class="thumb-wrap {{ $i === 0 ? 'active' : '' }}" onclick="bootstrap.Carousel.getOrCreateInstance('#productCarousel').to({{ $i }})">
                                        <img src="{{ $img }}" class="rounded-3" alt="Thumb {{ $i + 1 }}">
                                    </div>
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
            <div class="col-lg-6 reveal-slide-right">
                {{-- Category + Badges --}}
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="d-flex gap-2">
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
                    @auth
                        @php
                            $inWishlist = \App\Models\UserItem::where('user_id', auth()->id())->where('product_id', $product->id)->where('type', 'wishlist')->exists();
                        @endphp
                        <form action="{{ route('wishlists.toggle', $product->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width:40px;height:40px;" title="{{ $inWishlist ? 'Remove from wishlist' : 'Add to wishlist' }}">
                                <i class="bi bi-heart{{ $inWishlist ? '-fill text-danger' : '' }} fs-5"></i>
                            </button>
                        </form>
                    @endauth
                </div>

                <h1 class="fw-bold mb-3" style="font-size:2rem;letter-spacing:-0.5px;">{{ $product->name }}</h1>

                {{-- Rating Summary --}}
                @if($product->reviews_count > 0)
                <div class="d-flex align-items-center mb-3 text-warning">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= round($product->average_rating))
                            <i class="bi bi-star-fill"></i>
                        @else
                            <i class="bi bi-star"></i>
                        @endif
                    @endfor
                    <a href="#reviewsSection" class="ms-2 text-muted text-decoration-none small">
                        {{ number_format($product->average_rating, 1) }} ({{ $product->reviews_count }} reviews)
                    </a>
                </div>
                @else
                <div class="d-flex align-items-center mb-3">
                    <span class="badge bg-light text-muted border"><i class="bi bi-star me-1"></i>No reviews yet</span>
                    <a href="#reviewsSection" class="ms-2 text-primary small text-decoration-none fw-bold">Be the first to review!</a>
                </div>
                @endif

                {{-- Price --}}
                <div class="d-flex align-items-baseline gap-3 mb-4 reveal-fade delay-1">
                    <span class="gradient-text" style="font-size: clamp(2rem, 5vw, 2.8rem); font-weight: 800; font-family: 'Outfit', sans-serif; letter-spacing: -1px;">£{{ number_format($product->price, 2) }}</span>
                    @if($product->wholesale_price && $product->wholesale_price > $product->price)
                        <span class="text-muted" style="text-decoration: line-through; font-size: 1.1rem;">£{{ number_format($product->wholesale_price, 2) }}</span>
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
                    <div class="desktop-action-bar reveal-fade delay-2">
                        <form action="{{ route('cart.add') }}" method="POST" class="ajax-form">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <div class="qty-stepper d-flex align-items-center border rounded-pill p-1 bg-light">
                                        <button type="button" class="btn btn-white qty-minus shadow-sm rounded-circle p-0" style="width:36px;height:36px;">−</button>
                                        <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}" class="form-control text-center border-0 bg-transparent fw-bold" style="width:50px;" readonly>
                                        <button type="button" class="btn btn-white qty-plus shadow-sm rounded-circle p-0" style="width:36px;height:36px;">+</button>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-add-cart w-100 h-100 py-3 rounded-pill shadow-sm">
                                        <i class="bi bi-bag-plus-fill me-2"></i> Add to Cart
                                    </button>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" formaction="{{ route('cart.buyNow') }}" class="btn btn-primary w-100 h-100 py-3 rounded-pill shadow-sm" style="background: var(--ps-gradient); border: none;">
                                        <i class="bi bi-lightning-charge-fill me-2"></i> Buy Now
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                @else
                    <button class="btn btn-secondary btn-lg w-100 mb-4" disabled style="border-radius:50px;">
                        <i class="bi bi-x-circle me-2"></i> Out of Stock
                    </button>
                @endif

                {{-- Features --}}
                <div class="row g-3">
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

        {{-- Customer Reviews (Advanced) --}}
        <div id="reviewsSection" class="mt-5 pt-5 border-top reveal-3d">
            <h3 class="fw-bold mb-4">Customer Reviews</h3>
            <div class="row g-5">
                {{-- Left Col: Summary & Write Review --}}
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top:100px;">
                        @if($product->reviews_count > 0)
                            <div class="text-center mb-4">
                                <h1 class="display-3 fw-bold gradient-text mb-0">{{ number_format($product->average_rating, 1) }}</h1>
                                <div class="text-warning fs-4 mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi {{ $i <= round($product->average_rating) ? 'bi-star-fill' : 'bi-star' }}"></i>
                                    @endfor
                                </div>
                                <span class="text-muted small">Based on {{ $product->reviews_count }} {{ Str::plural('review', $product->reviews_count) }}</span>
                            </div>

                            @php
                                $totalReviews = $product->reviews()->where('is_approved', true)->count();
                                $ratings = $product->reviews()->where('is_approved', true)->selectRaw('rating, count(*) as count')->groupBy('rating')->get()->keyBy('rating');
                            @endphp

                            <div class="rating-bars mb-4">
                                @for($i = 5; $i >= 1; $i--)
                                    @php
                                        $count = isset($ratings[$i]) ? $ratings[$i]->count : 0;
                                        $percentage = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
                                    @endphp
                                    <div class="d-flex align-items-center mb-2" style="font-size:0.85rem;">
                                        <span class="text-muted me-2" style="width:12px;">{{ $i }}</span>
                                        <i class="bi bi-star-fill text-warning me-2"></i>
                                        <div class="progress flex-grow-1 mx-2" style="height:8px; border-radius:10px; background:#f0f2f5;">
                                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $percentage }}%; border-radius:10px;"></div>
                                        </div>
                                        <span class="text-muted ms-2" style="width:30px; text-align:right;">{{ $count }}</span>
                                    </div>
                                @endfor
                            </div>
                        @else
                            <div class="text-center mb-4">
                                <div class="mb-3">
                                    <i class="bi bi-star text-muted" style="font-size:3rem;"></i>
                                </div>
                                <h5>No reviews yet</h5>
                                <p class="text-muted small">Buy this product and be the first to share your thoughts!</p>
                            </div>
                        @endif

                        <hr class="opacity-10 mb-4">

                        @auth
                            @php
                                $hasReviewed = $product->reviews()->where('user_id', auth()->id())->exists();
                                $hasPurchased = \App\Models\Order::where('user_id', auth()->id())
                                    ->whereIn('status', ['delivered', 'shipped', 'processing'])
                                    ->whereHas('items', function ($query) use ($product) {
                                        $query->where('product_id', $product->id);
                                    })->exists();
                            @endphp
                            
                            @if($hasReviewed)
                                <div class="alert alert-success rounded-3 text-center mb-0 py-3">
                                    <i class="bi bi-check-circle-fill d-block fs-4 mb-2"></i>
                                    <strong class="d-block">Review Submitted</strong>
                                    <span class="small">Thanks for your feedback!</span>
                                </div>
                            @elseif($hasPurchased)
                                <button type="button" class="btn btn-primary w-100 rounded-pill py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#writeReviewModal">
                                    <i class="bi bi-pencil-square me-2"></i> Write a Review
                                </button>
                                <div class="text-center mt-3">
                                    <span class="badge bg-light text-success border border-success border-opacity-25 rounded-pill px-3 py-2">
                                        <i class="bi bi-gift-fill me-1"></i> Earn 50 Pts
                                    </span>
                                </div>
                            @else
                                <div class="alert alert-light text-center border rounded-3 mb-0 small">
                                    <i class="bi bi-lock-fill text-muted d-block fs-5 mb-1"></i>
                                    You must purchase this product before reviewing it.
                                </div>
                            @endif
                        @else
                            <div class="text-center">
                                <a href="{{ route('login') }}" class="btn btn-outline-primary w-100 rounded-pill py-2">Log in to Review</a>
                            </div>
                        @endauth
                    </div>
                </div>

                {{-- Right Col: Review List --}}
                <div class="col-lg-8">
                    @php
                        $approvedReviews = $product->reviews()->with('user')->where('is_approved', true)->latest()->paginate(5);
                    @endphp

                    @if($approvedReviews->count() > 0)
                        <div class="d-flex flex-column gap-4">
                            @foreach($approvedReviews as $review)
                                <div class="card border-0 rounded-4 p-4" style="background:#f8f9fa;">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <div class="text-warning lh-1" style="font-size:1.1rem;">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="bi {{ $i <= $review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                                    @endfor
                                                </div>
                                                <strong class="ms-2">{{ $review->user->name }}</strong>
                                                <span class="badge bg-success bg-opacity-10 text-success ms-2 rounded-pill" style="font-size:0.7rem;">
                                                    <i class="bi bi-patch-check-fill me-1"></i>Verified Buyer
                                                </span>
                                            </div>
                                            <small class="text-muted">{{ $review->created_at->format('M j, Y') }}</small>
                                        </div>
                                    </div>
                                    
                                    @if($review->title)
                                        <h6 class="fw-bold mb-2">{{ $review->title }}</h6>
                                    @endif
                                    
                                    @if($review->comment)
                                        <p class="mb-3 text-dark">{{ $review->comment }}</p>
                                    @endif

                                    @if($review->photos && count($review->photos) > 0)
                                        <div class="d-flex gap-2 mb-3 overflow-auto custom-scrollbar pb-2">
                                            @foreach($review->photos as $photo)
                                                <a href="{{ Storage::url($photo) }}" target="_blank">
                                                    <img src="{{ Storage::url($photo) }}" class="rounded-3" style="width:80px;height:80px;object-fit:cover;border:1px solid #dee2e6;" alt="Review Image">
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif

                                    @if($review->admin_reply)
                                        <div class="p-3 mt-2 rounded-3" style="background:rgba(108, 92, 231, 0.05); border-left:3px solid var(--ps-primary);">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <i class="bi bi-shop text-primary"></i>
                                                <strong class="text-dark small">Response from Premier Shop</strong>
                                            </div>
                                            <p class="mb-0 small text-muted">{{ $review->admin_reply }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-4">
                            {{ $approvedReviews->fragment('reviewsSection')->links('pagination::bootstrap-5') }}
                        </div>
                    @else
                        <div class="d-flex flex-column align-items-center justify-content-center py-5 rounded-4 border border-dashed text-muted">
                            <i class="bi bi-chat-dots fs-1 mb-3 opacity-50"></i>
                            <h5>No customer reviews yet</h5>
                            <p class="small">We're waiting for our first verified review.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Write Review Modal --}}
        @auth
        <div class="modal fade" id="writeReviewModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold">Write a Review</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('reviews.store', $product->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="d-flex align-items-center gap-3 mb-4 p-3 rounded-3" style="background:#f8f9fa;">
                                <img src="{{ $product->images[0] ?? '' }}" class="rounded" style="width:50px;height:50px;object-fit:cover;">
                                <div>
                                    <div class="fw-bold fs-6 lh-sm">{{ $product->name }}</div>
                                </div>
                            </div>

                            <div class="mb-4 text-center">
                                <label class="form-label fw-bold d-block mb-2">Overall Rating</label>
                                <div class="rating-input d-inline-flex flex-row-reverse justify-content-center gap-2 fs-2 text-warning cursor-pointer">
                                    <input type="radio" name="rating" id="star5" value="5" class="d-none" required><label for="star5"><i class="bi bi-star"></i></label>
                                    <input type="radio" name="rating" id="star4" value="4" class="d-none"><label for="star4"><i class="bi bi-star"></i></label>
                                    <input type="radio" name="rating" id="star3" value="3" class="d-none"><label for="star3"><i class="bi bi-star"></i></label>
                                    <input type="radio" name="rating" id="star2" value="2" class="d-none"><label for="star2"><i class="bi bi-star"></i></label>
                                    <input type="radio" name="rating" id="star1" value="1" class="d-none"><label for="star1"><i class="bi bi-star"></i></label>
                                </div>
                                <style>
                                    .rating-input label { cursor: pointer; transition: 0.2s; }
                                    .rating-input label:hover,
                                    .rating-input label:hover ~ label,
                                    .rating-input input:checked ~ label {
                                        color: #ffc107;
                                    }
                                    .rating-input label:hover i::before,
                                    .rating-input label:hover ~ label i::before,
                                    .rating-input input:checked ~ label i::before {
                                        content: "\F586"; /* bi-star-fill */
                                    }
                                </style>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Review Title</label>
                                <input type="text" name="title" class="form-control" placeholder="Summarize your experience" maxlength="255">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Review Details</label>
                                <textarea name="comment" class="form-control" rows="4" placeholder="What did you like or dislike?"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Add Photos (Optional)</label>
                                <input type="file" name="photos[]" class="form-control" multiple accept="image/jpeg,image/png,image/gif">
                                <small class="text-muted d-block mt-1">Upload up to 4 images (Max 2MB each).</small>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 pt-0">
                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4" style="background:var(--ps-gradient);border:none;">Submit Review</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endauth
                </div>
            </div>
        </div>

        {{-- Related Products --}}
        @if(isset($relatedProducts) && $relatedProducts->count() > 0)
            <div class="row mt-5 pt-5 border-top">
                <h3 class="fw-bold mb-4">You May Also Like</h3>
                @foreach($relatedProducts as $related)
                    @include('partials.product_card', ['product' => $related])
                @endforeach
            </div>
        @endif

        {{-- Recently Viewed Products --}}
        @if(isset($recentlyViewed) && $recentlyViewed->count() > 1) 
            <div class="row mt-5 pt-5 border-top">
                <h3 class="fw-bold mb-4">Recently Viewed</h3>
                @foreach($recentlyViewed as $recent)
                    @if($recent->id !== $product->id)
                        @include('partials.product_card', ['product' => $recent])
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    {{-- Mobile Sticky Action Bar --}}
    @if($product->stock > 0)
        <div class="mobile-sticky-action-bar d-md-none glass-card shadow-lg">
            <div class="container d-flex align-items-center justify-content-between py-2 px-3">
                <div class="product-info-minimal">
                    <div class="fw-bold text-dark truncate-1" style="font-size: 0.85rem; max-width: 140px;">{{ $product->name }}</div>
                    <div class="text-primary fw-bold" style="font-size: 1rem;">£{{ number_format($product->price, 2) }}</div>
                </div>
                <div class="d-flex gap-2">
                    <form action="{{ route('cart.add') }}" method="POST" class="ajax-form">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill p-2" style="width:42px; height:42px;">
                            <i class="bi bi-bag-plus-fill fs-5"></i>
                        </button>
                    </form>
                    <form action="{{ route('cart.buyNow') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm" style="background: var(--ps-gradient); border: none; height: 42px;">
                            Buy Now
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif
</section>
@endsection
