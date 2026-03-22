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
                <tr>
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
                    <span class="font-weight-bold d-block">{{ $category->name }}</span>
                    @if($category->description)
                        <small class="text-muted">{{ Str::limit($category->description, 60) }}</small>
                    @endif
                  </td>
                  <td>
                    <span class="badge badge-light-info">{{ $category->products_count }} Products</span>
                  </td>
                  <td class="text-right">
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
@endsection