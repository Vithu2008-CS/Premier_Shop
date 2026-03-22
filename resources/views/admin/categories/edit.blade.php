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
        
        <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
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

            <div class="mt-4">
                <button type="submit" class="btn btn-primary mr-2">
                    <i data-feather="upload" class="icon-sm mr-2"></i> Update Category
                </button>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                    Cancel
                </a>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection