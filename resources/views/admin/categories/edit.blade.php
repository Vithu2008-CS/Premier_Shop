@extends('layouts.admin')
@section('title', 'Edit Category — Admin Dashboard')

@section('content')
    <div class="admin-topbar">
        <div>
            <h2>Edit Category</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Categories</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-admin-outline"><i class="bi bi-arrow-left me-1"></i>
            Back</a>
    </div>

    <div class="admin-card">
        <div class="card-title">Category Information</div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">Name *</label>
                    <input type="text" name="name" class="form-control" required placeholder="e.g. Electronics"
                        value="{{ old('name', $category->name) }}">
                </div>

                <div class="col-md-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control"
                        rows="3">{{ old('description', $category->description) }}</textarea>
                </div>

                @if($category->image)
                    <div class="col-md-12 text-center my-3">
                        <img src="{{ $category->image }}" alt="{{ $category->name }}"
                            style="max-height: 150px; border-radius: 8px;">
                        <p class="text-muted mt-2"><small>Current Image</small></p>
                    </div>
                @endif

                <div class="col-md-6">
                    <label class="form-label">Update Image Upload</label>
                    <input type="file" name="image_file" class="form-control" accept="image/*">
                    <small class="text-muted d-block mt-1">Leave empty to keep existing image</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Or Update Image URL</label>
                    <input type="url" name="image_link" class="form-control" placeholder="https://example.com/image.jpg"
                        value="{{ old('image_link', Str::startsWith($category->image, 'http') ? $category->image : '') }}">
                </div>

                <div class="col-12 text-end mt-4">
                    <button type="submit" class="btn btn-admin"><i class="bi bi-check2-circle me-1"></i> Update
                        Category</button>
                </div>
            </div>
        </form>
    </div>
@endsection