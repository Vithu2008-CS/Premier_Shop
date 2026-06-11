{{--
    products/partials/filters.blade.php — Product listing sidebar/drawer filter form
    ====================================================================================
    GET → products.index. Fields: search, category[], min_price, max_price, sort, rating.
    $isMobile=true renders as off-canvas drawer; false renders as sidebar.
    Preserves active filter values via request() helpers.
    Variables: $categories (all), $isMobile (bool)
--}}
<form action="{{ route('products.index') }}" method="GET" class="{{ $isMobile ? 'mobile-filters' : '' }}">
    {{-- Search --}}
    <div class="mb-3">
        <label class="form-label fw-600">Search</label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" name="search" class="form-control border-start-0"
                placeholder="Product name..." value="{{ request('search') }}">
        </div>
    </div>
    {{-- Category --}}
    <div class="mb-3">
        <label class="form-label fw-600">Category</label>
        <div class="dropdown">
            <button
                class="form-select text-start d-flex justify-content-between align-items-center rounded-3"
                type="button" data-bs-toggle="dropdown" aria-expanded="false"
                id="categoryDropdownBtn{{ $isMobile ? 'Mob' : '' }}">
                @php
                    $selectedCat = request('category') ? \App\Models\Category::where('slug', request('category'))->first() : null;
                @endphp
                <span>{{ $selectedCat ? $selectedCat->name : 'All Categories' }}</span>
            </button>
            <input type="hidden" name="category" id="categoryInput{{ $isMobile ? 'Mob' : '' }}"
                value="{{ request('category') }}">
            <ul class="dropdown-menu w-100 shadow-sm border-0"
                style="max-height: 300px; overflow-y: auto;">
                <li>
                    <a class="dropdown-item text-wrap cursor-pointer {{ !request('category') ? 'active' : '' }}"
                        href="#" data-prevent
                        data-call="filterSelect"
                        data-args="{{ json_encode(['categoryInput'.($isMobile ? 'Mob' : ''), 'categoryDropdownBtn'.($isMobile ? 'Mob' : ''), '', 'All Categories']) }}">All
                        Categories</a>
                </li>
                @foreach(\App\Models\Category::withCount('products')->get() as $cat)
                    <li>
                        <a class="dropdown-item text-wrap cursor-pointer {{ request('category') == $cat->slug ? 'active' : '' }}"
                            href="#" data-prevent
                            data-call="filterSelect"
                            data-args="{{ json_encode(['categoryInput'.($isMobile ? 'Mob' : ''), 'categoryDropdownBtn'.($isMobile ? 'Mob' : ''), $cat->slug, $cat->name]) }}">
                            {{ $cat->name }} ({{ $cat->products_count }})
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    {{-- Price Range --}}
    <div class="mb-3">
        <label class="form-label fw-600">Price Range (£)</label>
        <div class="d-flex align-items-center gap-2">
            <input type="number" name="min_price" min="0" step="0.01" inputmode="decimal"
                class="form-control rounded-3"
                placeholder="{{ $priceBounds && $priceBounds->min_price !== null ? floor($priceBounds->min_price) : 'Min' }}"
                value="{{ request('min_price') }}" aria-label="Minimum price">
            <span class="text-muted">—</span>
            <input type="number" name="max_price" min="0" step="0.01" inputmode="decimal"
                class="form-control rounded-3"
                placeholder="{{ $priceBounds && $priceBounds->max_price !== null ? ceil($priceBounds->max_price) : 'Max' }}"
                value="{{ request('max_price') }}" aria-label="Maximum price">
        </div>
    </div>

    {{-- Minimum Rating --}}
    <div class="mb-3">
        <label class="form-label fw-600">Minimum Rating</label>
        <div class="d-flex flex-wrap gap-2">
            @foreach([0 => 'Any', 4 => '4', 3 => '3', 2 => '2'] as $val => $lbl)
                <label class="filter-pill {{ (string) request('rating', '0') === (string) $val ? 'active' : '' }}">
                    <input type="radio" name="rating" value="{{ $val ?: '' }}" class="d-none"
                        {{ (string) request('rating', '0') === (string) $val ? 'checked' : '' }}>
                    @if($val)<i class="bi bi-star-fill"></i> {{ $lbl }}+@else{{ $lbl }}@endif
                </label>
            @endforeach
        </div>
    </div>

    {{-- Availability & Offers --}}
    <div class="mb-4">
        <label class="form-label fw-600">Availability</label>
        <label class="filter-check d-flex align-items-center gap-2 mb-2">
            <input type="checkbox" name="in_stock" value="1" class="form-check-input m-0"
                {{ request('in_stock') ? 'checked' : '' }}>
            <span>In stock only</span>
        </label>
        <label class="filter-check d-flex align-items-center gap-2">
            <input type="checkbox" name="on_offer" value="1" class="form-check-input m-0"
                {{ request('on_offer') ? 'checked' : '' }}>
            <span>On offer <i class="bi bi-tag-fill text-danger"></i></span>
        </label>
    </div>

    {{-- Sort --}}
    <div class="mb-4">
        <label class="form-label fw-600">Sort By</label>
        <div class="dropdown">
            <button
                class="form-select text-start d-flex justify-content-between align-items-center rounded-3"
                type="button" data-bs-toggle="dropdown" aria-expanded="false"
                id="sortDropdownBtn{{ $isMobile ? 'Mob' : '' }}">
                @php
                    $sort = request('sort', 'newest');
                    $sortText = 'Newest First';
                    if ($sort == 'price_low')
                        $sortText = 'Price: Low → High';
                    if ($sort == 'price_high')
                        $sortText = 'Price: High → Low';
                    if ($sort == 'name')
                        $sortText = 'Name: A-Z';
                    if ($sort == 'rating')
                        $sortText = 'Top Rated';
                @endphp
                <span>{{ $sortText }}</span>
            </button>
            <input type="hidden" name="sort" id="sortInput{{ $isMobile ? 'Mob' : '' }}" value="{{ $sort }}">
            <ul class="dropdown-menu w-100 shadow-sm border-0">
                <li><a class="dropdown-item cursor-pointer {{ $sort == 'newest' ? 'active' : '' }}"
                        href="#" data-prevent
                        data-call="filterSelect"
                        data-args="{{ json_encode(['sortInput'.($isMobile ? 'Mob' : ''), 'sortDropdownBtn'.($isMobile ? 'Mob' : ''), 'newest', 'Newest First']) }}">Newest
                        First</a></li>
                <li><a class="dropdown-item cursor-pointer {{ $sort == 'price_low' ? 'active' : '' }}"
                        href="#" data-prevent
                        data-call="filterSelect"
                        data-args="{{ json_encode(['sortInput'.($isMobile ? 'Mob' : ''), 'sortDropdownBtn'.($isMobile ? 'Mob' : ''), 'price_low', 'Price: Low → High']) }}">Price:
                        Low → High</a></li>
                <li><a class="dropdown-item cursor-pointer {{ $sort == 'price_high' ? 'active' : '' }}"
                        href="#" data-prevent
                        data-call="filterSelect"
                        data-args="{{ json_encode(['sortInput'.($isMobile ? 'Mob' : ''), 'sortDropdownBtn'.($isMobile ? 'Mob' : ''), 'price_high', 'Price: High → Low']) }}">Price:
                        High → Low</a></li>
                <li><a class="dropdown-item cursor-pointer {{ $sort == 'name' ? 'active' : '' }}"
                        href="#" data-prevent
                        data-call="filterSelect"
                        data-args="{{ json_encode(['sortInput'.($isMobile ? 'Mob' : ''), 'sortDropdownBtn'.($isMobile ? 'Mob' : ''), 'name', 'Name: A-Z']) }}">Name:
                        A-Z</a></li>
                <li><a class="dropdown-item cursor-pointer {{ $sort == 'rating' ? 'active' : '' }}"
                        href="#" data-prevent
                        data-call="filterSelect"
                        data-args="{{ json_encode(['sortInput'.($isMobile ? 'Mob' : ''), 'sortDropdownBtn'.($isMobile ? 'Mob' : ''), 'rating', 'Top Rated']) }}">Top
                        Rated</a></li>
            </ul>
        </div>
    </div>
    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-primary py-2 fw-bold"><i class="bi bi-filter me-1"></i> Apply
            Filters</button>
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill">Clear
            All</a>
    </div>
</form>

<script nonce="{{ Vite::cspNonce() }}">
    // CSP shim target for the category/sort dropdowns above.
    window.filterSelect = window.filterSelect || function (inputId, btnId, value, label) {
        document.getElementById(inputId).value = value;
        document.getElementById(btnId).querySelector('span').innerText = label;
    };
</script>
