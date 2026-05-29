{{--
    admin/products/create.blade.php — Create new product form
    ===========================================================
    Fields: name (auto-slug), description (SimpleMDE), category, price, wholesale price,
    stock, product type, age restriction checkbox, multiple image upload (preview),
    barcode, active toggle, bulk-buy offer fields (min qty, discount %, active toggle).
    POST → admin.products.store → AdminProductController::store()
    QR code is auto-generated on store.
    Variable: $categories (for dropdown)
--}}
@extends('layouts.admin_noble')
@section('title', 'Add Product')

@section('content')
<nav class="page-breadcrumb mb-4">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
    <li class="breadcrumb-item active" aria-current="page">Add New</li>
  </ol>
</nav>

<form id="create-product-form" action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <input type="hidden" name="images" id="product_images_json" value="[]">

    <div class="row">
        {{-- Left Column: Form Details & Media Priority Manager --}}
        <div class="col-lg-8 grid-margin stretch-card d-flex flex-column gap-4">
            @php
                $activeTab = 'info';
                if ($errors->hasAny(['price', 'wholesale_price', 'stock', 'weight'])) {
                    $activeTab = 'pricing';
                } elseif ($errors->hasAny(['offer_min_qty', 'offer_discount_percent'])) {
                    $activeTab = 'offers';
                }
            @endphp

            {{-- Product Workspace Card (Tabbed) --}}
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-transparent border-0 pb-0 pt-3" style="background: rgba(108,92,231,0.02) !important;">
                    <ul class="nav nav-tabs card-header-tabs border-0 gap-2" id="product-workspace-tabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link {{ $activeTab === 'info' ? 'active' : '' }} fw-bold border-0 px-4 py-2 rounded-3 d-flex align-items-center gap-2" id="info-tab" data-toggle="tab" data-bs-toggle="tab" data-target="#info-pane" data-bs-target="#info-pane" type="button" role="tab">
                                <i class="bi bi-info-circle-fill text-primary"></i> Information
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link {{ $activeTab === 'pricing' ? 'active' : '' }} fw-bold border-0 px-4 py-2 rounded-3 d-flex align-items-center gap-2" id="pricing-tab" data-toggle="tab" data-bs-toggle="tab" data-target="#pricing-pane" data-bs-target="#pricing-pane" type="button" role="tab">
                                <i class="bi bi-currency-pound text-success"></i> Pricing & Stock
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link {{ $activeTab === 'offers' ? 'active' : '' }} fw-bold border-0 px-4 py-2 rounded-3 d-flex align-items-center gap-2" id="offers-tab" data-toggle="tab" data-bs-toggle="tab" data-target="#offers-pane" data-bs-target="#offers-pane" type="button" role="tab">
                                <i class="bi bi-percent text-danger"></i> Bulk Offers
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body pt-4">
                    <div class="tab-content" id="product-workspace-tab-content">
                        {{-- Tab 1: Information --}}
                        <div class="tab-pane fade {{ $activeTab === 'info' ? 'show active' : '' }}" id="info-pane" role="tabpanel" tabindex="0">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-600">Product Name <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-tag-fill text-primary"></i></span>
                                        <input type="text" name="name" id="product_name" class="form-control border-start-0 @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="Enter product name">
                                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-600">Description</label>
                                <textarea name="description" id="product_description" class="form-control" rows="4" placeholder="Write a compelling product description...">{{ old('description') }}</textarea>
                            </div>
                            
                            {{-- Classification & QR Code Notice (Moved inside Information Tab) --}}
                            <div class="row mt-3 border-top pt-3">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-600">Category <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-grid-fill text-primary"></i></span>
                                        <select name="category_id" id="product_category" class="form-control border-start-0 @error('category_id') is-invalid @enderror">
                                            <option value="">Select Category</option>
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('category_id') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-600">Product Type</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-tags-fill text-info"></i></span>
                                        <select name="product_type" class="form-control border-start-0">
                                            <option value="normal" {{ old('product_type') == 'normal' ? 'selected' : '' }}>Normal / Retail</option>
                                            <option value="wholesale" {{ old('product_type') == 'wholesale' ? 'selected' : '' }}>Wholesale Only</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row align-items-center mb-3">
                                <div class="col-md-6 mb-2">
                                    <div class="form-check mb-0 p-2.5 rounded-3 border d-flex align-items-center gap-2" style="min-height: 38px;">
                                        <input type="checkbox" name="is_age_restricted" id="is_age_restricted" class="form-check-input ms-0 mt-0" value="1" {{ old('is_age_restricted') ? 'checked' : '' }}>
                                        <label class="form-check-label fw-600 mb-0 cursor-pointer text-nowrap" for="is_age_restricted">
                                            Age Restricted (16+)
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2 text-start">
                                    <div class="bg-light p-2 rounded-3 border d-flex align-items-center gap-2" style="min-height: 38px; background: rgba(0,0,0,0.01);">
                                        <i class="bi bi-qr-code text-muted"></i>
                                        <span class="small text-muted" style="font-size: 0.72rem;">QR Code will be auto-generated on save.</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tab 2: Pricing & Stock --}}
                        <div class="tab-pane fade {{ $activeTab === 'pricing' ? 'show active' : '' }}" id="pricing-pane" role="tabpanel" tabindex="0">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-600">Retail Price (£) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-currency-pound text-success"></i></span>
                                        <input type="number" name="price" id="product_price" class="form-control border-start-0 @error('price') is-invalid @enderror" value="{{ old('price') }}" step="0.01" min="0" required placeholder="0.00">
                                        @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-600">Wholesale Price (£) <i class="bi bi-info-circle text-muted ms-1" style="cursor: help;" title="Optional cost value or discounted wholesale unit price"></i></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-currency-pound" style="color: #0d9488 !important;"></i></span>
                                        <input type="number" name="wholesale_price" class="form-control border-start-0" value="{{ old('wholesale_price') }}" step="0.01" min="0" placeholder="Optional">
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-600">Stock Quantity <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-archive-fill text-warning"></i></span>
                                        <input type="number" name="stock" id="product_stock" class="form-control border-start-0 @error('stock') is-invalid @enderror" value="{{ old('stock', 0) }}" min="0" required>
                                        @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div id="stock-val-badge" class="mt-1" style="min-height: 20px;"></div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-600">Weight (kg) <span class="text-danger">*</span> <i class="bi bi-info-circle text-muted ms-1" style="cursor: help;" title="Used to calculate shipping rates at checkout"></i></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-speedometer2" style="color: #ea580c !important;"></i></span>
                                        <input type="number" name="weight" class="form-control border-start-0 @error('weight') is-invalid @enderror" value="{{ old('weight') }}" step="0.01" min="0.01" required placeholder="e.g. 0.50">
                                        @error('weight') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tab 3: Bulk Offers --}}
                        <div class="tab-pane fade {{ $activeTab === 'offers' ? 'show active' : '' }}" id="offers-pane" role="tabpanel" tabindex="0">
                            <div class="row">
                                <div class="col-md-5 mb-3">
                                    <label class="form-label fw-600">Min Quantity for Offer</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-calculator" style="color: #db2777 !important;"></i></span>
                                        <input type="number" name="offer_min_qty" class="form-control border-start-0" value="{{ old('offer_min_qty') }}" min="1" placeholder="e.g. 12">
                                    </div>
                                    <small class="text-muted d-block mt-1">Quantity required to trigger discount.</small>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-600">Discount Percentage (%)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-percent text-danger"></i></span>
                                        <input type="number" name="offer_discount_percent" class="form-control border-start-0" value="{{ old('offer_discount_percent') }}" min="0" max="100" step="0.01" placeholder="e.g. 10">
                                    </div>
                                    <div id="offer-calc-badge" class="mt-1" style="min-height: 20px;"></div>
                                </div>
                                <div class="col-md-3 mb-3 d-flex align-items-end">
                                    <div class="form-check mb-2 p-2.5 rounded-3 border w-100 d-flex align-items-center gap-2" style="min-height: 38px;">
                                        <input type="checkbox" name="offer_active" id="offer_active" class="form-check-input ms-0 mt-0" value="1" {{ old('offer_active') ? 'checked' : '' }}>
                                        <label class="form-check-label fw-600 mb-0 cursor-pointer text-nowrap" for="offer_active">
                                            Activate Offer
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Premium Media & Image Priority Manager Card --}}
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                        <div>
                            <h6 class="card-title fw-bold text-primary mb-1 d-flex align-items-center">
                                <i data-feather="image" class="icon-md mr-2 text-primary"></i> Product Media & Priority Manager
                            </h6>
                            <p class="text-muted small mb-0" style="font-size: 0.78rem; line-height: 1.4;">
                                Click the star icon (<span class="text-warning">★</span>) to set an image as the **Primary Storefront Image** (Priority #1).
                            </p>
                        </div>
                        <span class="badge bg-soft-primary px-3 py-2 rounded-pill font-weight-bold" id="image-count-badge">0 Images</span>
                    </div>

                    {{-- Upload Dropzone --}}
                    <div class="upload-dropzone mb-4" id="dropzone">
                        <i data-feather="upload-cloud" class="text-primary mb-2" style="width: 28px; height: 28px;"></i>
                        <h6 class="fw-bold mb-1" style="font-size: 0.85rem;">Drag & drop images here or <span class="text-primary cursor-pointer">browse</span></h6>
                        <input type="file" id="dropzone-input" class="d-none" multiple accept="image/*">
                        {{-- Upload Progress --}}
                        <div class="progress mt-3 d-none" id="upload-progress-bar" style="height: 6px; border-radius: 10px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>

                    {{-- Sorted Image List --}}
                    <div class="media-list-grid mb-4" id="media-list-manager">
                        {{-- Rendered via Javascript --}}
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Mockup Preview, Classification & Actions --}}
        <div class="col-lg-4 grid-margin d-flex flex-column gap-4 position-sticky-sidebar">
            {{-- Collapsible Live Preview Card --}}
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header p-0 border-bottom" style="background: rgba(108,92,231,0.02); height: 48px;">
                    <button class="btn btn-link w-100 h-100 text-start text-decoration-none px-4 py-3 d-flex align-items-center justify-content-between fw-bold text-muted collapse-trigger-btn" type="button" data-bs-toggle="collapse" data-bs-target="#live-preview-collapse" aria-expanded="true">
                        <span class="small text-uppercase letter-spacing-1 d-flex align-items-center" style="font-size: 0.72rem; font-family: 'Outfit', sans-serif; letter-spacing: 0.5px;">
                            <span class="live-indicator me-2"></span> Live Page Preview
                        </span>
                        <i class="bi bi-chevron-down fs-6 collapse-icon"></i>
                    </button>
                </div>
                <div id="live-preview-collapse" class="collapse show">
                    <div class="card-body p-4 live-preview-body">
                        {{-- Premium Device Mockup --}}
                        <div class="device-container">
                            <div class="device-notch"></div>
                            <div class="device-screen">
                                <div class="device-header" id="preview-category">Category</div>
                                <div class="device-title" id="preview-name">Product Name</div>
                                
                                {{-- 1:1 Gallery Mockup --}}
                                <div class="device-gallery-box">
                                    <img id="preview-main-img" src="/images/placeholder-product.png" alt="Preview Image">
                                </div>
                                <div class="device-thumbs" id="preview-thumbnails">
                                    {{-- Rendered via JS --}}
                                </div>

                                {{-- Details --}}
                                <div class="device-price-row">
                                    <div class="device-price" id="preview-price">£0.00</div>
                                    <span class="device-badge bg-success" id="preview-stock-badge">In Stock</span>
                                </div>

                                <div class="mt-3 device-desc-text" style="font-size: 0.7rem; line-height: 1.5; height: 45px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;" id="preview-desc">
                                    No description provided.
                                </div>

                                {{-- Buttons --}}
                                <button type="button" class="device-btn">
                                    <i class="bi bi-bag-plus-fill me-1"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    
    {{-- Modern Floating Action Bar --}}
    <div class="floating-save-bar d-flex align-items-center justify-content-between px-4 py-3 border shadow-lg rounded-pill">
        <div class="d-flex align-items-center gap-3 text-white">
            <span class="live-indicator me-1"></span>
            <div style="font-family: 'Outfit', sans-serif;">
                <small class="text-muted text-uppercase d-block" style="font-size: 0.65rem; letter-spacing: 0.5px;">Currently Creating</small>
                <span class="fw-bold small" style="font-size: 0.85rem;" id="floating-product-title">Product Name</span>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-light rounded-pill px-4 btn-sm font-weight-bold" style="border-color: rgba(255,255,255,0.25); color: #fff;">
                Cancel
            </a>
            <button type="submit" form="create-product-form" class="btn btn-primary rounded-pill px-4 btn-sm font-weight-bold shadow-sm" style="background: var(--ps-gradient); border: none;">
                <i class="bi bi-plus-circle-fill me-1"></i> Create Product
            </button>
        </div>
    </div>
