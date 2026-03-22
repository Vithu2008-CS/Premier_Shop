@extends('layouts.admin_noble')
@section('title', 'Add Product')

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
    <li class="breadcrumb-item active" aria-current="page">Add New</li>
  </ol>
</nav>

<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-lg-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Product Details</h6>
                    <div class="row">
                        <div class="col-md-9 mb-3">
                            <label class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="Enter product name">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Barcode</label>
                            <input type="text" name="barcode" class="form-control" value="{{ old('barcode') }}" placeholder="Optional">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="8" placeholder="Write a compelling product description...">{{ old('description') }}</textarea>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h6 class="card-title">Pricing & Inventory</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Retail Price (£) <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" step="0.01" min="0" required>
                            @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Wholesale Price (£)</label>
                            <input type="number" name="wholesale_price" class="form-control" value="{{ old('wholesale_price') }}" step="0.01" min="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror" value="{{ old('stock', 0) }}" min="0" required>
                            @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h6 class="card-title">Bulk Offer Configuration</h6>
                    <div class="row">
                        <div class="col-md-5 mb-3">
                            <label class="form-label">Min Quantity for Offer</label>
                            <input type="number" name="offer_min_qty" class="form-control" value="{{ old('offer_min_qty') }}" min="1" placeholder="e.g. 12">
                            <small class="text-muted d-block mt-1">Required quantity to trigger the discount.</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Discount Percentage (%)</label>
                            <input type="number" name="offer_discount_percent" class="form-control" value="{{ old('offer_discount_percent') }}" min="0" max="100" step="0.01" placeholder="e.g. 10">
                        </div>
                        <div class="col-md-3 mb-3 d-flex align-items-end">
                            <div class="form-check mb-2">
                                <label class="form-check-label">
                                    <input type="checkbox" name="offer_active" class="form-check-input" value="1" {{ old('offer_active') ? 'checked' : '' }}>
                                    Activate Offer
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 grid-margin">
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="card-title">Classification</h6>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-control @error('category_id') is-invalid @enderror">
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Product Type</label>
                        <select name="product_type" class="form-control">
                            <option value="normal" {{ old('product_type') == 'normal' ? 'selected' : '' }}>Normal / Retail</option>
                            <option value="wholesale" {{ old('product_type') == 'wholesale' ? 'selected' : '' }}>Wholesale Only</option>
                        </select>
                    </div>
                    <div class="form-check mb-2">
                        <label class="form-check-label">
                            <input type="checkbox" name="is_age_restricted" class="form-check-input" value="1" {{ old('is_age_restricted') ? 'checked' : '' }}>
                            Age Restricted (16+)
                        </label>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="card-title">Product Media</h6>
                    <div class="mb-3">
                        <label class="form-label">Upload Images</label>
                        <input type="file" name="product_images[]" class="form-control" multiple accept="image/*">
                        <small class="text-muted d-block mt-2">You can select multiple images. Max 10MB total.</small>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary btn-block mb-2">
                        <i data-feather="upload-cloud" class="icon-sm mr-2"></i> Save & Generate QR
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-block">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
