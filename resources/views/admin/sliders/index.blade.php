@extends('layouts.admin_noble')
@section('title', 'Sliders')

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item active" aria-current="page">Sliders</li>
  </ol>
</nav>

<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h6 class="card-title mb-0">Homepage Banner Management</h6>
            <a href="{{ route('admin.sliders.create') }}" class="btn btn-primary btn-icon-text">
                <i class="btn-icon-prepend" data-feather="plus"></i>
                Add Slider
            </a>
        </div>
        
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Preview</th>
                <th>Content Details</th>
                <th>Link</th>
                <th>Priority</th>
                <th>Status</th>
                <th class="text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($sliders as $slider)
                <tr>
                  <td>
                    <img src="{{ (str_starts_with($slider->image_path, 'http') || str_starts_with($slider->image_path, 'data:')) ? $slider->image_path : asset('storage/' . $slider->image_path) }}" 
                         class="wd-100 h-50 rounded" style="object-fit: cover;" alt="Banner">
                  </td>
                  <td>
                    <span class="font-weight-bold d-block">{{ $slider->title ?? 'Untitled' }}</span>
                    @if($slider->subtitle)
                        <small class="text-muted">{{ Str::limit($slider->subtitle, 50) }}</small>
                    @endif
                  </td>
                  <td>
                    @if($slider->link_url)
                        <a href="{{ $slider->link_url }}" target="_blank" class="badge badge-light-primary">
                            <i data-feather="link" class="icon-xs mr-1"></i> Visit Link
                        </a>
                    @else
                        <span class="text-muted small">None</span>
                    @endif
                  </td>
                  <td>
                    <span class="badge badge-outline-secondary">{{ $slider->order_priority }}</span>
                  </td>
                  <td>
                    @if($slider->is_active)
                        <span class="badge badge-success">ACTIVE</span>
                    @else
                        <span class="badge badge-light">INACTIVE</span>
                    @endif
                  </td>
                  <td class="text-right">
                    <div class="dropdown">
                        <button class="btn btn-link p-0" type="button" id="dropSlider-{{ $slider->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <i class="icon-lg text-muted pb-3px" data-feather="more-horizontal"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropSlider-{{ $slider->id }}">
                          <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.sliders.edit', $slider) }}">
                              <i data-feather="edit-2" class="icon-sm mr-2"></i> Edit Banner
                          </a>
                          <div class="dropdown-divider"></div>
                          <form action="{{ route('admin.sliders.destroy', $slider) }}" method="POST" onsubmit="return confirm('Truly delete this banner?');">
                              @csrf @method('DELETE')
                              <button type="submit" class="dropdown-item d-flex align-items-center text-danger">
                                  <i data-feather="trash-2" class="icon-sm mr-2"></i> Delete
                              </button>
                          </form>
                        </div>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center py-5 text-muted">No slider banners found.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
