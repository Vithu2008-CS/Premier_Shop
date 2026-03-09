@extends('layouts.admin')
@section('title', 'Products — Admin')

@section('content')
    <div class="admin-topbar">
        <div>
            <h2>Products</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item active">Products</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.products.create') }}" class="btn btn-admin">
            <i class="bi bi-plus-lg me-1"></i> Add Product
        </a>
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
                        <th>Type</th>
                        <th>Offer</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div
                                        style="width:44px;height:44px;border-radius:10px;overflow:hidden;background:rgba(255,255,255,0.05);flex-shrink:0;">
                                        @if($product->images && count($product->images) > 0)
                                            <img src="{{ $product->images[0] }}" style="width:100%;height:100%;object-fit:cover;"
                                                alt="">
                                        @else
                                            <div class="d-flex align-items-center justify-content-center h-100"><i
                                                    class="bi bi-image" style="color:var(--admin-muted);"></i></div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-bold" style="color:#fff;">{{ Str::limit($product->name, 30) }}</div>
                                        @if($product->barcode)
                                            <small style="color:var(--admin-muted);">{{ $product->barcode }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($product->category)
                                    <span class="badge"
                                        style="background:rgba(108,92,231,0.15);color:#A29BFE;">{{ $product->category->name }}</span>
                                @else
                                    <span style="color:var(--admin-muted);">—</span>
                                @endif
                            </td>
                            <td class="fw-bold">£{{ number_format($product->price, 2) }}</td>
                            <td>
                                @if($product->stock < 10)
                                    <span class="badge badge-status bg-danger">{{ $product->stock }} <i
                                            class="bi bi-exclamation-triangle-fill ms-1"></i></span>
                                @elseif($product->stock < 50)
                                    <span class="badge badge-status bg-warning text-dark">{{ $product->stock }}</span>
                                @else
                                    <span class="badge badge-status bg-success">{{ $product->stock }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-status"
                                    style="background:rgba({{ $product->product_type === 'wholesale' ? '0,206,201' : '255,255,255' }},0.1);color:{{ $product->product_type === 'wholesale' ? '#00CEC9' : 'var(--admin-muted)' }};">
                                    {{ ucfirst($product->product_type) }}
                                </span>
                            </td>
                            <td>
                                @if($product->has_offer)
                                    <span class="badge badge-status" style="background:rgba(225,112,85,0.15);color:#E17055;">
                                        {{ number_format($product->offer_discount_percent) }}% off ({{ $product->offer_min_qty }}+)
                                    </span>
                                @else
                                    <span style="color:var(--admin-muted);">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.products.edit', $product) }}" class="btn-icon" title="Edit"><i
                                            class="bi bi-pencil"></i></a>
                                    @if($product->qr_code)
                                        <a href="{{ $product->qr_code }}" download class="btn-icon" title="Download QR"><i
                                                class="bi bi-qr-code"></i></a>
                                    @endif
                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Delete this product?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-icon btn-icon-danger" title="Delete"><i
                                                class="bi bi-trash3"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5" style="color:var(--admin-muted);">
                                <i class="bi bi-box-seam" style="font-size:2.5rem;display:block;margin-bottom:10px;"></i>
                                No products yet. <a href="{{ route('admin.products.create') }}" style="color:#A29BFE;">Add your
                                    first product</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $products->links('pagination::bootstrap-5') }}</div>
@endsection