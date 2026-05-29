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
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
    <li class="breadcrumb-item active" aria-current="page">Add New</li>
  </ol>
</nav>

<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <input type="hidden" name="images" id="product_images_json" value="[]">

    <div class="row">
        {{-- Left Column: Form Details & Media Priority Manager --}}
        <div class="col-lg-8 grid-margin stretch-card d-flex flex-column gap-4">
            {{-- Product Details Card --}}
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <h6 class="card-title fw-bold text-primary mb-4 d-flex align-items-center">
                        <i data-feather="info" class="icon-md mr-2 text-primary"></i> Product Details
                    </h6>
                    <div class="row">
                        <div class="col-md-9 mb-3">
                            <label class="form-label fw-600">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="product_name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="Enter product name">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-600">Barcode</label>
                            <input type="text" name="barcode" class="form-control" value="{{ old('barcode') }}" placeholder="Optional">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Description</label>
                        <textarea name="description" id="product_description" class="form-control" rows="6" placeholder="Write a compelling product description...">{{ old('description') }}</textarea>
                    </div>
                    
                    <hr class="my-4 opacity-5">
                    
                    <h6 class="card-title fw-bold text-primary mb-4 d-flex align-items-center">
                        <i data-feather="dollar-sign" class="icon-md mr-2 text-primary"></i> Pricing & Inventory
                    </h6>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-600">Retail Price (£) <span class="text-danger">*</span></label>
                            <input type="number" name="price" id="product_price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" step="0.01" min="0" required placeholder="0.00">
                            @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-600">Wholesale Price (£)</label>
                            <input type="number" name="wholesale_price" class="form-control" value="{{ old('wholesale_price') }}" step="0.01" min="0" placeholder="Optional">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-600">Stock Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="stock" id="product_stock" class="form-control @error('stock') is-invalid @enderror" value="{{ old('stock', 0) }}" min="0" required>
                            @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-600">Weight (kg) <span class="text-danger">*</span></label>
                            <input type="number" name="weight" class="form-control @error('weight') is-invalid @enderror" value="{{ old('weight') }}" step="0.01" min="0.01" required placeholder="e.g. 0.50">
                            @error('weight') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    
                    <hr class="my-4 opacity-5">
                    
                    <h6 class="card-title fw-bold text-primary mb-4 d-flex align-items-center">
                        <i data-feather="percent" class="icon-md mr-2 text-primary"></i> Bulk Offer Configuration
                    </h6>
                    <div class="row">
                        <div class="col-md-5 mb-3">
                            <label class="form-label fw-600">Min Quantity for Offer</label>
                            <input type="number" name="offer_min_qty" class="form-control" value="{{ old('offer_min_qty') }}" min="1" placeholder="e.g. 12">
                            <small class="text-muted d-block mt-1">Quantity required to trigger discount.</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-600">Discount Percentage (%)</label>
                            <input type="number" name="offer_discount_percent" class="form-control" value="{{ old('offer_discount_percent') }}" min="0" max="100" step="0.01" placeholder="e.g. 10">
                        </div>
                        <div class="col-md-3 mb-3 d-flex align-items-end">
                            <div class="form-check mb-2">
                                <label class="form-check-label font-weight-bold">
                                    <input type="checkbox" name="offer_active" class="form-check-input" value="1" {{ old('offer_active') ? 'checked' : '' }}>
                                    Activate Offer
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Premium Media & Image Priority Manager Card --}}
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="card-title fw-bold text-primary mb-0 d-flex align-items-center">
                            <i data-feather="image" class="icon-md mr-2 text-primary"></i> Product Media & Priority Manager
                        </h6>
                        <span class="badge bg-soft-primary px-3 py-2 rounded-pill font-weight-bold" id="image-count-badge">0 Images</span>
                    </div>

                    {{-- Upload Dropzone --}}
                    <div class="upload-dropzone mb-4" id="dropzone">
                        <i data-feather="upload-cloud" class="text-primary mb-3" style="width: 44px; height: 44px;"></i>
                        <h5 class="fw-bold mb-1">Drag & Drop Product Images</h5>
                        <p class="text-muted small mb-3">Or click here to browse files. WebP conversion is automatic.</p>
                        <input type="file" id="dropzone-input" class="d-none" multiple accept="image/*">
                        <div class="d-flex justify-content-center">
                            <button type="button" class="btn btn-outline-primary btn-sm px-4 rounded-pill fw-bold" onclick="document.getElementById('dropzone-input').click()">
                                Select Files
                            </button>
                        </div>
                        {{-- Upload Progress --}}
                        <div class="progress mt-3 d-none" id="upload-progress-bar" style="height: 6px; border-radius: 10px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>

                    {{-- Sorted Image List --}}
                    <div class="media-list-grid mb-4" id="media-list-manager">
                        {{-- Rendered via Javascript --}}
                    </div>

                    {{-- Quick Action Save Button --}}
                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <button type="submit" class="btn btn-primary px-4 rounded-pill font-weight-bold shadow-sm d-flex align-items-center">
                            <i class="bi bi-plus-circle me-2"></i> Create Product & Media
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Mockup Preview, Classification & Actions --}}
        <div class="col-lg-4 grid-margin d-flex flex-column gap-4">
            {{-- Sleek Live Mobile Page Preview Mockup --}}
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="p-3 border-bottom d-flex align-items-center justify-content-between" style="background: rgba(108,92,231,0.02);">
                        <span class="fw-bold small text-muted text-uppercase letter-spacing-1 d-flex align-items-center">
                            <span class="live-indicator me-2"></span> Live Page Preview
                        </span>
                        <span class="badge bg-secondary font-weight-600">Smart View</span>
                    </div>
                    <div class="p-4" style="background: #0b0f19;">
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

                                <div class="mt-3" style="font-size: 0.7rem; color: #94a3b8; line-height: 1.5; height: 45px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;" id="preview-desc">
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

            {{-- Classification Section --}}
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <h6 class="card-title fw-bold text-primary mb-4 d-flex align-items-center">
                        <i data-feather="grid" class="icon-md mr-2 text-primary"></i> Classification
                    </h6>
                    <div class="mb-3">
                        <label class="form-label fw-600">Category</label>
                        <select name="category_id" id="product_category" class="form-control @error('category_id') is-invalid @enderror">
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Product Type</label>
                        <select name="product_type" class="form-control">
                            <option value="normal" {{ old('product_type') == 'normal' ? 'selected' : '' }}>Normal / Retail</option>
                            <option value="wholesale" {{ old('product_type') == 'wholesale' ? 'selected' : '' }}>Wholesale Only</option>
                        </select>
                    </div>
                    <div class="form-check mb-2">
                        <label class="form-check-label font-weight-bold">
                            <input type="checkbox" name="is_age_restricted" class="form-check-input" value="1" {{ old('is_age_restricted') ? 'checked' : '' }}>
                            Age Restricted (16+)
                        </label>
                    </div>
                </div>
            </div>

            {{-- Actions Card --}}
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body d-flex flex-column gap-2">
                    <button type="submit" class="btn btn-primary btn-block rounded-pill py-2.5 font-weight-bold" style="background: var(--ps-gradient); border: none;">
                        <i data-feather="upload-cloud" class="icon-sm mr-2"></i> Save & Generate QR
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-block rounded-pill py-2.5">
                        Cancel
                    </a>
                </div>
            </div>
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

