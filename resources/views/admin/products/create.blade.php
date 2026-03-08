@extends('layouts.admin')
@section('title', 'Add Product — Admin')

@section('content')
<div class="admin-topbar">
    <div>
        <h2>Add New Product</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
                <li class="breadcrumb-item active">Add New</li>
            </ol>
        </nav>
    </div>
</div>

<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row g-4">
        <div class="col-lg-8">
            {{-- Basic Details --}}
            <div class="admin-card mb-4">
                <div class="card-title"><i class="bi bi-info-circle me-2"></i>Product Details</div>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Product Name *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="Enter product name">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Barcode</label>
                        <input type="text" name="barcode" class="form-control" value="{{ old('barcode') }}" placeholder="Optional">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Write a compelling product description...">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Pricing & Inventory --}}
            <div class="admin-card mb-4">
                <div class="card-title"><i class="bi bi-currency-pound me-2"></i>Pricing & Inventory</div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Price (£) *</label>
                        <input type="number" name="price" class="form-control" value="{{ old('price') }}" step="0.01" min="0" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Wholesale Price (£)</label>
                        <input type="number" name="wholesale_price" class="form-control" value="{{ old('wholesale_price') }}" step="0.01" min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Stock Quantity *</label>
                        <input type="number" name="stock" class="form-control" value="{{ old('stock', 0) }}" min="0" required>
                    </div>
                </div>
            </div>

            {{-- Offer Configuration --}}
            <div class="admin-card mb-4" style="border-color:rgba(225,112,85,0.2);">
                <div class="card-title"><i class="bi bi-lightning-fill text-warning me-2"></i>Bulk Offer Configuration</div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Min Quantity for Offer</label>
                        <input type="number" name="offer_min_qty" class="form-control" value="{{ old('offer_min_qty') }}" min="1" placeholder="e.g. 10">
                        <small style="color:var(--admin-muted);">Customers must buy this many to get discount</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Discount Percentage (%)</label>
                        <input type="number" name="offer_discount_percent" class="form-control" value="{{ old('offer_discount_percent') }}" min="0" max="100" step="0.01" placeholder="e.g. 15">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="offer_active" id="offerActive" value="1" {{ old('offer_active') ? 'checked' : '' }}>
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
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Product Type</label>
                    <select name="product_type" class="form-select">
                        <option value="normal" {{ old('product_type') == 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="wholesale" {{ old('product_type') == 'wholesale' ? 'selected' : '' }}>Wholesale</option>
                    </select>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_age_restricted" id="ageRestricted" value="1" {{ old('is_age_restricted') ? 'checked' : '' }}>
                    <label class="form-check-label" for="ageRestricted">🔞 Age Restricted (16+)</label>
                </div>
            </div>

            {{-- Images --}}
            <div class="admin-card mb-4">
                <div class="card-title"><i class="bi bi-images me-2"></i>Product Images</div>
                <input type="file" name="product_images[]" class="form-control" multiple accept="image/*">
                <small class="d-block mt-2" style="color:var(--admin-muted);">Upload one or more images (JPEG, PNG, WebP. Max 5MB each)</small>
            </div>

            {{-- Actions --}}
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-admin btn-lg"><i class="bi bi-plus-lg me-2"></i>Create Product & Generate QR</button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-admin-outline">Cancel</a>
            </div>
        </div>
    </div>
</form>
@endsection
