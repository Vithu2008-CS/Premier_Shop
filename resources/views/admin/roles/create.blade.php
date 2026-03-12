@extends('layouts.admin')
@section('title', 'Create Role — Admin Dashboard')

@section('content')
<div class="admin-topbar">
    <div>
        <h2>Create New Role</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ol>
        </nav>
    </div>
</div>

<form action="{{ route('admin.roles.store') }}" method="POST">
    @csrf
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="admin-card">
                <h5 class="card-title mb-4"><i class="bi bi-shield-lock me-2"></i>Role Details</h5>

                <div class="mb-3">
                    <label class="form-label">Role Name (slug)</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. warehouse_staff" value="{{ old('name') }}" required pattern="[a-z_]+">
                    <small class="text-muted">Lowercase letters and underscores only</small>
                    @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Display Name</label>
                    <input type="text" name="display_name" class="form-control" placeholder="e.g. Warehouse Staff" value="{{ old('display_name') }}" required>
                    @error('display_name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Brief description of this role">{{ old('description') }}</textarea>
                </div>

                <div class="form-check form-switch mb-4">
                    <input type="hidden" name="is_staff" value="0">
                    <input class="form-check-input" type="checkbox" name="is_staff" value="1" id="isStaff" {{ old('is_staff') ? 'checked' : '' }}>
                    <label class="form-check-label" for="isStaff">
                        <strong>Admin Panel Access</strong>
                        <div class="text-muted small">Enable this to allow users with this role to access the admin panel</div>
                    </label>
                </div>

                <button type="submit" class="btn btn-admin w-100"><i class="bi bi-save me-2"></i>Create Role</button>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="admin-card">
                <h5 class="card-title mb-4"><i class="bi bi-key me-2"></i>Permissions</h5>
                
                @foreach($permissions as $group => $groupPerms)
                    <div class="mb-4">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <h6 class="fw-bold mb-0 text-white">{{ $group }}</h6>
                            <button type="button" class="btn btn-sm btn-admin-outline py-0 px-2" style="font-size:0.7rem;" onclick="toggleGroup(this, '{{ $group }}')">Select All</button>
                        </div>
                        <div class="row g-2">
                            @foreach($groupPerms as $perm)
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input perm-{{ $group }}" type="checkbox" name="permissions[]" value="{{ $perm->id }}" id="perm_{{ $perm->id }}" {{ in_array($perm->id, old('permissions', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="perm_{{ $perm->id }}">{{ $perm->display_name }}</label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
function toggleGroup(btn, group) {
    const checkboxes = document.querySelectorAll('.perm-' + group);
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(cb => cb.checked = !allChecked);
    btn.textContent = allChecked ? 'Select All' : 'Deselect All';
}
</script>
@endpush
