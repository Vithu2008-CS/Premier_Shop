@extends('layouts.admin')
@section('title', 'Edit Product — Admin')

@section('content')
<div class="admin-topbar">
    <div>
        <h2>Edit Product</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
                <li class="breadcrumb-item active">{{ Str::limit($product->name, 20) }}</li>
            </ol>
        </nav>
    </div>
</div>

<form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="row g-4">
        <div class="col-lg-8">
            {{-- Basic Details --}}
            <div class="admin-card mb-4">
                <div class="card-title"><i class="bi bi-info-circle me-2"></i>Product Details</div>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Product Name *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Barcode</label>
                        <input type="text" name="barcode" class="form-control" value="{{ old('barcode', $product->barcode) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="4">{{ old('description', $product->description) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Pricing --}}
            <div class="admin-card mb-4">
                <div class="card-title"><i class="bi bi-currency-pound me-2"></i>Pricing & Inventory</div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Price (£) *</label>
                        <input type="number" name="price" class="form-control" value="{{ old('price', $product->price) }}" step="0.01" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Wholesale Price (£)</label>
                        <input type="number" name="wholesale_price" class="form-control" value="{{ old('wholesale_price', $product->wholesale_price) }}" step="0.01">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Stock Quantity *</label>
                        <input type="number" name="stock" class="form-control" value="{{ old('stock', $product->stock) }}" min="0" required>
                    </div>
                </div>
            </div>

            {{-- Offer --}}
            <div class="admin-card mb-4" style="border-color:rgba(225,112,85,0.2);">
                <div class="card-title"><i class="bi bi-lightning-fill text-warning me-2"></i>Bulk Offer Configuration</div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Min Quantity for Offer</label>
                        <input type="number" name="offer_min_qty" class="form-control" value="{{ old('offer_min_qty', $product->offer_min_qty) }}" min="1" placeholder="e.g. 10">
                        <small style="color:var(--admin-muted);">Customers must buy this many to get discount</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Discount Percentage (%)</label>
                        <input type="number" name="offer_discount_percent" class="form-control" value="{{ old('offer_discount_percent', $product->offer_discount_percent) }}" min="0" max="100" step="0.01" placeholder="e.g. 15">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="offer_active" id="offerActive" value="1" {{ old('offer_active', $product->offer_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="offerActive">Activate Offer</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Classification --}}
            <div class="admin-card mb-4">
                <div class="card-title"><i class="bi bi-tags me-2"></i>Classification</div>
                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Product Type</label>
                    <select name="product_type" class="form-select">
                        <option value="normal" {{ $product->product_type == 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="wholesale" {{ $product->product_type == 'wholesale' ? 'selected' : '' }}>Wholesale</option>
                    </select>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_age_restricted" id="ageRestricted" value="1" {{ $product->is_age_restricted ? 'checked' : '' }}>
                    <label class="form-check-label" for="ageRestricted">🔞 Age Restricted (16+)</label>
                </div>
            </div>

            {{-- QR Code --}}
            <div class="admin-card mb-4 text-center">
                <div class="card-title"><i class="bi bi-qr-code me-2"></i>QR Code</div>
                @if($product->qr_code)
                    <img src="{{ $product->qr_code }}" alt="QR Code" class="img-fluid mb-3" style="max-width:180px;border-radius:12px;background:#fff;padding:10px;">
                    <div class="d-grid gap-2">
                        <a href="{{ $product->qr_code }}" download class="btn btn-admin btn-sm"><i class="bi bi-download me-1"></i>Download QR</a>
                        <form action="{{ route('admin.products.regenerateQr', $product) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-admin-outline btn-sm w-100"><i class="bi bi-arrow-clockwise me-1"></i>Regenerate</button>
                        </form>
                    </div>
                @else
                    <div class="py-3" style="color:var(--admin-muted);">
                        <i class="bi bi-qr-code" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                        No QR code generated yet
                    </div>
                    <form action="{{ route('admin.products.regenerateQr', $product) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-admin btn-sm"><i class="bi bi-qr-code me-1"></i>Generate QR</button>
                    </form>
                @endif
            </div>

            {{-- Current Images --}}
            @if($product->images && count($product->images) > 0)
                <div class="admin-card mb-4">
                    <div class="card-title"><i class="bi bi-images me-2"></i>Current Images</div>
                    <div class="row g-2">
                        @foreach($product->images as $img)
                            <div class="col-6">
                                <img src="{{ $img }}" class="w-100" style="height:90px;object-fit:cover;border-radius:10px;" alt="">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Upload Images --}}
            <div class="admin-card mb-4">
                <div class="card-title"><i class="bi bi-cloud-upload me-2"></i>Add More Images</div>
                <input type="file" name="product_images[]" class="form-control" multiple accept="image/*">
            </div>

            {{-- Actions --}}
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-admin btn-lg"><i class="bi bi-save me-2"></i>Update Product</button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-admin-outline">Cancel</a>
            </div>
        </div>
    </div>
</form>
@endsection
