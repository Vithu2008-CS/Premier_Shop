{{--
    admin/categories/index.blade.php — Category management list
    =============================================================
    Table of all categories: image, name, slug, product count, actions (edit, delete).
    Variable: $categories (with products_count)
--}}
@extends('layouts.admin_noble')
@section('title', 'Categories')

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item active" aria-current="page">Categories</li>
  </ol>
</nav>

<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h6 class="card-title mb-0">Category Management</h6>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-icon-text">
                <i class="btn-icon-prepend" data-feather="plus"></i>
                Add Category
            </a>
        </div>
        
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Image</th>
                <th>Category Name</th>
                <th>Products Count</th>
                <th class="text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($categories as $category)
                <tr class="category-row-btn align-middle" onclick="window.location='{{ route('admin.categories.edit', $category) }}'">
                  <td>
                    @if($category->image)
                        <img src="{{ $category->image }}" class="wd-40 h-40 rounded" style="object-fit: cover;" alt="category">
                    @else
                        <div class="wd-40 h-40 rounded bg-light-primary d-flex align-items-center justify-content-center">
                            <i data-feather="tag" class="text-primary icon-sm"></i>
                        </div>
                    @endif
                  </td>
                  <td>
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="font-weight-bold d-block text-primary-hover mb-1" style="font-size: 0.95rem; transition: color 0.15s ease;">{{ $category->name }}</span>
                            @if($category->description)
                                <small class="text-muted d-block mt-0.5">{{ Str::limit($category->description, 80) }}</small>
                            @endif
                        </div>
                        <span class="btn btn-xs btn-outline-primary rounded-pill opacity-0 hover-show-btn px-2.5 py-1" style="pointer-events: none; transition: all 0.2s ease; margin-left: 15px;">
                            <i data-feather="edit-2" class="wd-10 h-10 mr-1" style="width: 11px; height: 11px;"></i> Edit
                        </span>
                    </div>
                  </td>
                  <td>
                    <span class="badge badge-light-info">{{ $category->products_count }} Products</span>
                  </td>
                  <td class="text-right" onclick="event.stopPropagation();">
                    <div class="dropdown">
                        <button class="btn btn-link p-0" type="button" id="drop-{{ $category->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <i class="icon-lg text-muted pb-3px" data-feather="more-horizontal"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="drop-{{ $category->id }}">
                          <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.categories.edit', $category) }}">
                              <i data-feather="edit-2" class="icon-sm mr-2"></i> Edit
                          </a>
                          <div class="dropdown-divider"></div>
                          <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Delete this category?');">
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
                  <td colspan="4" class="text-center py-5 text-muted">No categories found.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        @if($categories->hasPages())
          <div class="mt-4">
              {{ $categories->links('pagination::bootstrap-4') }}
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<style>
/* Custom Premium Sibling Row buttons styling */
.category-row-btn {
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.category-row-btn:hover {
    background-color: rgba(108, 92, 231, 0.04) !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
}
html[data-admin-theme="dark"] .category-row-btn:hover {
    background-color: rgba(167, 139, 250, 0.05) !important;
}
.category-row-btn:hover .text-primary-hover {
    color: #6c5ce7 !important;
}
html[data-admin-theme="dark"] .category-row-btn:hover .text-primary-hover {
    color: #a78bfa !important;
}
.category-row-btn:hover .hover-show-btn {
    opacity: 1 !important;
}
.hover-show-btn {
    opacity: 0;
    transition: opacity 0.2s ease;
}
</style>
@endsection