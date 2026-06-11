{{--
    products/index.blade.php — Product catalogue listing
    ======================================================
    Filterable, sortable, paginated product grid.
    Left sidebar: category filter, price range, product type, age-restriction toggle.
    Right area: sort dropdown, product cards grid (partials/product_card).
    AJAX search suggestions via products.suggest endpoint.
    Variables: $products (paginated), $categories, $filters (active filters array)
--}}
@extends('layouts.app')
@section('title', 'Products — Premier Shop')

@section('content')
    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                {{-- Mobile Filter Toggle --}}
                <div class="col-12 d-lg-none reveal-fade">
                    <button class="btn btn-outline-primary w-100 d-flex justify-content-between align-items-center py-3 rounded-4" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas">
                        <span><i class="bi bi-funnel me-2"></i>Filters</span>
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>

                {{-- Sidebar Filters (Desktop) --}}
                <div class="col-lg-3 d-none d-lg-block reveal-slide-left">
                    <div class="card" style="position:sticky;top:90px;overflow:visible;">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3"><i class="bi bi-funnel me-2"></i>Filters</h5>
                            @include('products.partials.filters', ['isMobile' => false])
                        </div>
                    </div>
                </div>

                {{-- Filter Off-canvas (Mobile) --}}
                <div class="offcanvas offcanvas-end filter-offcanvas d-lg-none" tabindex="-1" id="filterOffcanvas">
                    <div class="offcanvas-header">
                        <h5 class="fw-bold mb-0"><i class="bi bi-funnel me-2"></i>Filters</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">
                        @include('products.partials.filters', ['isMobile' => true])
                    </div>
                </div>

                {{-- Product Grid --}}
                <div class="col-lg-9">
                    {{-- Toolbar: title + result count + sort --}}
                    <div class="plp-toolbar reveal-slide-right">
                        <div>
                            <h2 class="section-title mb-0">
                                @if(request('category'))
                                    {{ \App\Models\Category::where('slug', request('category'))->first()?->name ?? 'All' }}
                                    Products
                                @else
                                    All Products
                                @endif
                            </h2>
                            <p class="text-muted mb-0 small">
                                <span class="fw-bold text-primary">{{ $products->total() }}</span>
                                product{{ $products->total() === 1 ? '' : 's' }} found
                            </p>
                        </div>

                        @php
                            $sortLabels = [
                                'newest'     => 'Newest',
                                'price_low'  => 'Price: Low → High',
                                'price_high' => 'Price: High → Low',
                                'name'       => 'Name: A–Z',
                                'rating'     => 'Top Rated',
                            ];
                            $currentSort = request('sort', 'newest');
                        @endphp
                        <div class="dropdown plp-sort">
                            <button class="btn btn-sort dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-sort-down me-1"></i> {{ $sortLabels[$currentSort] ?? 'Newest' }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                @foreach($sortLabels as $key => $label)
                                    <li>
                                        <a class="dropdown-item {{ $currentSort === $key ? 'active' : '' }}"
                                           href="{{ request()->fullUrlWithQuery(['sort' => $key, 'page' => null]) }}">{{ $label }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    {{-- Active filter chips (each removes its own param) --}}
                    @php
                        $chips = [];
                        if (request('search')) {
                            $chips[] = ['label' => '“'.request('search').'”', 'remove' => request()->fullUrlWithQuery(['search' => null, 'page' => null])];
                        }
                        if (request('category')) {
                            $catName = \App\Models\Category::where('slug', request('category'))->first()?->name;
                            if ($catName) {
                                $chips[] = ['label' => $catName, 'remove' => request()->fullUrlWithQuery(['category' => null, 'page' => null])];
                            }
                        }
                        if (request('min_price') || request('max_price')) {
                            $chips[] = ['label' => '£'.(request('min_price') ?: '0').' – '.(request('max_price') ?: '∞'), 'remove' => request()->fullUrlWithQuery(['min_price' => null, 'max_price' => null, 'page' => null])];
                        }
                        if (request('rating')) {
                            $chips[] = ['label' => request('rating').'★ & up', 'remove' => request()->fullUrlWithQuery(['rating' => null, 'page' => null])];
                        }
                        if (request('in_stock')) {
                            $chips[] = ['label' => 'In stock', 'remove' => request()->fullUrlWithQuery(['in_stock' => null, 'page' => null])];
                        }
                        if (request('on_offer')) {
                            $chips[] = ['label' => 'On offer', 'remove' => request()->fullUrlWithQuery(['on_offer' => null, 'page' => null])];
                        }
                    @endphp
                    @if(count($chips))
                        <div class="plp-chips">
                            <span class="plp-chips-label"><i class="bi bi-funnel-fill"></i> Active:</span>
                            @foreach($chips as $chip)
                                <a href="{{ $chip['remove'] }}" class="plp-chip">{{ $chip['label'] }} <i class="bi bi-x-lg"></i></a>
                            @endforeach
                            <a href="{{ route('products.index') }}" class="plp-chip-clear">Clear all</a>
                        </div>
                    @endif

                    <div class="row g-4 stagger-children">
                        @forelse($products as $index => $product)
                            @include('partials.product_card', ['delay' => ($index % 6) + 1])
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

    @once
    @push('styles')
    <style>
    /* ── Product listing toolbar, chips & filter controls ── */
    .plp-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 1rem;
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }
    .btn-sort {
        background: var(--ps-card-bg, #fff);
        border: 1px solid var(--ps-border, rgba(0,0,0,.08));
        border-radius: 100px;
        font-weight: 700;
        font-size: .82rem;
        font-family: 'Outfit', sans-serif;
        color: var(--ps-text, #2D3436);
        padding: 8px 16px;
        white-space: nowrap;
    }
    .btn-sort:hover { border-color: #6C5CE7; color: #6C5CE7; }
    .plp-sort .dropdown-menu {
        border-radius: 14px;
        border: 1px solid var(--ps-border, rgba(0,0,0,.08));
        padding: 6px;
        background: var(--ps-card-bg, #fff);
    }
    .plp-sort .dropdown-item {
        border-radius: 8px;
        font-size: .85rem;
        font-weight: 600;
        padding: 7px 12px;
        color: var(--ps-text, #2D3436);
    }
    .plp-sort .dropdown-item:hover { background: rgba(108,92,231,.08); color: #6C5CE7; }
    .plp-sort .dropdown-item.active { background: #6C5CE7; color: #fff; }

    .plp-chips { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 1.5rem; align-items: center; }
    .plp-chips-label { font-size: .76rem; font-weight: 700; color: var(--ps-text-muted, #636e72); font-family: 'Outfit', sans-serif; }
    .plp-chip {
        display: inline-flex; align-items: center; gap: 6px;
        background: rgba(108,92,231,.08);
        border: 1px solid rgba(108,92,231,.18);
        color: #6C5CE7;
        border-radius: 100px;
        padding: 5px 12px;
        font-size: .78rem; font-weight: 700; font-family: 'Outfit', sans-serif;
        text-decoration: none; transition: all .2s ease;
    }
    .plp-chip:hover { background: #6C5CE7; color: #fff; }
    .plp-chip i { font-size: .64rem; }
    .plp-chip-clear { font-size: .76rem; font-weight: 700; color: var(--ps-text-muted, #636e72); text-decoration: none; padding: 5px 6px; }
    .plp-chip-clear:hover { color: #e17055; text-decoration: underline; }

    /* Rating pills + availability checks in the filter form */
    .filter-pill {
        cursor: pointer;
        border: 1.5px solid rgba(108,92,231,.2);
        border-radius: 100px;
        padding: 5px 14px;
        font-size: .8rem; font-weight: 700; font-family: 'Outfit', sans-serif;
        color: var(--ps-text-muted, #636e72);
        transition: all .2s ease; user-select: none;
        display: inline-flex; align-items: center; gap: 5px;
    }
    .filter-pill .bi-star-fill { color: #f39c12; }
    .filter-pill:hover { border-color: #6C5CE7; color: #6C5CE7; }
    .filter-pill.active,
    .filter-pill:has(input:checked) { background: #6C5CE7; border-color: #6C5CE7; color: #fff; }
    .filter-pill.active .bi-star-fill,
    .filter-pill:has(input:checked) .bi-star-fill { color: #fff; }
    .filter-check { cursor: pointer; font-size: .85rem; font-weight: 500; color: var(--ps-text, #2D3436); }
    </style>
    @endpush
    @endonce
@endsection