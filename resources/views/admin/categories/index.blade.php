@extends('layouts.admin')
@section('title', 'Categories — Admin Dashboard')

@section('content')
    <div class="admin-topbar">
        <div>
            <h2>Categories</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Categories</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-admin"><i class="bi bi-plus-lg me-1"></i> Add
            Category</a>
    </div>

    <div class="admin-card">
        <div class="card-title d-flex justify-content-between align-items-center">
            <span>All Categories ({{ $categories->total() }})</span>
        </div>

        <div class="table-responsive">
            <table class="admin-table align-middle">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Products Count</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>
                                @if($category->image)
                                    <img src="{{ $category->image }}" alt="{{ $category->name }}"
                                        style="width: 48px; height: 48px; object-fit: cover; border-radius: 8px;">
                                @else
                                    <div
                                        style="width: 48px; height: 48px; background: rgba(255,255,255,0.05); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: var(--admin-muted);">
                                        <i class="bi bi-tag"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span style="font-weight: 600; color: #fff;">{{ $category->name }}</span>
                                @if($category->description)
                                    <br><small class="text-muted"
                                        style="font-size: 0.75rem;">{{ Str::limit($category->description, 50) }}</small>
                                @endif
                            </td>
                            <td>{{ $category->products_count }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn-icon"
                                        data-bs-toggle="tooltip" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Delete this category?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn-icon btn-icon-danger" data-bs-toggle="tooltip" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">No categories found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($categories->hasPages())
            <div class="mt-4 border-top border-secondary pt-3">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
@endsection