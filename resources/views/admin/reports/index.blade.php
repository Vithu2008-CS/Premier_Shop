@extends('layouts.admin_noble')
@section('title', 'Sales Report')

@push('styles')
<style>
    @media print {
        body {
            background: #fff !important;
            color: #000 !important;
        }
        .sidebar, .navbar, .btn, .pagination-container, .alert, .page-breadcrumb, form {
            display: none !important;
        }
        .page-content {
            margin: 0 !important;
            padding: 0 !important;
        }
        .card {
            border: none !important;
            background: #fff !important;
            box-shadow: none !important;
        }
        .table th, .table td {
            color: #000 !important;
            border-color: #ddd !important;
        }
        .table th {
            background: #f8f9fa !important;
            -webkit-print-color-adjust: exact;
        }
        .badge {
            border: 1px solid #ccc !important;
            color: #000 !important;
            background: transparent !important;
        }
        .print-header {
            display: block !important;
            text-align: center;
            margin-bottom: 20px;
        }
        .print-header h2 {
            margin: 0;
            color: #000;
        }
        .print-header p {
            margin: 0;
            color: #555;
        }
        td .font-weight-bold { 
            color: #000 !important; 
        }
    }
    .print-header {
        display: none;
    }
</style>
@endpush

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item active" aria-current="page">Sales Reports</li>
  </ol>
</nav>

<div class="print-header">
    <h2>Sales Report</h2>
    <p>Generated on {{ now()->format('M d, Y H:i') }}</p>
</div>

<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
          <h6 class="card-title mb-0">Sales Performance</h6>
          <div class="d-flex align-items-center flex-wrap text-nowrap">
            <a href="{{ route('admin.reports.print', request()->all()) }}" class="btn btn-outline-primary btn-icon-text me-2 mb-2 mb-md-0">
              <i class="btn-icon-prepend" data-feather="download-cloud"></i>
              Download PDF
            </a>
          </div>
        </div>

        <form method="GET" action="{{ route('admin.reports.index') }}" class="mb-4">
          <div class="row align-items-end">
            <div class="col-md-3 mb-3">
              <label class="form-label">Category</label>
              <select name="category" class="form-control">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                  <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Sort By</label>
              <select name="sort_by" class="form-control">
                <option value="sold" {{ request('sort_by') == 'sold' ? 'selected' : '' }}>Sold</option>
                <option value="price" {{ request('sort_by') == 'price' ? 'selected' : '' }}>Price</option>
                <option value="stock" {{ request('sort_by') == 'stock' ? 'selected' : '' }}>Stock</option>
                <option value="wishlist" {{ request('sort_by') == 'wishlist' ? 'selected' : '' }}>Wishlist</option>
              </select>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Order</label>
              <select name="order" class="form-control">
                <option value="desc" {{ request('order') == 'desc' || !request('order') ? 'selected' : '' }}>Highest to Lowest</option>
                <option value="asc" {{ request('order') == 'asc' ? 'selected' : '' }}>Lowest to Highest</option>
              </select>
            </div>
            <div class="col-md-3 mb-3">
              <button type="submit" class="btn btn-primary btn-block">
                <i data-feather="filter" class="icon-sm mr-2"></i> Apply Filters
              </button>
            </div>
          </div>
        </form>

        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Product</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Wishlist</th>
                <th>Total Sold</th>
              </tr>
            </thead>
            <tbody>
              @forelse($products as $product)
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="mr-3">
                        @if($product->images && count($product->images) > 0)
                          <img src="{{ $product->images[0] }}" class="wd-40 ht-40 rounded-circle" alt="">
                        @else
                          <div class="wd-40 ht-40 rounded-circle bg-soft-primary d-flex align-items-center justify-content-center">
                            <i data-feather="image" class="icon-sm text-primary"></i>
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
                      <span class="badge badge-soft-primary">{{ $product->category->name }}</span>
                    @else
                      <span class="text-muted small">N/A</span>
                    @endif
                  </td>
                  <td class="font-weight-bold">£{{ number_format($product->price, 2) }}</td>
                  <td>
                    @if($product->stock < 10)
                      <span class="badge badge-danger">{{ $product->stock }}</span>
                    @elseif($product->stock < 50)
                      <span class="badge badge-warning">{{ $product->stock }}</span>
                    @else
                      <span class="badge badge-success">{{ $product->stock }}</span>
                    @endif
                  </td>
                  <td>
                    <span class="text-info font-weight-bold">
                      <i data-feather="heart" class="icon-xs mr-1"></i>{{ number_format($product->total_wishlist ?? 0) }}
                    </span>
                  </td>
                  <td>
                    <span class="font-weight-bold tx-14 {{ $product->total_sold > 0 ? 'text-success' : 'text-muted' }}">
                      {{ number_format($product->total_sold ?? 0) }}
                    </span>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center py-5">
                    <i data-feather="inbox" class="icon-xxl text-muted mb-3 d-block mx-auto"></i>
                    <p class="text-muted">No products matched your criteria.</p>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        @if(method_exists($products, 'links'))
        <div class="mt-4 pagination-container">
            {{ $products->links() }}
        </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

