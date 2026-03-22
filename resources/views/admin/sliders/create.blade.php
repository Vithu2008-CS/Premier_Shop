@extends('layouts.admin_noble')
@section('title', 'Add Slider')

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.sliders.index') }}">Sliders</a></li>
    <li class="breadcrumb-item active" aria-current="page">Add New</li>
  </ol>
</nav>

<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h6 class="card-title">Banner Information</h6>
        
        <form action="{{ route('admin.sliders.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Banner Title</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. Summer Sale 2026" value="{{ old('title') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Banner Subtitle</label>
                    <input type="text" name="subtitle" class="form-control" placeholder="e.g. Up to 50% off on all items" value="{{ old('subtitle') }}">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Upload High-Res Image</label>
                    <input type="file" name="image_file" class="form-control" accept="image/*">
                    <small class="text-muted d-block mt-2">Recommended: 1920x800px for best quality.</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Or External Image URL</label>
                    <input type="url" name="image_link" class="form-control" placeholder="https://example.com/banner.jpg" value="{{ old('image_link') }}">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Target Destination (URL)</label>
                    <input type="url" name="link_url" class="form-control" placeholder="{{ url('/shop') }}" value="{{ old('link_url') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Call to Action Button Text</label>
                    <input type="text" name="button_text" class="form-control" placeholder="e.g. Shop Now" value="{{ old('button_text') }}">
                </div>
            </div>

            <div class="row items-center">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Display Order (Priority)</label>
                    <input type="number" name="order_priority" class="form-control" value="{{ old('order_priority', 0) }}">
                </div>
                <div class="col-md-4 mb-3 d-flex align-items-end">
                    <div class="form-check mb-2">
                        <label class="form-check-label">
                            <input type="checkbox" name="is_active" class="form-check-input" value="1" checked>
                            Visible on Frontend
                        </label>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary mr-2">
                    <i data-feather="check-circle" class="icon-sm mr-2"></i> Save Banner
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
