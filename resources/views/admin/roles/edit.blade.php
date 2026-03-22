@extends('layouts.admin_noble')
@section('title', 'Edit Role: ' . $role->display_name)

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit: {{ $role->display_name }}</li>
  </ol>
</nav>

<form action="{{ route('admin.roles.update', $role) }}" method="POST">
    @csrf @method('PUT')
    <div class="row">
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Role Configuration</h6>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted">Role Slug (Locked)</label>
                        <input type="text" class="form-control bg-light" value="{{ $role->name }}" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Display Name <span class="text-danger">*</span></label>
                        <input type="text" name="display_name" class="form-control @error('display_name') is-invalid @enderror" value="{{ old('display_name', $role->display_name) }}" required>
                        @error('display_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $role->description) }}</textarea>
                    </div>

                    <div class="form-group mb-4">
                        <div class="form-check form-switch card-title mb-0">
                            <input type="checkbox" class="form-check-input" name="is_staff" id="isStaff" value="1" {{ old('is_staff', $role->is_staff) ? 'checked' : '' }}>
                            <label class="form-check-label" for="isStaff">Staff / Admin Access</label>
                        </div>
                        <p class="text-muted small mt-2">Allows login to the administrative dashboard.</p>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <i data-feather="save" class="icon-sm mr-2"></i> Update Role
                    </button>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary btn-block mt-2">Cancel</a>
                </div>
            </div>
        </div>

        <div class="col-md-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Manage Privileges</h6>
                    
                    @foreach($permissions as $group => $groupPerms)
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                                <h6 class="mb-0 text-muted font-weight-bold text-uppercase tx-12">{{ $group }}</h6>
                                <button type="button" class="btn btn-xs btn-outline-light text-primary" onclick="toggleGroup(this, '{{ Str::slug($group) }}')">Select All</button>
                            </div>
                            <div class="row">
                                @foreach($groupPerms as $perm)
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <label class="form-check-label text-{{ in_array($perm->id, $rolePermissions) ? 'primary font-weight-bold' : 'muted' }}">
                                            <input type="checkbox" name="permissions[]" class="form-check-input perm-{{ Str::slug($group) }}" value="{{ $perm->id }}" {{ in_array($perm->id, old('permissions', $rolePermissions)) ? 'checked' : '' }}>
                                            {{ $perm->display_name }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
function toggleGroup(btn, groupClass) {
    const checkboxes = document.querySelectorAll('.perm-' + groupClass);
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(cb => cb.checked = !allChecked);
    btn.textContent = allChecked ? 'Select All' : 'Deselect All';
}
</script>
@endpush
