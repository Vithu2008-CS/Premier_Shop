@extends('layouts.admin')
@section('title', 'Sliders — Admin Dashboard')

@section('content')
    <div class="admin-topbar">
        <div>
            <h2>Sliders</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Sliders</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.sliders.create') }}" class="btn btn-admin"><i class="bi bi-plus-lg me-1"></i> Add
            Slider</a>
    </div>

    <div class="admin-card">
        <div class="card-title">Manage Sliders</div>

        <div class="table-responsive">
            <table class="admin-table align-middle m-0">
                <thead>
                    <tr>
                        <th width="80">Image</th>
                        <th>Title / Subtitle</th>
                        <th>Link URL</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th width="120" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sliders as $slider)
                        <tr>
                            <td>
                                <img src="{{ (str_starts_with($slider->image_path, 'http') || str_starts_with($slider->image_path, 'data:')) ? $slider->image_path : asset('storage/' . $slider->image_path) }}" alt="Slider"
                                    style="width:80px;height:40px;object-fit:cover;border-radius:6px;background:rgba(255,255,255,0.05);">
                            </td>
                            <td>
                                <strong>{{ $slider->title ?? 'N/A' }}</strong><br>
                                <small class="text-muted">{{ $slider->subtitle }}</small>
                            </td>
                            <td>
                                @if($slider->link_url)
                                    <a href="{{ $slider->link_url }}" target="_blank"
                                        class="text-primary text-decoration-none">
                                        <i class="bi bi-link-45deg"></i> Link
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $slider->order_priority }}</td>
                            <td>
                                @if($slider->is_active)
                                    <span class="badge bg-success badge-status">Active</span>
                                @else
                                    <span class="badge bg-secondary badge-status">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.sliders.edit', $slider) }}" class="btn-icon" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.sliders.destroy', $slider) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('Delete this slider?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-icon btn-icon-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No sliders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