</form>

<style>
/* Scoped custom premium styles for mobile preview & priority manager */
.fw-600 { font-weight: 600; }
.bg-soft-primary { background: rgba(108,92,231,0.1); color: #6c5ce7; }
.live-indicator {
    width: 8px;
    height: 8px;
    background: #10b981;
    border-radius: 50%;
    display: inline-block;
    animation: blinkIndicator 1.5s infinite ease-in-out;
}
@keyframes blinkIndicator {
    0%, 100% { opacity: 0.3; transform: scale(0.9); }
    50% { opacity: 1; transform: scale(1.15); }
}

/* Premium Tab Navigation Styling */
.nav-tabs {
    border-bottom: none !important;
}
.nav-tabs .nav-link {
    background: transparent !important;
    color: #475569 !important;
    border: none !important;
    transition: all 0.25s ease !important;
}
html[data-admin-theme="dark"] .nav-tabs .nav-link {
    color: #94a3b8 !important;
}
.nav-tabs .nav-link.active {
    background: rgba(108, 92, 231, 0.1) !important;
    color: #6c5ce7 !important;
}
html[data-admin-theme="dark"] .nav-tabs .nav-link.active {
    background: rgba(167, 139, 250, 0.15) !important;
    color: #a78bfa !important;
}
.nav-tabs .nav-link:hover:not(.active) {
    background: rgba(0, 0, 0, 0.03) !important;
}
html[data-admin-theme="dark"] .nav-tabs .nav-link:hover:not(.active) {
    background: rgba(255, 255, 255, 0.03) !important;
}

/* Device Mockup Display */
.live-preview-body {
    background-color: #f1f5f9 !important; /* Soft light-gray background */
    transition: background-color 0.25s ease;
}
.device-container {
    max-width: 325px;
    margin: 0 auto;
    border: 10px solid #1e293b;
    border-radius: 35px;
    background: #0b0f19;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
    overflow: hidden;
    position: relative;
    aspect-ratio: 9 / 18.5;
}
.device-notch {
    width: 100px;
    height: 16px;
    background: #1e293b;
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    border-bottom-left-radius: 10px;
    border-bottom-right-radius: 10px;
    z-index: 10;
}
.device-screen {
    padding: 22px 12px 12px 12px;
    color: #0f172a; /* Dark text for light mode */
    font-family: 'Outfit', sans-serif;
    height: 100%;
    display: flex;
    flex-direction: column;
    background: #ffffff; /* White screen for light mode */
    transition: background-color 0.25s ease, color 0.25s ease;
}
.device-header {
    font-size: 0.7rem;
    opacity: 0.8;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #6c5ce7; /* Brand purple category header */
    font-weight: 700;
    transition: color 0.25s ease;
}
.device-title {
    color: #0f172a;
    font-size: 1.05rem;
    font-weight: 700;
    margin-top: 3px;
    margin-bottom: 10px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    transition: color 0.25s ease;
}
.device-gallery-box {
    width: 100%;
    aspect-ratio: 1;
    border-radius: 12px;
    overflow: hidden;
    background: #f8fafc;
    border: 1px solid rgba(0, 0, 0, 0.06);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.25s ease, border-color 0.25s ease;
}
.device-gallery-box img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}
.device-thumbs {
    display: flex;
    gap: 5px;
    margin-top: 8px;
    overflow-x: auto;
    justify-content: center;
    padding-bottom: 2px;
}
.device-thumb-dot {
    width: 22px;
    height: 22px;
    border-radius: 5px;
    border: 1px solid rgba(0, 0, 0, 0.15);
    background: #f8fafc;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.2s ease;
}
.device-thumb-dot.active {
    border-color: #6c5ce7;
    box-shadow: 0 0 5px rgba(108,92,231,0.5);
}
.device-thumb-dot img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.device-price-row {
    margin-top: 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.device-price {
    font-size: 1.2rem;
    font-weight: 800;
    color: #6c5ce7; /* Brand purple price */
    transition: color 0.25s ease;
}
.device-badge {
    font-size: 0.65rem;
    padding: 2px 7px;
    border-radius: 20px;
    font-weight: 700;
    text-transform: uppercase;
}
.device-btn {
    width: 100%;
    padding: 8px 12px;
    border-radius: 20px;
    border: none;
    font-size: 0.8rem;
    font-weight: 700;
    margin-top: auto;
    background: linear-gradient(135deg, #6c5ce7, #a78bfa);
    color: #fff;
    text-align: center;
    box-shadow: 0 4px 10px rgba(108,92,231,0.25);
}
.device-desc-text {
    color: #475569 !important; /* Soft dark gray description */
    transition: color 0.25s ease;
}

/* Dark Mode Overrides for Live Preview Mockup */
html[data-admin-theme="dark"] .live-preview-body {
    background-color: #0b0f19 !important;
}
html[data-admin-theme="dark"] .device-screen {
    background: #0f172a;
    color: #ffffff;
}
html[data-admin-theme="dark"] .device-header {
    color: #a78bfa;
}
html[data-admin-theme="dark"] .device-title {
    color: #ffffff;
}
html[data-admin-theme="dark"] .device-gallery-box {
    background: rgba(255, 255, 255, 0.02);
    border-color: rgba(255, 255, 255, 0.06);
}
html[data-admin-theme="dark"] .device-thumb-dot {
    border-color: rgba(255, 255, 255, 0.15);
    background: rgba(255, 255, 255, 0.05);
}
html[data-admin-theme="dark"] .device-price {
    color: #a78bfa;
}
html[data-admin-theme="dark"] .device-desc-text {
    color: #94a3b8 !important;
}

/* Premium Collapsible Card Header trigger and rotation styling */
.collapse-trigger-btn {
    color: #475569 !important;
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
    transition: background-color 0.2s ease, color 0.2s ease !important;
}
html[data-admin-theme="dark"] .collapse-trigger-btn {
    color: #cbd5e1 !important;
}
.collapse-trigger-btn:hover {
    color: #6c5ce7 !important;
    background-color: rgba(108, 92, 231, 0.04) !important;
    text-decoration: none !important;
}
html[data-admin-theme="dark"] .collapse-trigger-btn:hover {
    color: #a78bfa !important;
    background-color: rgba(167, 139, 250, 0.04) !important;
}
.collapse-trigger-btn:focus {
    text-decoration: none !important;
    outline: none !important;
    box-shadow: none !important;
}
.collapse-icon {
    transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}
.collapse-trigger-btn[aria-expanded="false"] .collapse-icon,
.collapse-trigger-btn.collapsed .collapse-icon {
    transform: rotate(180deg);
}

/* Media Manager Zone */
.upload-dropzone {
    border: 2px dashed rgba(108,92,231,0.3);
    border-radius: 16px;
    padding: 30px 20px;
    text-align: center;
    background: rgba(108,92,231,0.01);
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}
.upload-dropzone:hover {
    border-color: #6c5ce7;
    background: rgba(108,92,231,0.04);
}
.media-list-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(135px, 1fr));
    gap: 15px;
    margin-top: 10px;
}
.media-sort-item {
    border-radius: 14px;
    border: 1px solid rgba(0,0,0,0.08);
    background: #fff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    padding: 8px;
    position: relative;
    transition: all 0.2s ease;
}
html[data-admin-theme="dark"] .media-sort-item {
    background: rgba(255,255,255,0.02);
    border-color: rgba(255,255,255,0.06);
}
.media-sort-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 18px rgba(0,0,0,0.08);
    border-color: rgba(108,92,231,0.25);
}
.media-sort-img-wrap {
    width: 100%;
    aspect-ratio: 1;
    border-radius: 10px;
    overflow: hidden;
    background: rgba(255,255,255,0.02);
    border: 1px solid rgba(0,0,0,0.04);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}
