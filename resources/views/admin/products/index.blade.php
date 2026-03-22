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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h6 class="card-title mb-0">Product Management</h6>
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-icon-text">
                <i class="btn-icon-prepend" data-feather="plus-square"></i>
                Add Product
            </a>
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
                <tr>
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
                            <span class="font-weight-bold d-block">{{ Str::limit($product->name, 40) }}</span>
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
                  <td class="text-right">
                    <div class="dropdown">
                        <button class="btn btn-link p-0" type="button" id="dropdownMenuButton{{ $product->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <i class="icon-lg text-muted pb-3px" data-feather="more-horizontal"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton{{ $product->id }}">
                          <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.products.edit', $product) }}">
                              <i data-feather="edit-2" class="icon-sm mr-2"></i> Edit
                          </a>
                          @if($product->qr_code)
                            <a class="dropdown-item d-flex align-items-center" href="{{ $product->qr_code }}" download>
                                <i data-feather="maximize" class="icon-sm mr-2"></i> Download QR
                            </a>
                          @endif
                          <div class="dropdown-divider"></div>
                          <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Truly delete this product?')">
                              @csrf @method('DELETE')
                              <button type="submit" class="dropdown-item d-flex align-items-center text-danger">
                                  <i data-feather="trash-2" class="icon-sm mr-2"></i> Delete
                              </button>
                          </form>
                        </div>
                    </div>
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
@endsection