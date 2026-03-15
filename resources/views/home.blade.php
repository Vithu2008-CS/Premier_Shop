@extends('layouts.app')
@section('title', 'Premier Shop — Your One-Stop Shop for Quality Products')

@section('content')
    {{-- Hero --}}
    {{-- Hero --}}
    @if(isset($sliders) && $sliders->count() > 0)
        <section class="hero-slider-section py-0">
            <div class="container px-0 px-md-3 mt-md-4 mb-md-3">
                <div id="heroCarousel" class="carousel slide carousel-fade hero-carousel-wrapper" data-bs-ride="carousel" data-bs-interval="5000">
                    <div class="carousel-indicators">
                        @foreach($sliders as $i => $slider)
                            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $i }}" class="{{ $i == 0 ? 'active' : '' }}" aria-label="Slide {{ $i + 1 }}"></button>
                        @endforeach
                    </div>

                <div class="carousel-inner">
                    @foreach($sliders as $i => $slider)
                        <div class="carousel-item {{ $i == 0 ? 'active' : '' }}">
                            <a href="{{ $slider->link_url ?? '#' }}" class="d-block w-100 position-relative text-decoration-none">
                                <div class="slider-image-wrapper">
                                    <img src="{{ (str_starts_with($slider->image_path, 'http') || str_starts_with($slider->image_path, 'data:')) ? $slider->image_path : asset('storage/' . $slider->image_path) }}" class="d-block w-100 hero-slider-img" alt="{{ $slider->title ? $slider->title . ' - ' . $slider->subtitle : 'Promotional Offer Slide' }}">
                                    <div class="slider-overlay"></div>
                                </div>
                                @if($slider->title || $slider->subtitle)
                                <div class="carousel-caption">
                                    <div class="caption-content fade-up">
                                        @if($slider->title)<h1 class="slider-title">{{ \Illuminate\Support\Str::words($slider->title, 7, '...') }}</h1>@endif
                                        @if($slider->subtitle)<p class="slider-subtitle">{{ $slider->subtitle }}</p>@endif
                                    </div>
                                </div>
                                @endif
                            </a>
                        </div>
                    @endforeach
                </div>

                @if($sliders->count() > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev" style="width: 5%;">
                        <span class="carousel-control-prev-icon" aria-hidden="true" style="background-color: rgba(0,0,0,0.5); border-radius: 50%; padding: 1.5rem;"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next" style="width: 5%;">
                        <span class="carousel-control-next-icon" aria-hidden="true" style="background-color: rgba(0,0,0,0.5); border-radius: 50%; padding: 1.5rem;"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                @endif
            </div>
            </div>
        </section>
    @else
        @php
        $dicedSlides = [
            [
                'title' => 'Premium Devices',
                'image' => 'https://images.unsplash.com/photo-1498049794561-7780e7231661?q=80&w=1080&auto=format&fit=crop',
            ],
            [
                'title' => 'Accessories',
                'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?q=80&w=1080&auto=format&fit=crop',
            ],
            [
                'title' => 'Smart Watches',
                'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?q=80&w=1080&auto=format&fit=crop',
            ],
            [
                'title' => 'Lifestyle Quality',
                'image' => 'https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?q=80&w=1080&auto=format&fit=crop',
            ]
        ];
        @endphp
        <x-diced-hero 
            topText="Discover Premier"
            mainText="Quality Goods"
            subMainText="Explore a curated selection of premium electronics, lifestyle accessories, and everyday essentials. Unveil a paramount collection sourced for those who demand the absolute best."
            buttonText="Shop Now"
            buttonLink="{{ route('products.index') }}"
            :slides="$dicedSlides"
        />
    @endif

    @push('styles')
    <style>
        .hero-slider-section {
            width: 100%;
            margin-top: 0;
            padding: 0 !important;
            max-width: 100% !important;
        }
        .hero-carousel-wrapper {
            border-radius: 0;
            overflow: hidden;
            box-shadow: none;
            background-color: #1a1d24; /* Fallback background for network issues */
        }
        .hero-slider-section .hero-section {
            border-radius: 0 !important;
            box-shadow: none !important;
        }
        .slider-image-wrapper {
            position: relative;
            width: 100%;
            height: 45vh; /* Controlled height for mobile */
            display: block;
            overflow: hidden;
            background-color: #1a1d24; /* Fallback for networks */
        }
        .hero-slider-img {
            width: 100%;
            height: 45vh; /* Match container */
            object-fit: cover; /* Prevents squishing */
            display: block; 
            transform: scale(1); /* stable scale for mobile */
            transition: transform 6s ease;
            color: rgba(255, 255, 255, 0.7); 
            font-size: 1.1rem;
            text-align: center;
        }
        .slider-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(to top, rgba(15, 17, 23, 0.9) 0%, rgba(15, 17, 23, 0.2) 60%, rgba(0, 0, 0, 0.1) 100%);
        }
        .carousel-caption {
            bottom: 10%;
            left: 10%;
            right: 10%;
            text-align: left;
            padding-bottom: 30px;
        }
        .slider-title {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 2.2rem;
            color: #fff;
            margin-bottom: 0.5rem;
            line-height: 1.2;
            text-shadow: 0 4px 15px rgba(0,0,0,0.5);
        }
        .slider-subtitle {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.85);
            max-width: 600px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.5);
        }
        .slider-btn {
            border-radius: 50px;
            padding: 12px 28px;
            font-weight: 600;
            background: linear-gradient(135deg, #6C5CE7, #A29BFE);
            border: none;
            box-shadow: 0 6px 20px rgba(108, 92, 231, 0.4);
        }
        
        /* Carousel indicator styling */
        .carousel-indicators {
            margin-bottom: 1.5rem;
        }
        .carousel-indicators button {
            width: 10px !important;
            height: 10px !important;
            border-radius: 50%;
            margin: 0 6px;
            background-color: rgba(255, 255, 255, 0.5) !important;
            border: none !important;
        }
        .carousel-indicators button.active {
            background-color: #6C5CE7 !important;
            transform: scale(1.3);
        }

        @media (min-width: 768px) {
            .hero-slider-section {
                padding: 0 !important;
            }
            .hero-carousel-wrapper {
                border-radius: 20px;
                box-shadow: 0 15px 35px rgba(0,0,0,0.15);
            }
            .slider-image-wrapper {
                height: 400px; /* Reduced desktop height */
            }
            .hero-slider-img {
                height: 400px; /* Reduced desktop height */
                object-fit: cover; 
                transform: scale(1.02); /* very subtle initial zoom for life, won't cut aggressively */
            }
            .carousel-item.active .hero-slider-img {
                transform: scale(1);
            }
            .carousel-caption {
                bottom: 15%;
            }
            .slider-title {
                font-size: 3.5rem;
            }
            .slider-subtitle {
                font-size: 1.25rem;
            }
        }
    </style>
    @endpush

    {{-- Offers Banner --}}
    @if(isset($offerProducts) && $offerProducts->count() > 0)
        <section class="py-5 reveal-3d">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="section-title"><i class="bi bi-lightning-fill text-warning me-2"></i>Hot <span
                                class="gradient-text">Offers</span></h2>
                        <p class="section-subtitle mb-0">Buy in bulk and save big!</p>
                    </div>
                    <a href="{{ route('offers') }}" class="btn btn-outline-primary" style="border-radius:50px;">See All Offers
                        <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
                <div class="row g-4 stagger-children">
                    @foreach($offerProducts as $i => $product)
                        <div class="col-6 col-md-3 fade-up delay-{{ $i + 1 }}">
                            <div class="product-card position-relative">
                                @auth
                                    @php
                                        $inWishlist = \App\Models\UserItem::where('user_id', auth()->id())->where('product_id', $product->id)->where('type', 'wishlist')->exists();
                                    @endphp
                                    <form action="{{ route('wishlists.toggle', $product->id) }}" method="POST" class="position-absolute" style="top:10px; right:10px; z-index:10;">
                                        @csrf
                                        <button type="submit" class="btn btn-light btn-sm rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width:35px;height:35px;" title="{{ $inWishlist ? 'Remove from wishlist' : 'Add to wishlist' }}">
                                            <i class="bi bi-heart{{ $inWishlist ? '-fill text-danger' : '' }} fs-6"></i>
                                        </button>
                                    </form>
                                @endauth
                                <span class="product-badge bg-danger"><i class="bi bi-lightning-fill"></i>
                                    {{ number_format($product->offer_discount_percent) }}% OFF</span>
                                <div class="product-img-wrap">
                                    @if($product->images && count($product->images) > 0)
                                        <img src="{{ $product->images[0] }}" alt="{{ $product->name }}" loading="lazy">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center h-100" style="background:#f0f0f5;">
                                            <i class="bi bi-image text-muted" style="font-size:2.5rem;"></i></div>
                                    @endif
                                    <div class="product-overlay">
                                        <a href="{{ route('products.show', $product->slug) }}" class="btn btn-light btn-sm"><i
                                                class="bi bi-eye"></i> View</a>
                                    </div>
                                </div>
                                <div class="product-body">
                                    <div class="product-category">{{ $product->category?->name }}</div>
                                    <h5 class="product-title"><a
                                            href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a></h5>
                                    <div class="product-price">
                                        £{{ number_format($product->offer_price, 2) }}
                                        <span class="original-price">£{{ number_format($product->price, 2) }}</span>
                                    </div>
                                    <span class="badge mt-1"
                                        style="background:rgba(0,206,201,0.1);color:#00CEC9;font-size:0.7rem;">Buy
                                        {{ $product->offer_min_qty }}+ save
                                        {{ number_format($product->offer_discount_percent) }}%</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif


    {{-- Popular Products --}}
    @if(isset($popularProducts) && $popularProducts->count() > 0)
    <section class="py-5 bg-light reveal-3d">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="section-title"><i class="bi bi-star-fill text-warning me-2"></i>Most <span class="gradient-text">Popular</span></h2>
                <a href="{{ route('products.index', ['sort' => 'popular']) }}" class="btn btn-outline-primary" style="border-radius:50px;">View All</a>
            </div>
            <div class="row g-4 stagger-children">
                @foreach($popularProducts as $i => $product)
                    @include('partials.product_card', ['product' => $product, 'delay' => ($i % 4) + 1])
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- New Arrivals --}}
    @if(isset($newProducts) && $newProducts->count() > 0)
    <section class="py-5 reveal-3d">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="section-title"><i class="bi bi-rocket-takeoff-fill text-primary me-2"></i>New <span class="text-primary">Arrivals</span></h2>
                <a href="{{ route('products.index', ['sort' => 'newest']) }}" class="btn btn-outline-primary" style="border-radius:50px;">View All</a>
            </div>
            <div class="row g-4 stagger-children">
                @foreach($newProducts as $i => $product)
                    @include('partials.product_card', ['product' => $product, 'delay' => ($i % 4) + 1])
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Random Products --}}
    @if(isset($randomProducts) && $randomProducts->count() > 0)
    <section class="py-5 bg-light reveal-3d">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="section-title">Discover <span class="gradient-text">More</span></h2>
                <a href="{{ route('products.index') }}" class="btn btn-outline-primary" style="border-radius:50px;">View All</a>
            </div>
            <div class="row g-4 stagger-children">
                @foreach($randomProducts as $i => $product)
                    @include('partials.product_card', ['product' => $product, 'delay' => ($i % 4) + 1])
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Trust Bar --}}
    <section class="trust-bar reveal-3d">
        <div class="container">
            <div class="row g-4 stagger-children">
                <div class="col-6 col-md-3">
                    <div class="trust-item">
                        <div class="trust-icon"><i class="bi bi-truck"></i></div>
                        <h6>Fast Delivery</h6>
                        <small>we deliver your order within 24 hours</small>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="trust-item">
                        <div class="trust-icon"><i class="bi bi-shield-check"></i></div>
                        <h6>Secure Checkout</h6>
                        <small>100% secure payments</small>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="trust-item">
                        <div class="trust-icon"><i class="bi bi-arrow-counterclockwise"></i></div>
                        <h6>Easy Returns</h6>
                        <small>30-day return policy</small>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="trust-item">
                        <div class="trust-icon"><i class="bi bi-headset"></i></div>
                        <h6>24/7 Support</h6>
                        <small>Dedicated customer care</small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var myCarousel = document.querySelector('#heroCarousel');
            if(myCarousel) {
                // Explicitly initialize Bootstrap carousel for automatic sliding
                var carousel = new bootstrap.Carousel(myCarousel, {
                    interval: 1000,
                    ride: 'carousel',
                    pause: 'hover'
                });
                // Force cycle immediately after load
                carousel.cycle();
            }
        });
    </script>
    @endpush
@endsection