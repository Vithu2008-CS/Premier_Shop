@extends('layouts.admin_noble')
@section('title', 'Edit Slider')

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.sliders.index') }}">Sliders</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit Banner</li>
  </ol>
</nav>

<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h6 class="card-title">Update Banner Information</h6>
        
        <form action="{{ route('admin.sliders.update', $slider) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Banner Title</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $slider->title) }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Banner Subtitle</label>
                    <input type="text" name="subtitle" class="form-control" value="{{ old('subtitle', $slider->subtitle) }}">
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-12">
                     <label class="form-label d-block text-muted small font-weight-bold">Current Banner Preview</label>
                     <div class="p-2 border rounded bg-light text-center">
                        <img src="{{ (str_starts_with($slider->image_path, 'http') || str_starts_with($slider->image_path, 'data:')) ? $slider->image_path : asset('storage/' . $slider->image_path) }}" 
                             class="img-fluid rounded" style="max-height: 200px; object-fit: contain;" alt="Banner Preview">
                     </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Update Banner Image</label>
                    <input type="file" name="image_file" class="form-control" accept="image/*">
                    <small class="text-muted d-block mt-2">Leave blank to keep existing image.</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Or Update Image URL</label>
                    <input type="url" name="image_link" class="form-control" value="{{ str_contains($slider->image_path, 'http') ? $slider->image_path : '' }}">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Target Destination (URL)</label>
                    <input type="url" name="link_url" class="form-control" value="{{ old('link_url', $slider->link_url) }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Call to Action Button Text</label>
                    <input type="text" name="button_text" class="form-control" value="{{ old('button_text', $slider->button_text) }}">
                </div>
            </div>

            <div class="row items-center">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Display Order (Priority)</label>
                    <input type="number" name="order_priority" class="form-control" value="{{ old('order_priority', $slider->order_priority) }}">
                </div>
                <div class="col-md-4 mb-3 d-flex align-items-end">
                    <div class="form-check mb-2">
                        <label class="form-check-label">
                            <input type="checkbox" name="is_active" class="form-check-input" value="1" {{ $slider->is_active ? 'checked' : '' }}>
                            Visible on Frontend
                        </label>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary mr-2">
                    <i data-feather="save" class="icon-sm mr-2"></i> Update Banner
                </button>
                <a href="{{ route('admin.sliders.index') }}" class="btn btn-outline-secondary">
                    Cancel
                </a>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
