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
                <form action="{{ route('admin.products.index') }}" method="GET" class="mr-2 mb-2 mb-md-0">
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control font-weight-medium" placeholder="Search products..." value="{{ request('search') }}" style="width: 220px; border-radius: 20px 0 0 20px;">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit" style="border-radius: 0 20px 20px 0;">
                                <i data-feather="search" class="icon-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>
                @if(request()->filled('search'))
                    <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-secondary mr-2 mb-2 mb-md-0" style="border-radius: 20px;">
                        Clear
                    </a>
                @endif
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-icon-text mb-2 mb-md-0">
                    <i class="btn-icon-prepend" data-feather="plus-square"></i>
                    Add Product
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
                                <img src="{{ $product->images[0] }}" class="wd-40 h-40 rounded" style="object-fit: cover;" alt="product">
                            @else
                                <div class="wd-40 h-40 rounded bg-light d-flex align-items-center justify-content-center">
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
                        <a href="{{ route('admin.products.create') }}" class="btn btn-primary mt-3">Add First Product</a>
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
@endsection