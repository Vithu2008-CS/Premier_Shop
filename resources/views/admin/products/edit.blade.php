@extends('layouts.admin_noble')
@section('title', 'Edit Product')

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($product->name, 30) }}</li>
  </ol>
</nav>

<form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="row">
        <div class="col-lg-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Product Details</h6>
                    <div class="row">
                        <div class="col-md-9 mb-3">
                            <label class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Barcode</label>
                            <input type="text" name="barcode" class="form-control" value="{{ old('barcode', $product->barcode) }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="8">{{ old('description', $product->description) }}</textarea>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h6 class="card-title">Pricing & Inventory</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Retail Price (£) <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $product->price) }}" step="0.01" min="0" required>
                            @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Wholesale Price (£)</label>
                            <input type="number" name="wholesale_price" class="form-control" value="{{ old('wholesale_price', $product->wholesale_price) }}" step="0.01" min="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror" value="{{ old('stock', $product->stock) }}" min="0" required>
                            @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h6 class="card-title">Bulk Offer Configuration</h6>
                    <div class="row">
                        <div class="col-md-5 mb-3">
                            <label class="form-label">Min Quantity for Offer</label>
                            <input type="number" name="offer_min_qty" class="form-control" value="{{ old('offer_min_qty', $product->offer_min_qty) }}" min="1">
                            <small class="text-muted d-block mt-1">Quantity required to trigger discount.</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Discount Percentage (%)</label>
                            <input type="number" name="offer_discount_percent" class="form-control" value="{{ old('offer_discount_percent', $product->offer_discount_percent) }}" min="0" max="100" step="0.01">
                        </div>
                        <div class="col-md-3 mb-3 d-flex align-items-end">
                            <div class="form-check mb-2">
                                <label class="form-check-label">
                                    <input type="checkbox" name="offer_active" class="form-check-input" value="1" {{ old('offer_active', $product->offer_active) ? 'checked' : '' }}>
                                    Activate Offer
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 grid-margin">
            {{-- QR Code Section --}}
            <div class="card mb-3">
                <div class="card-body text-center">
                    <h6 class="card-title text-left">Product QR Code</h6>
                    @if($product->qr_code)
                        <div class="bg-light p-3 rounded mb-3">
                            <img src="{{ $product->qr_code }}" alt="QR Code" class="img-fluid wd-150 mx-auto d-block" style="mix-blend-mode: multiply;">
                        </div>
                        <div class="d-flex flex-column gap-2">
                            <a href="{{ $product->qr_code }}" download class="btn btn-outline-primary btn-sm btn-block mb-2">
                                <i data-feather="download" class="icon-sm mr-2"></i> Download QR
                            </a>
                            <button type="submit" form="regenerate-qr-form" class="btn btn-link btn-sm text-muted">
                                <i data-feather="refresh-cw" class="icon-xs mr-1"></i> Regenerate QR Code
                            </button>
                        </div>
                    @else
                        <div class="py-4 text-muted">
                            <i data-feather="slash" class="icon-xxl mb-2"></i>
                            <p>No QR Code</p>
                            <button type="submit" form="regenerate-qr-form" class="btn btn-primary btn-sm mt-3">Generate Now</button>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="card-title">Classification</h6>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-control @error('category_id') is-invalid @enderror">
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Product Type</label>
                        <select name="product_type" class="form-control">
                            <option value="normal" {{ old('product_type', $product->product_type) == 'normal' ? 'selected' : '' }}>Normal / Retail</option>
                            <option value="wholesale" {{ old('product_type', $product->product_type) == 'wholesale' ? 'selected' : '' }}>Wholesale Only</option>
                        </select>
                    </div>
                    <div class="form-check mb-2">
                        <label class="form-check-label">
                            <input type="checkbox" name="is_age_restricted" class="form-check-input" value="1" {{ old('is_age_restricted', $product->is_age_restricted) ? 'checked' : '' }}>
                            Age Restricted (16+)
                        </label>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="card-title">Product Media</h6>
                    @if($product->images && count($product->images) > 0)
                        <div class="row no-gutters mb-3">
                            @foreach($product->images as $img)
                                <div class="col-4 p-1">
                                    <img src="{{ $img }}" class="img-fluid rounded border" style="height: 60px; width: 100%; object-fit: cover;">
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label">Add More Images</label>
                        <input type="file" name="product_images[]" class="form-control" multiple accept="image/*">
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary btn-block mb-2">
                        <i data-feather="check-square" class="icon-sm mr-2"></i> Update Product
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-block">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- Hidden Regenerate Form --}}
<form id="regenerate-qr-form" action="{{ route('admin.products.regenerateQr', $product) }}" method="POST" style="display: none;">
    @csrf
</form>

@endsection