/* Device Mockup Display */
.device-container {
    max-width: 290px;
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
    color: #fff;
    font-family: 'Outfit', sans-serif;
    height: 100%;
    display: flex;
    flex-direction: column;
    background: #0f172a;
}
.device-header {
    font-size: 0.7rem;
    opacity: 0.6;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #a78bfa;
    font-weight: 700;
}
.device-title {
    font-size: 1.05rem;
    font-weight: 700;
    margin-top: 3px;
    margin-bottom: 10px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.device-gallery-box {
    width: 100%;
    aspect-ratio: 1;
    border-radius: 12px;
    overflow: hidden;
    background: rgba(255,255,255,0.02);
    border: 1px solid rgba(255,255,255,0.06);
    display: flex;
    align-items: center;
    justify-content: center;
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
    border: 1px solid rgba(255,255,255,0.15);
    background: rgba(255,255,255,0.05);
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
    color: #a78bfa;
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
.dark-theme .media-sort-item {
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
.dark-theme .media-btn-circle {
    background: rgba(255,255,255,0.04);
    border-color: rgba(255,255,255,0.08);
    color: #cbd5e1;
}
.media-btn-circle:hover {
    background: #6c5ce7;
    color: #fff;
    border-color: #6c5ce7;
}
.media-btn-circle.btn-star-active {
    background: #eab308;
    color: #fff;
    border-color: #eab308;
}
.media-btn-circle.btn-delete:hover {
    background: #ef4444;
    color: #fff;
    border-color: #ef4444;
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
                        <button type="button" class="media-btn-circle ${isMain ? 'btn-star-active' : ''}" title="${isMain ? 'Main Image' : 'Set as Main'}" onclick="setImageAsMain(${i})">
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

    // Setup input trigger listeners
    [inputName, inputPrice, inputStock, textDesc].forEach(el => {
        el.addEventListener('input', syncLivePreview);
        el.addEventListener('change', syncLivePreview);
    });
    selectCategory.addEventListener('change', syncLivePreview);

    // Initial render
    updateImagesState();
});
</script>
@endsection
