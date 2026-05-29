{{--
    admin/products/index.blade.php — Product list management
    =========================================================
    Searchable, filterable table of all products.
    Columns: image, name, category, price, stock (colour-coded), status, actions.
    Actions: edit, toggle active, delete, regenerate QR.
    Quick stock update inline input.
    Link to QR scanner page.
    Variable: $products (paginated with category)
--}}
@extends('layouts.admin_noble')
@section('title', 'Products')

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item active" aria-current="page">Products</li>
  </ol>
</nav>

<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
            <h6 class="card-title mb-md-0 mb-3">Product Management</h6>
            <div class="d-flex align-items-center flex-wrap">
                <form action="{{ route('admin.products.index') }}" method="GET" class="mr-2 mb-2 mb-md-0 position-relative" style="width: 280px;" id="adminSearchForm">
                    <div class="input-group">
                        <input type="text" id="adminSearchInput" name="search" autocomplete="off" class="form-control font-weight-medium" placeholder="Search products..." value="{{ request('search') }}" style="height: 38px; border-radius: 20px 0 0 20px; font-size: 0.875rem;">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit" style="height: 38px; border-radius: 0 20px 20px 0; padding: 0 16px; display: flex; align-items: center; justify-content: center;">
                                <i data-feather="search" class="icon-sm"></i>
                            </button>
                        </div>
                    </div>
                    <div id="adminSearchSuggestions" class="dropdown-menu shadow-lg w-100 p-0" style="position: absolute; top: 100%; left: 0; display: none; max-height: 350px; overflow-y: auto; z-index: 1050; border-radius: 10px; margin-top: 5px;"></div>
                </form>
                @if(request()->filled('search'))
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center rounded-pill mr-2 mb-2 mb-md-0" style="height: 38px; font-size: 0.875rem; padding: 0 16px;">
                        <span>Clear</span>
                    </a>
                @endif
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary d-inline-flex align-items-center justify-content-center rounded-pill mb-2 mb-md-0" style="height: 38px; font-size: 0.875rem; padding: 0 16px;">
                    <i data-feather="plus-square" style="width: 16px; height: 16px; margin-right: 6px;"></i>
                    <span>Add Product</span>
                </a>
            </div>
        </div>
        
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Product</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Type</th>
                <th>Offer</th>
                <th class="text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($products as $product)
                <tr class="product-row-btn align-middle" onclick="window.location='{{ route('admin.products.edit', $product) }}'">
                  <td>
                    <div class="d-flex align-items-center">
                        <div class="mr-3">
                            @if($product->images && count($product->images) > 0)
                                <img src="{{ $product->images[0] }}" class="wd-40 ht-40 rounded" style="object-fit: cover;" alt="product">
                            @else
                                <div class="wd-40 ht-40 rounded bg-light d-flex align-items-center justify-content-center">
                                    <i data-feather="image" class="text-muted icon-sm"></i>
                                </div>
                            @endif
                        </div>
                        <div>
                            <span class="font-weight-bold d-block text-primary-hover mb-1" style="font-size: 0.95rem; transition: color 0.15s ease;">{{ Str::limit($product->name, 40) }}</span>
                            @if($product->barcode)
                                <small class="text-muted">{{ $product->barcode }}</small>
                            @endif
                        </div>
                    </div>
                  </td>
                  <td>
                    @if($product->category)
                        <span class="badge badge-light-primary">{{ $product->category->name }}</span>
                    @else
                        <span class="text-muted small">Not assigned</span>
                    @endif
                  </td>
                  <td class="font-weight-bold">£{{ number_format($product->price, 2) }}</td>
                  <td>
                    @if($product->stock < 10)
                        <span class="badge badge-danger">{{ $product->stock }} <i data-feather="alert-triangle" class="icon-xs ml-1"></i></span>
                    @elseif($product->stock < 50)
                        <span class="badge badge-warning">{{ $product->stock }}</span>
                    @else
                        <span class="badge badge-success">{{ $product->stock }}</span>
                    @endif
                  </td>
                  <td>
                    <span class="badge badge-outline-{{ $product->product_type === 'wholesale' ? 'info' : 'secondary' }}">
                        {{ ucfirst($product->product_type) }}
                    </span>
                  </td>
                  <td>
                    @if($product->has_offer)
                        <span class="badge badge-light-warning">
                            {{ number_format($product->offer_discount_percent) }}% OFF
                        </span>
                    @else
                        <span class="text-muted small">—</span>
                    @endif
                  </td>
                  <td class="text-right" onclick="event.stopPropagation();">
                      <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Truly delete this product?');" class="d-inline-block">
                          @csrf @method('DELETE')
                          <button type="submit" class="btn btn-xs btn-outline-danger rounded-pill px-3 py-1.5 font-weight-bold" style="transition: all 0.2s ease;">
                              <i data-feather="trash-2" class="wd-10 h-10 mr-1" style="width: 12px; height: 12px; vertical-align: -1px;"></i> Delete
                          </button>
                      </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center py-5">
                    <div class="d-flex flex-column align-items-center">
                        <i data-feather="package" class="icon-xxl text-muted mb-3"></i>
                        <p class="text-muted">No products found in the database.</p>
                        <a href="{{ route('admin.products.create') }}" class="btn btn-primary rounded-pill px-4 mt-3">Add First Product</a>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-4">
            {{ $products->links('pagination::bootstrap-4') }}
        </div>
      </div>
    </div>
  </div>
