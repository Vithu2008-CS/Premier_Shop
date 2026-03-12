@extends('layouts.app')
@section('title', 'Products — Premier Shop')

@section('content')
    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                {{-- Sidebar Filters --}}
                <div class="col-lg-3">
                    <div class="card" style="position:sticky;top:90px;overflow:visible;">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3"><i class="bi bi-funnel me-2"></i>Filters</h5>
                            <form action="{{ route('products.index') }}" method="GET">
                                {{-- Search --}}
                                <div class="mb-3">
                                    <label class="form-label">Search</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i
                                                class="bi bi-search"></i></span>
                                        <input type="text" name="search" class="form-control border-start-0"
                                            placeholder="Product name..." value="{{ request('search') }}">
                                    </div>
                                </div>
                                {{-- Category --}}
                                <div class="mb-3">
                                    <label class="form-label">Category</label>
                                    <div class="dropdown">
                                        <button
                                            class="form-select text-start d-flex justify-content-between align-items-center"
                                            type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                            id="categoryDropdownBtn">
                                            @php
                                                $selectedCat = request('category') ? \App\Models\Category::where('slug', request('category'))->first() : null;
                                            @endphp
                                            <span>{{ $selectedCat ? $selectedCat->name : 'All Categories' }}</span>
                                        </button>
                                        <input type="hidden" name="category" id="categoryInput"
                                            value="{{ request('category') }}">
                                        <ul class="dropdown-menu w-100 shadow-sm"
                                            style="max-height: 300px; overflow-y: auto;">
                                            <li>
                                                <a class="dropdown-item text-wrap cursor-pointer {{ !request('category') ? 'active' : '' }}"
                                                    href="javascript:void(0)"
                                                    onclick="document.getElementById('categoryInput').value=''; document.getElementById('categoryDropdownBtn').querySelector('span').innerText='All Categories';">All
                                                    Categories</a>
                                            </li>
                                            @foreach(\App\Models\Category::withCount('products')->get() as $cat)
                                                <li>
                                                    <a class="dropdown-item text-wrap cursor-pointer {{ request('category') == $cat->slug ? 'active' : '' }}"
                                                        href="javascript:void(0)"
                                                        onclick="document.getElementById('categoryInput').value='{{ $cat->slug }}'; document.getElementById('categoryDropdownBtn').querySelector('span').innerText='{{ addslashes($cat->name) }}';">
                                                        {{ $cat->name }} ({{ $cat->products_count }})
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                {{-- Sort --}}
                                <div class="mb-4">
                                    <label class="form-label">Sort By</label>
                                    <div class="dropdown">
                                        <button
                                            class="form-select text-start d-flex justify-content-between align-items-center"
                                            type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                            id="sortDropdownBtn">
                                            @php
                                                $sort = request('sort', 'newest');
                                                $sortText = 'Newest First';
                                                if ($sort == 'price_low')
                                                    $sortText = 'Price: Low → High';
                                                if ($sort == 'price_high')
                                                    $sortText = 'Price: High → Low';
                                                if ($sort == 'name')
                                                    $sortText = 'Name: A-Z';
                                            @endphp
                                            <span>{{ $sortText }}</span>
                                        </button>
                                        <input type="hidden" name="sort" id="sortInput" value="{{ $sort }}">
                                        <ul class="dropdown-menu w-100 shadow-sm">
                                            <li><a class="dropdown-item cursor-pointer {{ $sort == 'newest' ? 'active' : '' }}"
                                                    href="javascript:void(0)"
                                                    onclick="document.getElementById('sortInput').value='newest'; document.getElementById('sortDropdownBtn').querySelector('span').innerText='Newest First';">Newest
                                                    First</a></li>
                                            <li><a class="dropdown-item cursor-pointer {{ $sort == 'price_low' ? 'active' : '' }}"
                                                    href="javascript:void(0)"
                                                    onclick="document.getElementById('sortInput').value='price_low'; document.getElementById('sortDropdownBtn').querySelector('span').innerText='Price: Low → High';">Price:
                                                    Low → High</a></li>
                                            <li><a class="dropdown-item cursor-pointer {{ $sort == 'price_high' ? 'active' : '' }}"
                                                    href="javascript:void(0)"
                                                    onclick="document.getElementById('sortInput').value='price_high'; document.getElementById('sortDropdownBtn').querySelector('span').innerText='Price: High → Low';">Price:
                                                    High → Low</a></li>
                                            <li><a class="dropdown-item cursor-pointer {{ $sort == 'name' ? 'active' : '' }}"
                                                    href="javascript:void(0)"
                                                    onclick="document.getElementById('sortInput').value='name'; document.getElementById('sortDropdownBtn').querySelector('span').innerText='Name: A-Z';">Name:
                                                    A-Z</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary"><i class="bi bi-filter me-1"></i> Apply
                                        Filters</button>
                                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">Clear
                                        All</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Product Grid --}}
                <div class="col-lg-9">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="section-title mb-0">
                                @if(request('category'))
                                    {{ \App\Models\Category::where('slug', request('category'))->first()?->name ?? 'All' }}
                                    Products
                                @else
                                    All Products
                                @endif
                            </h2>
                            <p class="text-muted mb-0">{{ $products->total() }} products found</p>
                        </div>
                    </div>

                    <div class="row g-4">
                        @forelse($products as $index => $product)
                            <div class="col-6 col-md-4 fade-up delay-{{ ($index % 6) + 1 }}">
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
                                    @if($product->is_age_restricted)
                                        <span class="product-badge bg-danger">🔞 16+</span>
                                    @elseif($product->created_at->diffInDays(now()) < 7)
                                        <span class="product-badge bg-success">NEW</span>
                                    @endif
                                    <div class="product-img-wrap">
                                        @if($product->images && count($product->images) > 0)
                                            <img src="{{ $product->images[0] }}" alt="{{ $product->name }}" loading="lazy">
                                        @else
                                            <div class="d-flex align-items-center justify-content-center h-100"
                                                style="background:linear-gradient(135deg,#f0f0f5,#e8e8f0);">
                                                <i class="bi bi-image text-muted" style="font-size:3rem;"></i>
                                            </div>
                                        @endif
                                        <div class="product-overlay">
                                            <a href="{{ route('products.show', $product->slug) }}"
                                                class="btn btn-light btn-sm"><i class="bi bi-eye"></i></a>
                                            <form action="{{ route('cart.add') }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                <input type="hidden" name="quantity" value="1">
                                                <button class="btn btn-primary btn-sm btn-add-to-cart" title="Add to Cart"><i
                                                        class="bi bi-bag-plus"></i></button>
                                            </form>
                                            <form action="{{ route('cart.buyNow') }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                <input type="hidden" name="quantity" value="1">
                                                <button class="btn btn-sm text-white" style="background: linear-gradient(135deg, #0ba360, #3cba92); border: none;" title="Buy Now"><i
                                                        class="bi bi-lightning-charge"></i></button>
                                            </form>
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
                        @empty
                            <div class="col-12 text-center py-5">
                                <i class="bi bi-search text-muted" style="font-size:4rem;"></i>
                                <h5 class="mt-3 fw-bold">No products found</h5>
                                <p class="text-muted">Try adjusting your search or filters</p>
                                <a href="{{ route('products.index') }}" class="btn btn-primary">View All Products</a>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-4">{{ $products->withQueryString()->links() }}</div>
                </div>
            </div>
        </div>
    </section>
@endsection