{{--
    admin/categories/edit.blade.php — Edit category form
    ======================================================
    Same fields as create + current image preview with replace option.
    PUT → admin.categories.update → CategoryController::update()
    Variable: $category
--}}
@extends('layouts.admin_noble')
@section('title', 'Edit Category')

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Categories</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit: {{ $category->name }}</li>
  </ol>
</nav>

<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h6 class="card-title">Category Information</h6>
        
        <form id="edit-category-form" action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Category Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" required placeholder="e.g. Beverages" value="{{ old('name', $category->name) }}">
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="5" placeholder="Describe this category...">{{ old('description', $category->description) }}</textarea>
                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            @if($category->image)
            <div class="row mb-4">
                <div class="col-md-4">
                    <label class="form-label d-block text-muted small font-weight-bold">Current Category Image</label>
                    <div class="p-2 border rounded bg-light text-center">
                        <img src="{{ $category->image }}" alt="{{ $category->name }}" class="img-fluid rounded" style="max-height: 120px;">
                    </div>
                </div>
            </div>
            @endif

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Update Category Image</label>
                    <input type="file" name="image_file" class="form-control @error('image_file') is-invalid @enderror" accept="image/*">
                    <small class="text-muted d-block mt-2">Leave blank to keep existing image. Recommended: 400x400px.</small>
                    @error('image_file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Or Update Image URL</label>
                    <input type="url" name="image_link" class="form-control @error('image_link') is-invalid @enderror" placeholder="https://example.com/category-image.jpg" value="{{ old('image_link', Str::startsWith($category->image, 'http') ? $category->image : '') }}">
                    @error('image_link') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

        </form>
      </div>
    </div>
  </div>
</div>

{{-- Modern Floating Action Bar --}}
<div class="floating-save-bar d-flex align-items-center justify-content-between px-4 py-3 border shadow-lg rounded-pill">
    <div class="d-flex align-items-center gap-2" style="font-family: 'Outfit', sans-serif;">
        <span class="live-indicator me-1"></span>
        <div class="d-flex align-items-baseline gap-2">
            <span class="text-muted text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px; font-weight: 600; white-space: nowrap; line-height: 1;">Currently Editing:</span>
            <span class="fw-bold text-nowrap floating-bar-title" style="font-size: 0.85rem; line-height: 1;" id="floating-category-title">{{ $category->name }}</span>
        </div>
    </div>
    <div class="button-group">
        <button type="button" class="btn btn-danger" onclick="event.preventDefault(); if(confirm('Delete this category?')) document.getElementById('delete-category-form').submit();">
            <i class="bi bi-trash-fill me-1"></i> Delete
        </button>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-light">
            Cancel
        </a>
        <button type="submit" form="edit-category-form" class="btn btn-primary">
            <i class="bi bi-check-circle-fill me-1"></i> Save
        </button>
    </div>
</div>

{{-- Hidden Deletion Form --}}
<form id="delete-category-form" action="{{ route('admin.categories.destroy', $category) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<style>
/* Scoped custom premium styles for floating actions and indicator */
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

.floating-save-bar .button-group {
    display: flex;
    align-items: center;
    gap: 12px; /* Perfect space between buttons */
}

.floating-save-bar .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 38px !important;
    min-width: 110px !important;
    padding: 0 24px !important;
    font-size: 0.82rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.3px;
    border-radius: 30px !important;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

.floating-save-bar .btn-outline-light {
    border: 1.5px solid rgba(0, 0, 0, 0.15) !important;
    background: transparent !important;
    color: #475569 !important;
}
.floating-save-bar .btn-outline-light:hover {
    background: rgba(0, 0, 0, 0.04) !important;
    border-color: rgba(0, 0, 0, 0.25) !important;
    color: #1e293b !important;
}

.floating-save-bar .btn-primary {
    border: 1.5px solid transparent !important;
    background: var(--ps-gradient, linear-gradient(135deg, #6c5ce7, #a78bfa)) !important;
    color: #ffffff !important;
    box-shadow: 0 4px 12px rgba(108, 92, 231, 0.2) !important;
}
.floating-save-bar .btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(108, 92, 231, 0.3) !important;
    color: #ffffff !important;
}

.floating-save-bar .btn-danger {
    border: 1.5px solid transparent !important;
    background: #ef4444 !important;
    color: #ffffff !important;
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2) !important;
}
.floating-save-bar .btn-danger:hover {
    background: #dc2626 !important;
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(239, 68, 68, 0.3) !important;
    color: #ffffff !important;
}

.floating-save-bar .floating-bar-title {
    color: #0f172a !important;
}
.floating-save-bar .text-muted {
    color: #64748b !important;
}

.floating-save-bar {
    position: fixed;
    bottom: 72px;
    left: calc(50% + 120px);
    transform: translateX(-50%);
    z-index: 1000;
    width: calc(100% - 32px - 240px);
    max-width: 920px;
    background: rgba(255, 255, 255, 0.8) !important;
    backdrop-filter: blur(12px) !important;
    -webkit-backdrop-filter: blur(12px) !important;
    border: 1px solid rgba(0, 0, 0, 0.08) !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06) !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
html[data-admin-theme="dark"] .floating-save-bar {
    background: rgba(15, 23, 42, 0.8) !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3) !important;
}
html[data-admin-theme="dark"] .floating-save-bar .floating-bar-title {
    color: #ffffff !important;
}
html[data-admin-theme="dark"] .floating-save-bar .text-muted {
    color: #94a3b8 !important;
}
html[data-admin-theme="dark"] .floating-save-bar .btn-outline-light {
    border: 1.5px solid rgba(255, 255, 255, 0.3) !important;
    background: transparent !important;
    color: #ffffff !important;
}
html[data-admin-theme="dark"] .floating-save-bar .btn-outline-light:hover {
    background: rgba(255, 255, 255, 0.1) !important;
    border-color: rgba(255, 255, 255, 0.4) !important;
    color: #ffffff !important;
}

@media (max-width: 991px) {
    .floating-save-bar {
        left: 50% !important;
        width: calc(100% - 32px) !important;
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
    const inputName = document.getElementsByName('name')[0];
    const floatTitle = document.getElementById('floating-category-title');
    
    if (inputName && floatTitle) {
        inputName.addEventListener('input', function() {
            floatTitle.innerText = inputName.value.trim() || 'Category Name';
        });
    }
});
</script>
@endsection