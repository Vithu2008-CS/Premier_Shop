@extends('layouts.admin')
@section('title', 'Edit Slider — Admin Dashboard')

@section('content')
    <div class="admin-topbar">
        <div>
            <h2>Edit Slider</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.sliders.index') }}">Sliders</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.sliders.index') }}" class="btn btn-admin-outline"><i class="bi bi-arrow-left me-1"></i>
            Back</a>
    </div>

    <div class="admin-card">
        <div class="card-title">Slider Information</div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.sliders.update', $slider) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $slider->title) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Subtitle</label>
                    <input type="text" name="subtitle" class="form-control" value="{{ old('subtitle', $slider->subtitle) }}">
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Upload Image (Leave empty to keep current)</label>
                    <input type="file" name="image_file" class="form-control" accept="image/*">
                    <small class="d-block mt-2">Current Image: <a href="{{ (str_starts_with($slider->image_path, 'http') || str_starts_with($slider->image_path, 'data:')) ? $slider->image_path : asset('storage/' . $slider->image_path) }}" target="_blank">View image</a></small>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Or Image URL</label>
                    <input type="url" name="image_link" class="form-control" value="{{ str_contains($slider->image_path, 'http') ? $slider->image_path : '' }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Target Link (URL)</label>
                    <input type="url" name="link_url" class="form-control" value="{{ old('link_url', $slider->link_url) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Button Text</label>
                    <input type="text" name="button_text" class="form-control" value="{{ old('button_text', $slider->button_text) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Order Priority</label>
                    <input type="number" name="order_priority" class="form-control" value="{{ old('order_priority', $slider->order_priority) }}">
                </div>

                <div class="col-md-6 d-flex align-items-center mt-auto">
                    <div class="form-check form-switch mt-4">
                        <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="isActive" {{ $slider->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="isActive">Active</label>
                    </div>
                </div>

                <div class="col-12 text-end mt-4">
                    <button type="submit" class="btn btn-admin"><i class="bi bi-check2-circle me-1"></i> Update
                        Slider</button>
                </div>
            </div>
        </form>
    </div>
@endsection