.media-sort-img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}
.media-sort-badge {
    position: absolute;
    top: 6px;
    left: 6px;
    font-size: 0.6rem;
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: 800;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.media-sort-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 8px;
    gap: 4px;
}
.media-btn-circle {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(0,0,0,0.08);
    background: #f8fafc;
    color: #475569;
    cursor: pointer;
    font-size: 0.75rem;
    transition: all 0.2s ease;
    padding: 0;
}
html[data-admin-theme="dark"] .media-btn-circle {
    background: rgba(255,255,255,0.04);
    border-color: rgba(255,255,255,0.08);
    color: #cbd5e1;
}
.media-btn-circle:hover {
    background: #6c5ce7;
    color: #fff;
    border-color: #6c5ce7;
}
.media-btn-circle.btn-star-active,
html[data-admin-theme="dark"] .media-btn-circle.btn-star-active,
button.media-btn-circle.btn-star-active,
html[data-admin-theme="dark"] button.media-btn-circle.btn-star-active {
    background: rgba(234, 179, 8, 0.15) !important;
    border-color: rgba(234, 179, 8, 0.6) !important;
    color: #f59e0b !important;
    box-shadow: 0 0 10px rgba(234, 179, 8, 0.3) !important;
}
.media-btn-circle.btn-star-active i,
html[data-admin-theme="dark"] .media-btn-circle.btn-star-active i,
button.media-btn-circle.btn-star-active i,
html[data-admin-theme="dark"] button.media-btn-circle.btn-star-active i {
    color: #f59e0b !important;
}
.media-btn-circle.btn-star-active:hover,
html[data-admin-theme="dark"] .media-btn-circle.btn-star-active:hover {
    background: rgba(234, 179, 8, 0.25) !important;
    border-color: #f59e0b !important;
}
.media-btn-circle.btn-star-inactive:hover,
html[data-admin-theme="dark"] .media-btn-circle.btn-star-inactive:hover {
    background: rgba(234, 179, 8, 0.12) !important;
    border-color: rgba(234, 179, 8, 0.5) !important;
    color: #f59e0b !important;
}
.media-btn-circle.btn-star-inactive:hover i,
html[data-admin-theme="dark"] .media-btn-circle.btn-star-inactive:hover i {
    color: #f59e0b !important;
}
.media-btn-circle.btn-delete:hover {
    background: #ef4444;
    color: #fff;
    border-color: #ef4444;
}