</div>

<style>
/* Custom Premium Sibling Row buttons styling */
.product-row-btn {
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.product-row-btn:hover {
    background-color: rgba(108, 92, 231, 0.04) !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
}
html[data-admin-theme="dark"] .product-row-btn:hover {
    background-color: rgba(167, 139, 250, 0.05) !important;
}
.product-row-btn:hover .text-primary-hover {
    color: #6c5ce7 !important;
}
html[data-admin-theme="dark"] .product-row-btn:hover .text-primary-hover {
    color: #a78bfa !important;
}
</style>

@push('scripts')
<script>
$(document).ready(function() {
    const $searchInput = $('#adminSearchInput');
    const $suggestions = $('#adminSearchSuggestions');
    let debounceTimer;
    let activeIndex = -1;

    $searchInput.on('input', function() {
        clearTimeout(debounceTimer);
        const query = $(this).val().trim();

        if (query.length < 2) {
            $suggestions.empty().hide();
            activeIndex = -1;
            return;
        }

        debounceTimer = setTimeout(function() {
            // Show searching spinner
            $suggestions.html('<div class="p-3 text-center text-muted small"><div class="spinner-border spinner-border-sm text-primary mr-2" role="status"></div>Searching...</div>').show();
            activeIndex = -1;

            $.ajax({
                url: '{{ route("admin.products.suggest") }}',
                method: 'GET',
                data: { q: query },
                success: function(data) {
                    if (data.length === 0) {
                        $suggestions.html('<div class="p-3 text-center text-muted small">No products found for "' + query + '"</div>');
                        return;
                    }

                    let html = '';
                    data.forEach(function(item) {
                        html += `
                            <a href="${item.url}" class="dropdown-item d-flex align-items-center py-2 border-bottom" style="border-color: rgba(0,0,0,0.05) !important; white-space: normal;">
                                <div class="mr-3 flex-shrink-0">
                                    <img src="${item.image}" class="rounded" style="width: 35px; height: 35px; object-fit: cover;" onerror="this.src='/images/placeholder-product.png'">
                                </div>
                                <div class="flex-grow-1 min-width-0">
                                    <div class="text-truncate font-weight-bold mb-0" style="font-size: 0.85rem; line-height: 1.2;">${item.name}</div>
                                    <div class="text-muted small d-flex justify-content-between mt-1">
                                        <span>${item.category || 'No Category'}</span>
                                        <span>
                                            <span class="mr-2 font-weight-medium text-primary">${item.price}</span>
                                            <span class="badge ${item.stock < 10 ? 'badge-danger' : (item.stock < 50 ? 'badge-warning' : 'badge-success')}">${item.stock}</span>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        `;
                    });
                    $suggestions.html(html);
                },
                error: function() {
                    $suggestions.html('<div class="p-3 text-center text-danger small">Failed to load suggestions.</div>');
                }
            });
        }, 250);
    });

    // Keyboard navigation
    $searchInput.on('keydown', function(e) {
        const $items = $suggestions.find('.dropdown-item');
        if (!$items.length || !$suggestions.is(':visible')) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            activeIndex++;
            if (activeIndex >= $items.length) activeIndex = 0;
            highlightItem($items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            activeIndex--;
            if (activeIndex < 0) activeIndex = $items.length - 1;
            highlightItem($items);
        } else if (e.key === 'Enter') {
            if (activeIndex >= 0) {
                e.preventDefault();
                const targetUrl = $items.eq(activeIndex).attr('href');
                if (targetUrl) window.location.href = targetUrl;
            }
        } else if (e.key === 'Escape') {
            $suggestions.hide();
        }
    });

    function highlightItem($items) {
        $items.removeClass('active bg-light text-primary');
        if (activeIndex >= 0) {
            const $activeItem = $items.eq(activeIndex);
            $activeItem.addClass('active bg-light text-primary');
            
            // Scroll container if item goes out of view
            const containerTop = $suggestions.scrollTop();
            const containerBottom = containerTop + $suggestions.height();
            const elemTop = $activeItem.position().top + containerTop;
            const elemBottom = elemTop + $activeItem.outerHeight();

            if (elemBottom > containerBottom) {
                $suggestions.scrollTop(elemBottom - $suggestions.height());
            } else if (elemTop < containerTop) {
                $suggestions.scrollTop(elemTop);
            }
        }
    }

    // Close suggestions when clicking outside
    $(document).on('click', function(event) {
        if (!$(event.target).closest('#adminSearchForm').length) {
            $suggestions.hide();
        }
    });

    // Re-show suggestions when input is focused if it has content
    $searchInput.on('focus', function() {
        if ($(this).val().trim().length >= 2) {
            $suggestions.show();
        }
    });
});
</script>
@endpush
@endsection