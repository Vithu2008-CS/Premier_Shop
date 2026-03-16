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
                        href="javascript:void(0)"
                        onclick="document.getElementById('categoryInput{{ $isMobile ? 'Mob' : '' }}').value=''; document.getElementById('categoryDropdownBtn{{ $isMobile ? 'Mob' : '' }}').querySelector('span').innerText='All Categories';">All
                        Categories</a>
                </li>
                @foreach(\App\Models\Category::withCount('products')->get() as $cat)
                    <li>
                        <a class="dropdown-item text-wrap cursor-pointer {{ request('category') == $cat->slug ? 'active' : '' }}"
                            href="javascript:void(0)"
                            onclick="document.getElementById('categoryInput{{ $isMobile ? 'Mob' : '' }}').value='{{ $cat->slug }}'; document.getElementById('categoryDropdownBtn{{ $isMobile ? 'Mob' : '' }}').querySelector('span').innerText='{{ addslashes($cat->name) }}';">
                            {{ $cat->name }} ({{ $cat->products_count }})
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
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
                @endphp
                <span>{{ $sortText }}</span>
            </button>
            <input type="hidden" name="sort" id="sortInput{{ $isMobile ? 'Mob' : '' }}" value="{{ $sort }}">
            <ul class="dropdown-menu w-100 shadow-sm border-0">
                <li><a class="dropdown-item cursor-pointer {{ $sort == 'newest' ? 'active' : '' }}"
                        href="javascript:void(0)"
                        onclick="document.getElementById('sortInput{{ $isMobile ? 'Mob' : '' }}').value='newest'; document.getElementById('sortDropdownBtn{{ $isMobile ? 'Mob' : '' }}').querySelector('span').innerText='Newest First';">Newest
                        First</a></li>
                <li><a class="dropdown-item cursor-pointer {{ $sort == 'price_low' ? 'active' : '' }}"
                        href="javascript:void(0)"
                        onclick="document.getElementById('sortInput{{ $isMobile ? 'Mob' : '' }}').value='price_low'; document.getElementById('sortDropdownBtn{{ $isMobile ? 'Mob' : '' }}').querySelector('span').innerText='Price: Low → High';">Price:
                        Low → High</a></li>
                <li><a class="dropdown-item cursor-pointer {{ $sort == 'price_high' ? 'active' : '' }}"
                        href="javascript:void(0)"
                        onclick="document.getElementById('sortInput{{ $isMobile ? 'Mob' : '' }}').value='price_high'; document.getElementById('sortDropdownBtn{{ $isMobile ? 'Mob' : '' }}').querySelector('span').innerText='Price: High → Low';">Price:
                        High → Low</a></li>
                <li><a class="dropdown-item cursor-pointer {{ $sort == 'name' ? 'active' : '' }}"
                        href="javascript:void(0)"
                        onclick="document.getElementById('sortInput{{ $isMobile ? 'Mob' : '' }}').value='name'; document.getElementById('sortDropdownBtn{{ $isMobile ? 'Mob' : '' }}').querySelector('span').innerText='Name: A-Z';">Name:
                        A-Z</a></li>
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