/* ─── Premium UX Upgrades ─── */
.input-group-text {
    border-top-left-radius: 12px !important;
    border-bottom-left-radius: 12px !important;
    background: rgba(0, 0, 0, 0.02) !important;
    border-color: rgba(0, 0, 0, 0.08) !important;
    padding-left: 14px;
    padding-right: 14px;
}
html[data-admin-theme="dark"] .input-group-text {
    background: rgba(255, 255, 255, 0.02) !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
    color: #cbd5e1 !important;
}
.input-group .form-control {
    border-top-right-radius: 12px !important;
    border-bottom-right-radius: 12px !important;
    border-color: rgba(0, 0, 0, 0.08) !important;
}
html[data-admin-theme="dark"] .input-group .form-control {
    border-color: rgba(255, 255, 255, 0.08) !important;
}
.input-group:focus-within .input-group-text {
    border-color: #6c5ce7 !important;
    background: rgba(108, 92, 231, 0.03) !important;
}
.floating-save-bar {
    position: fixed;
    bottom: 24px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 1000;
    width: calc(100% - 32px);
    max-width: 780px;
    background: rgba(15, 23, 42, 0.8) !important;
    backdrop-filter: blur(12px) !important;
    -webkit-backdrop-filter: blur(12px) !important;
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3) !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
html[data-admin-theme="dark"] .floating-save-bar {
    background: rgba(10, 15, 28, 0.85) !important;
    border-color: rgba(255, 255, 255, 0.06) !important;
}
.form-check {
    border-color: rgba(0,0,0,0.08) !important;
    background: rgba(0,0,0,0.02) !important;
    transition: all 0.2s ease !important;
}
html[data-admin-theme="dark"] .form-check {
    border-color: rgba(255, 255, 255, 0.08) !important;
    background: rgba(255,255,255,0.03) !important;
}
.form-check-label {
    color: #475569 !important;
    transition: all 0.2s ease !important;
}
html[data-admin-theme="dark"] .form-check-label {
    color: #cbd5e1 !important;
}
.position-sticky-sidebar {
    position: sticky !important;
    top: 24px !important;
    height: fit-content !important;
    align-self: start !important;
    z-index: 5 !important;
}
@media (max-width: 991px) {
    .position-sticky-sidebar {
        position: static !important;
    }
}
@media (max-width: 575px) {
    .floating-save-bar {
        border-radius: 18px !important;
        padding: 10px 16px !important;
        bottom: 12px;
        flex-direction: column;
        gap: 10px;
        align-items: stretch !important;
        text-align: center;
    }
    .floating-save-bar .d-flex {
        justify-content: center;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Current images list loaded from backend
    const inputJson = document.getElementById('product_images_json');
    let imagesList = [];
    try {
        imagesList = JSON.parse(inputJson.value || '[]');
    } catch(e) {
        imagesList = [];
    }

    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('dropzone-input');
    const uploadProgress = document.getElementById('upload-progress-bar');
    const progressBar = uploadProgress.querySelector('.progress-bar');

    // Setup drag and drop events
    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
            e.preventDefault();
            dropzone.classList.add('bg-soft-primary');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
            e.preventDefault();
            dropzone.classList.remove('bg-soft-primary');
        }, false);
    });

    dropzone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFilesUpload(files);
    });

    dropzone.addEventListener('click', (e) => {
        if (!e.target.closest('input')) {
            fileInput.click();
        }
    });

    fileInput.addEventListener('change', function() {
        handleFilesUpload(this.files);
    });

    // Handle AJAX upload to controller
    function handleFilesUpload(files) {
        if (!files.length) return;
        
        uploadProgress.classList.remove('d-none');
        let uploadedCount = 0;
        const totalFiles = files.length;

        Array.from(files).forEach((file, index) => {
            const formData = new FormData();
            formData.append('file', file);

            fetch('{{ route("admin.products.uploadImage") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.url) {
                    imagesList.push(data.url);
                    updateImagesState();
                }
            })
            .catch(err => console.error('Upload error:', err))
            .finally(() => {
                uploadedCount++;
                const percentage = Math.round((uploadedCount / totalFiles) * 100);
                progressBar.style.width = percentage + '%';
                
                if (uploadedCount === totalFiles) {
                    setTimeout(() => {
                        uploadProgress.classList.add('d-none');
                        progressBar.style.width = '0%';
                    }, 800);
                }
            });
        });
    }

    // Move image position
    window.moveImageOrder = function(index, direction) {
        if (direction === 'up' && index > 0) {
            const temp = imagesList[index];
            imagesList[index] = imagesList[index - 1];
            imagesList[index - 1] = temp;
        } else if (direction === 'down' && index < imagesList.length - 1) {
            const temp = imagesList[index];
            imagesList[index] = imagesList[index + 1];
            imagesList[index + 1] = temp;
        }
        updateImagesState();
    };

    // Set an image as main priority #1
    window.setImageAsMain = function(index) {
        if (index === 0) return;
        const mainImage = imagesList.splice(index, 1)[0];
        imagesList.unshift(mainImage);
        updateImagesState();
    };

    // Remove/delete an image
    window.deleteImageItem = function(index) {
        if (confirm('Are you sure you want to remove this image from the product?')) {
            imagesList.splice(index, 1);
            updateImagesState();
        }
    };

    // Synchronize images state
    function updateImagesState() {
        // Save to hidden input as JSON string
        inputJson.value = JSON.stringify(imagesList);
        
        // Render UI panels
        renderMediaManager();
        syncLivePreview();
    }

    // Render interactive media manager grid
    function renderMediaManager() {
        const grid = document.getElementById('media-list-manager');
        const badge = document.getElementById('image-count-badge');
        
        badge.innerText = `${imagesList.length} ${imagesList.length === 1 ? 'Image' : 'Images'}`;
        grid.innerHTML = '';

        if (!imagesList.length) {
            grid.innerHTML = `
                <div class="col-12 text-center py-4 text-muted">
                    <p class="mb-0 font-weight-bold">No images uploaded yet.</p>
                </div>
            `;
            return;
        }

        imagesList.forEach((url, i) => {
            const isMain = i === 0;
            const itemHTML = `
                <div class="media-sort-item">
                    <div class="media-sort-img-wrap">
                        <img src="${url}" alt="Product Image ${i + 1}">
                        <span class="media-sort-badge ${isMain ? 'bg-warning text-dark' : 'bg-secondary text-white'}">
                            ${isMain ? '★ Main' : `#${i + 1}`}
                        </span>
                    </div>
                    <div class="media-sort-actions">
                        <button type="button" class="media-btn-circle ${isMain ? 'btn-star-active' : 'btn-star-inactive'}" title="${isMain ? 'Primary Storefront Image (Priority #1)' : 'Set as Primary Storefront Image'}" onclick="setImageAsMain(${i})">
                            <i class="bi bi-star-fill" style="font-size: 0.75rem;"></i>
                        </button>
                        <div class="d-flex gap-1">
                            <button type="button" class="media-btn-circle" title="Move Up" ${isMain ? 'disabled style="opacity:0.3;cursor:not-allowed;"' : ''} onclick="moveImageOrder(${i}, 'up')">
                                <i class="bi bi-arrow-left-short" style="font-size: 1rem;"></i>
                            </button>
                            <button type="button" class="media-btn-circle" title="Move Down" ${i === imagesList.length - 1 ? 'disabled style="opacity:0.3;cursor:not-allowed;"' : ''} onclick="moveImageOrder(${i}, 'down')">
                                <i class="bi bi-arrow-right-short" style="font-size: 1rem;"></i>
                            </button>
                        </div>
                        <button type="button" class="media-btn-circle btn-delete" title="Delete Image" onclick="deleteImageItem(${i})">
                            <i class="bi bi-trash" style="font-size: 0.75rem;"></i>
                        </button>
                    </div>
                </div>
            `;
            grid.insertAdjacentHTML('beforeend', itemHTML);
        });
    }

    // Dynamic Live Mockup synchronization
    const inputName = document.getElementById('product_name');
    const inputPrice = document.getElementById('product_price');
    const inputStock = document.getElementById('product_stock');
    const selectCategory = document.getElementById('product_category');
    const textDesc = document.getElementById('product_description');

    const prevName = document.getElementById('preview-name');
    const prevCategory = document.getElementById('preview-category');
    const prevPrice = document.getElementById('preview-price');
    const prevStockBadge = document.getElementById('preview-stock-badge');
    const prevDesc = document.getElementById('preview-desc');
    const prevMainImg = document.getElementById('preview-main-img');
    const prevThumbs = document.getElementById('preview-thumbnails');

    function syncLivePreview() {
        // Name
        prevName.innerText = inputName.value.trim() || 'Product Name';
        
        // Category
        if (selectCategory.selectedIndex > 0) {
            prevCategory.innerText = selectCategory.options[selectCategory.selectedIndex].text;
        } else {
            prevCategory.innerText = 'Category';
        }

        // Price
        const priceVal = parseFloat(inputPrice.value || 0);
        prevPrice.innerText = `£${priceVal.toFixed(2)}`;

        // Stock
        const stockVal = parseInt(inputStock.value || 0);
        if (stockVal > 0) {
            prevStockBadge.innerText = 'In Stock';
            prevStockBadge.className = 'device-badge bg-success';
        } else {
            prevStockBadge.innerText = 'Out of Stock';
            prevStockBadge.className = 'device-badge bg-danger';
        }

        // Description
        const descText = textDesc.value.trim();
        prevDesc.innerText = descText || 'No description provided.';

        // Image Gallery
        if (imagesList.length > 0) {
            prevMainImg.src = imagesList[0];
            
            prevThumbs.innerHTML = '';
            if (imagesList.length > 1) {
                imagesList.forEach((url, idx) => {
                    const activeClass = idx === 0 ? 'active' : '';
                    const thumbHTML = `
                        <div class="device-thumb-dot ${activeClass}" onclick="changePreviewActiveSlide(${idx})">
                            <img src="${url}">
                        </div>
                    `;
                    prevThumbs.insertAdjacentHTML('beforeend', thumbHTML);
                });
            }
        } else {
            prevMainImg.src = '/images/placeholder-product.png';
            prevThumbs.innerHTML = '';
        }
    }

    // Set preview image slider on thumbnail click
    window.changePreviewActiveSlide = function(index) {
        if (index >= 0 && index < imagesList.length) {
            prevMainImg.src = imagesList[index];
            const dots = prevThumbs.querySelectorAll('.device-thumb-dot');
            dots.forEach((dot, idx) => {
                if (idx === index) {
                    dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                }
            });
        }
    };

    // Sync floating title
    const floatTitle = document.getElementById('floating-product-title');
    function syncFloatingTitle() {
        floatTitle.innerText = inputName.value.trim() || 'Product Name';
    }

    // Live Calculations
    const inputWholesale = document.getElementsByName('wholesale_price')[0];
    const inputDiscount = document.getElementsByName('offer_discount_percent')[0];
    const inputMinQty = document.getElementsByName('offer_min_qty')[0];
    const inputOfferActive = document.getElementsByName('offer_active')[0];

    const stockValBadge = document.getElementById('stock-val-badge');
    const offerCalcBadge = document.getElementById('offer-calc-badge');

    function updateLiveCalculations() {
        // 1. Stock Valuation
        const price = parseFloat(inputPrice.value || 0);
        const stock = parseInt(inputStock.value || 0);
        if (price > 0 && stock > 0) {
            const valuation = price * stock;
            stockValBadge.innerHTML = `<span class="badge bg-soft-success text-success px-2.5 py-1 rounded"><i class="bi bi-wallet2 me-1"></i> Inventory Value: £${valuation.toFixed(2)}</span>`;
        } else {
            stockValBadge.innerHTML = '';
        }

        // 2. Offer Calculation
        const discount = parseFloat(inputDiscount.value || 0);
        const minQty = parseInt(inputMinQty.value || 0);
        const isOfferActive = inputOfferActive ? inputOfferActive.checked : false;

        if (price > 0 && discount > 0 && isOfferActive) {
            const savings = price * (discount / 100);
            const promoPrice = price - savings;
            offerCalcBadge.innerHTML = `
                <span class="badge bg-soft-warning text-warning px-2.5 py-1 rounded d-inline-block mt-1">
                    <i class="bi bi-tag-fill me-1"></i> Promo Price: £${promoPrice.toFixed(2)} each 
                    <small class="d-block text-muted mt-1" style="font-size:0.65rem;">(Saves £${savings.toFixed(2)} per unit on min purchase of ${minQty || 1})</small>
                </span>
            `;
        } else {
            offerCalcBadge.innerHTML = '';
        }
    }

    // Setup input trigger listeners
    [inputName, inputPrice, inputStock, textDesc].forEach(el => {
        el.addEventListener('input', () => {
            syncLivePreview();
            syncFloatingTitle();
        });
        el.addEventListener('change', () => {
            syncLivePreview();
            syncFloatingTitle();
        });
    });
    selectCategory.addEventListener('change', syncLivePreview);

    [inputPrice, inputStock, inputDiscount, inputMinQty].forEach(el => {
        if (el) {
            el.addEventListener('input', updateLiveCalculations);
            el.addEventListener('change', updateLiveCalculations);
        }
    });
    if (inputOfferActive) {
        inputOfferActive.addEventListener('change', updateLiveCalculations);
    }

    // Collapse preview on mobile viewports dynamically
    const previewCollapseEl = document.getElementById('live-preview-collapse');
    if (window.innerWidth < 992 && previewCollapseEl) {
        const bsCollapse = new bootstrap.Collapse(previewCollapseEl, { toggle: false });
        bsCollapse.hide();
    }

    // Initial render
    updateImagesState();
    syncFloatingTitle();
    updateLiveCalculations();
});
</script>
@endsection
