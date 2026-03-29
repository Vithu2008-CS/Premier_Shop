@extends('layouts.app')
@section('title', 'Premier Shop — Your One-Stop Shop for Quality Products')

@push('seo')
{{-- JSON-LD Structured Data --}}
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "WebSite",
  "name": "Premier Shop",
  "url": "{{ url('/') }}",
  "description": "Your one-stop destination for quality products at unbeatable prices.",
  "potentialAction": {
    "@@type": "SearchAction",
    "target": "{{ route('products.index') }}?search={search_term_string}",
    "query-input": "required name=search_term_string"
  }
}
</script>
@endpush

@section('content')

    {{-- ═══════════════════════════════════════════════════════════
         PARALLAX HERO SECTION
    ═══════════════════════════════════════════════════════════ --}}
    @if(isset($sliders) && $sliders->count() > 0)
        <section class="parallax-hero" id="heroSection">
            {{-- Background Carousel (parallax-driven) --}}
            <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="6000">
                <div class="carousel-inner">
                    @foreach($sliders as $i => $slider)
                        <div class="carousel-item {{ $i == 0 ? 'active' : '' }}">
                            <div class="parallax-bg" style="background-image: url('{{ (str_starts_with($slider->image_path, 'http') || str_starts_with($slider->image_path, 'data:')) ? $slider->image_path : asset('storage/' . $slider->image_path) }}');" aria-label="{{ $slider->title ?? 'Promotional slide' }}"></div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Gradient Overlay --}}
            <div class="hero-overlay"></div>



            {{-- Hero Content --}}
            <div class="hero-content">
                <div class="container text-center">
                    <div class="hero-badge scroll-reveal" data-delay="0">
                        <span class="badge-pill">
                            <span class="badge-dot"></span>
                            <span class="text-uppercase tracking-widest fw-bold" style="font-size: 0.7rem;">Curated Selection</span>
                        </span>
                    </div>
                    <h1 class="hero-title scroll-reveal" data-delay="100">
                        <span class="fw-light">Elevate Your</span><br>
                        <span class="hero-title-accent glass-text-wrap">Shopping Experience</span>
                    </h1>
                    <p class="hero-subtitle scroll-reveal" data-delay="200">
                        Premium products, unbeatable prices, and fast delivery straight to your door.
                    </p>
                    <div class="hero-actions scroll-reveal" data-delay="300">
                        <a href="{{ route('products.index') }}" class="btn-hero-primary premium-btn">
                            <span>Shop Now</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                        <a href="{{ route('offers') }}" class="btn-hero-glass">
                            <i class="bi bi-lightning-charge-fill text-warning"></i>
                            <span>View Offers</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Frosted Section Divider --}}
            <div class="frosted-divider"></div>

            {{-- Scroll Indicator --}}
            <div class="scroll-indicator">
                <div class="mouse">
                    <div class="wheel"></div>
                </div>
                <span>Scroll to explore</span>
            </div>

            {{-- Carousel Indicators --}}
            @if($sliders->count() > 1)
            <div class="hero-carousel-dots">
                @foreach($sliders as $i => $slider)
                    <button data-bs-target="#heroCarousel" data-bs-slide-to="{{ $i }}" class="{{ $i == 0 ? 'active' : '' }}" aria-label="Slide {{ $i + 1 }}"></button>
                @endforeach
            </div>
            @endif
        </section>
    @else
        {{-- Fallback Hero --}}
        <section class="parallax-hero" id="heroSection">
            <div class="parallax-bg" style="background-image: url('https://images.unsplash.com/photo-1498049794561-7780e7231661?q=80&w=1920&auto=format&fit=crop');"></div>
            <div class="hero-overlay"></div>

            <div class="hero-content">
                <div class="container text-center">
                    <div class="hero-badge scroll-reveal" data-delay="0">
                        <span class="badge-dot"></span>
                        <span>Curated Selection</span>
                    </div>
                    <h1 class="hero-title scroll-reveal" data-delay="100">
                        Discover<br>
                        <span class="hero-title-accent">Quality Goods</span>
                    </h1>
                    <p class="hero-subtitle scroll-reveal" data-delay="200">
                        Explore a curated selection of premium electronics, lifestyle accessories, and everyday essentials.
                    </p>
                    <div class="hero-actions scroll-reveal" data-delay="300">
                        <a href="{{ route('products.index') }}" class="btn-hero-primary">
                            <span>Shop Now</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                        <a href="{{ route('offers') }}" class="btn-hero-glass">
                            <i class="bi bi-lightning-charge-fill"></i>
                            <span>View Offers</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="scroll-indicator">
                <div class="mouse"><div class="wheel"></div></div>
                <span>Scroll to explore</span>
            </div>
        </section>
    @endif


    {{-- ═══════════════════════════════════════════════════════════
         CATEGORY SHOWCASE
    ═══════════════════════════════════════════════════════════ --}}
    @if(isset($categories) && $categories->count() > 0)
    <section class="category-showcase-section" aria-label="Shop by Category">
        <div class="container">
            <div class="premium-section-header scroll-reveal">
                <div class="header-line"></div>
                <h2 class="section-title">Shop by <span class="gradient-text">Category</span></h2>
                <a href="{{ route('categories') }}" class="view-all-link">Explore All <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="category-scroll-container">
                <div class="category-grid-premium">
                    @foreach($categories as $i => $cat)
                        <a href="{{ route('products.index', ['category' => $cat->slug]) }}" class="category-glass-card scroll-reveal" data-delay="{{ ($i % 6) * 60 }}">
                            <div class="category-glow"></div>
                            <div class="category-icon-wrap">
                                @if($cat->image)
                                    <img src="{{ $cat->image }}" alt="{{ $cat->name }}" loading="lazy">
                                @else
                                    <div class="icon-fallback"><i class="bi bi-box-seam"></i></div>
                                @endif
                            </div>
                            <div class="category-info">
                                <span class="category-name">{{ $cat->name }}</span>
                                <span class="category-meta">{{ $cat->products_count ?? $cat->products()->where('is_active', true)->count() }} Products</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif


    {{-- ═══════════════════════════════════════════════════════════
         HOT OFFERS
    ═══════════════════════════════════════════════════════════ --}}
    @if(isset($offerProducts) && $offerProducts->count() > 0)
    <section class="section-padding" aria-label="Hot Offers">
        <div class="section-blob section-blob-left"></div>
        <div class="container">
            <div class="section-header scroll-reveal">
                <div>
                    <h2 class="section-title"><i class="bi bi-lightning-fill text-warning me-2"></i>Hot <span class="gradient-text">Offers</span></h2>
                    <p class="section-subtitle mb-0">Buy in bulk and save big!</p>
                </div>
                <a href="{{ route('offers') }}" class="btn btn-outline-primary rounded-pill">See All Offers <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
            <div class="row g-4">
                @foreach($offerProducts as $i => $product)
                    @include('partials.product_card', ['delay' => $i + 1])
                @endforeach
            </div>
        </div>
    </section>
    @endif


    {{-- ═══════════════════════════════════════════════════════════
         MOST POPULAR
    ═══════════════════════════════════════════════════════════ --}}
    @if(isset($popularProducts) && $popularProducts->count() > 0)
    <section class="section-padding bg-dynamic-light" aria-label="Most Popular Products">
        <div class="section-blob section-blob-right"></div>
        <div class="container">
            <div class="section-header scroll-reveal">
                <h2 class="section-title"><i class="bi bi-star-fill text-warning me-2"></i>Most <span class="gradient-text">Popular</span></h2>
                <a href="{{ route('products.index', ['sort' => 'popular']) }}" class="btn btn-outline-primary rounded-pill">View All</a>
            </div>
            <div class="row g-4">
                @foreach($popularProducts as $i => $product)
                    @include('partials.product_card', ['product' => $product, 'delay' => ($i % 4) + 1])
                @endforeach
            </div>
        </div>
    </section>
    @endif


    {{-- ═══════════════════════════════════════════════════════════
         NEW ARRIVALS
    ═══════════════════════════════════════════════════════════ --}}
    @if(isset($newProducts) && $newProducts->count() > 0)
    <section class="section-padding" aria-label="New Arrivals">
        <div class="section-blob section-blob-center"></div>
        <div class="container">
            <div class="section-header scroll-reveal">
                <h2 class="section-title"><i class="bi bi-rocket-takeoff-fill text-primary me-2"></i>New <span class="text-primary">Arrivals</span></h2>
                <a href="{{ route('products.index', ['sort' => 'newest']) }}" class="btn btn-outline-primary rounded-pill">View All</a>
            </div>
            <div class="row g-4">
                @foreach($newProducts as $i => $product)
                    @include('partials.product_card', ['product' => $product, 'delay' => ($i % 4) + 1])
                @endforeach
            </div>
        </div>
    </section>
    @endif


    {{-- ═══════════════════════════════════════════════════════════
         DISCOVER MORE
    ═══════════════════════════════════════════════════════════ --}}
    @if(isset($randomProducts) && $randomProducts->count() > 0)
    <section class="section-padding bg-dynamic-light" aria-label="Discover More Products">
        <div class="container">
            <div class="section-header scroll-reveal">
                <h2 class="section-title">Discover <span class="gradient-text">More</span></h2>
                <a href="{{ route('products.index') }}" class="btn btn-outline-primary rounded-pill">View All</a>
            </div>
            <div class="row g-4">
                @foreach($randomProducts as $i => $product)
                    @include('partials.product_card', ['product' => $product, 'delay' => ($i % 4) + 1])
                @endforeach
            </div>
        </div>
    </section>
    @endif


    {{-- ═══════════════════════════════════════════════════════════
         RECENTLY VIEWED
    ═══════════════════════════════════════════════════════════ --}}
    @if(isset($recentlyViewed) && $recentlyViewed->count() > 0)
    <section class="section-padding" aria-label="Recently Viewed Products">
        <div class="container">
            <div class="section-header scroll-reveal">
                <h2 class="section-title"><i class="bi bi-clock-history text-primary me-2"></i>Recently <span class="gradient-text">Viewed</span></h2>
            </div>
            <div class="recently-viewed-scroll scroll-reveal">
                @foreach($recentlyViewed as $product)
                    <div class="recently-viewed-item">
                        <a href="{{ route('products.show', $product->slug) }}" class="recently-viewed-card d-block text-decoration-none">
                            <div class="recently-viewed-img">
                                @if($product->images && count($product->images) > 0)
                                    <img src="{{ $product->images[0] }}" alt="{{ $product->name }}" loading="lazy">
                                @else
                                    <div class="d-flex align-items-center justify-content-center h-100 w-100 bg-dynamic-light">
                                        <i class="bi bi-image text-muted" style="font-size:1.5rem;"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="recently-viewed-body">
                                <h6 class="recently-viewed-title">{{ Str::limit($product->name, 30) }}</h6>
                                <span class="recently-viewed-price">£{{ number_format($product->price, 2) }}</span>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif


    {{-- ═══════════════════════════════════════════════════════════
         ANIMATED TRUST BAR
    ═══════════════════════════════════════════════════════════ --}}
    <section class="trust-bar-modern" aria-label="Why Shop With Us">
        <div class="container">
            <div class="row g-4">
                <div class="col-6 col-md-3 scroll-reveal" data-delay="0">
                    <div class="trust-card">
                        <div class="trust-card-icon">
                            <i class="bi bi-truck"></i>
                        </div>
                        <h6>Fast Delivery</h6>
                        <small>We deliver within 24 hours</small>
                    </div>
                </div>
                <div class="col-6 col-md-3 scroll-reveal" data-delay="100">
                    <div class="trust-card">
                        <div class="trust-card-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h6>Secure Checkout</h6>
                        <small>100% secure payments</small>
                    </div>
                </div>
                <div class="col-6 col-md-3 scroll-reveal" data-delay="200">
                    <div class="trust-card">
                        <div class="trust-card-icon">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </div>
                        <h6>Easy Returns</h6>
                        <small>30-day return policy</small>
                    </div>
                </div>
                <div class="col-6 col-md-3 scroll-reveal" data-delay="300">
                    <div class="trust-card">
                        <div class="trust-card-icon">
                            <i class="bi bi-headset"></i>
                        </div>
                        <h6>24/7 Support</h6>
                        <small>Dedicated customer care</small>
                    </div>
                </div>
            </div>
        </div>
    </section>


    {{-- ═══════════════════════════════════════════════════════════
         SCRIPTS — Parallax + Scroll Reveal + Counter
    ═══════════════════════════════════════════════════════════ --}}
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {

        // ── Parallax Hero ──────────────────────────────────────
        const heroSection = document.getElementById('heroSection');
        if (heroSection && window.innerWidth > 768) {
            const parallaxBgs = heroSection.querySelectorAll('.parallax-bg');
            let ticking = false;

            window.addEventListener('scroll', function() {
                if (!ticking) {
                    requestAnimationFrame(function() {
                        const scrolled = window.pageYOffset;
                        const heroHeight = heroSection.offsetHeight;

                        if (scrolled < heroHeight) {
                            const yPos = scrolled * 0.4;
                            parallaxBgs.forEach(bg => {
                                bg.style.transform = 'translate3d(0, ' + yPos + 'px, 0) scale(1.1)';
                            });

                            // Fade hero content on scroll
                            const heroContent = heroSection.querySelector('.hero-content');
                            if (heroContent) {
                                const opacity = 1 - (scrolled / (heroHeight * 0.6));
                                const translateY = scrolled * 0.2;
                                heroContent.style.opacity = Math.max(0, opacity);
                                heroContent.style.transform = 'translateY(' + translateY + 'px)';
                            }
                        }
                        ticking = false;
                    });
                    ticking = true;
                }
            });
        }

        // ── Scroll Reveal (IntersectionObserver) ───────────────
        const revealElements = document.querySelectorAll('.scroll-reveal');
        if (revealElements.length > 0) {
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        const delay = parseInt(entry.target.getAttribute('data-delay') || '0');
                        setTimeout(function() {
                            entry.target.classList.add('revealed');
                        }, delay);
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.08,
                rootMargin: '0px 0px -40px 0px'
            });

            revealElements.forEach(function(el) {
                observer.observe(el);
            });
        }

        // ── Bootstrap Carousel Init ────────────────────────────
        var myCarousel = document.querySelector('#heroCarousel');
        if (myCarousel) {
            var carousel = new bootstrap.Carousel(myCarousel, {
                interval: 6000,
                ride: 'carousel',
                pause: false
            });
            carousel.cycle();
        }

        // ── Smooth Scroll Indicator ────────────────────────────
        const scrollIndicator = document.querySelector('.scroll-indicator');
        if (scrollIndicator) {
            scrollIndicator.addEventListener('click', function() {
                const nextSection = heroSection.nextElementSibling;
                if (nextSection) {
                    nextSection.scrollIntoView({ behavior: 'smooth' });
                }
            });

            // Hide on scroll
            window.addEventListener('scroll', function() {
                if (window.scrollY > 100) {
                    scrollIndicator.style.opacity = '0';
                    scrollIndicator.style.pointerEvents = 'none';
                } else {
                    scrollIndicator.style.opacity = '1';
                    scrollIndicator.style.pointerEvents = 'auto';
                }
            });
        }
    });
    </script>
    @endpush

@endsection
