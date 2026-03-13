@extends('layouts.admin')
@section('title', 'Sales Report — Admin')

@push('styles')
<style>
    @media print {
        body {
            background: #fff !important;
            color: #000 !important;
        }
        .admin-sidebar, .admin-topbar form, .btn-admin, .d-lg-none, .pagination-container, .alert {
            display: none !important;
        }
        .admin-content {
            margin: 0 !important;
            padding: 0 !important;
        }
        .admin-card {
            border: none !important;
            background: #fff !important;
            box-shadow: none !important;
        }
        .admin-table th, .admin-table td {
            color: #000 !important;
            border-color: #ddd !important;
        }
        .admin-table th {
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
        /* Ensure table text is dark in print */
        td .fw-bold { 
            color: #000 !important; 
        }
    }
    .print-header {
        display: none;
    }
</style>
@endpush

@section('content')
<div class="admin-topbar d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <h2>Sales Report</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item active">Reports</li>
            </ol>
        </nav>
    </div>
    
    <form method="GET" action="{{ route('admin.reports.index') }}" class="d-flex gap-2 align-items-end shadow-sm p-3 rounded" style="background: var(--admin-card); border: 1px solid var(--admin-border);">
        <div>
            <label class="form-label mb-1" style="font-size: 0.75rem;">Category</label>
            <select name="category" class="form-select form-select-sm" style="min-width: 150px;">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label mb-1" style="font-size: 0.75rem;">Sort By Sold</label>
            <select name="sort" class="form-select form-select-sm" style="min-width: 140px;">
                <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Highest to Lowest</option>
                <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Lowest to Highest</option>
            </select>
        </div>
        <button type="submit" class="btn btn-admin btn-sm h-100 px-3">Filter</button>
        <a href="{{ route('admin.reports.print', request()->all()) }}" class="btn btn-admin-outline btn-sm h-100 px-3 ms-2">
            <i class="bi bi-file-earmark-pdf me-1"></i> Download PDF
        </a>
    </form>
</div>

<div class="print-header">
    <h2>Sales Report</h2>
    <p>Generated on {{ now()->format('M d, Y H:i') }}</p>
</div>

<div class="admin-card">
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Total Sold</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="d-none d-sm-block" style="width:40px;height:40px;border-radius:8px;overflow:hidden;background:rgba(255,255,255,0.05);flex-shrink:0;">
                                    @if($product->images && count($product->images) > 0)
                                        <img src="{{ $product->images[0] }}" style="width:100%;height:100%;object-fit:cover;" alt="">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center h-100"><i class="bi bi-image" style="color:var(--admin-muted);"></i></div>
                                    @endif
                                </div>
                                <div>
                                    <div class="fw-bold" style="color:#fff;">{{ Str::limit($product->name, 40) }}</div>
                                    @if($product->barcode)
                                        <small style="color:var(--admin-muted);">{{ $product->barcode }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($product->category)
                                <span class="badge" style="background:rgba(108,92,231,0.15);color:#A29BFE;">{{ $product->category->name }}</span>
                            @else
                                <span style="color:var(--admin-muted);">—</span>
                            @endif
                        </td>
                        <td class="fw-bold">£{{ number_format($product->price, 2) }}</td>
                        <td>
                            @if($product->stock < 10)
                                <span class="badge badge-status bg-danger">{{ $product->stock }}</span>
                            @elseif($product->stock < 50)
                                <span class="badge badge-status bg-warning text-dark">{{ $product->stock }}</span>
                            @else
                                <span class="badge badge-status bg-success">{{ $product->stock }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="fw-bold" style="font-size: 1.1em; color: {{ $product->total_sold > 0 ? '#00CEC9' : 'var(--admin-muted)' }};">
                                {{ number_format($product->total_sold ?? 0) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5" style="color:var(--admin-muted);">
                            <i class="bi bi-inbox" style="font-size:2.5rem;display:block;margin-bottom:10px;"></i>
                            No products matched your criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if(method_exists($products, 'links'))
<div class="mt-3 pagination-container">
    {{ $products->links('pagination::bootstrap-5') }}
</div>
@endif

@endsection
