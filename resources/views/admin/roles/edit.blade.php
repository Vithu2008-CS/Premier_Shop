{{--
    admin/roles/edit.blade.php — Edit role form
    =============================================
    Same fields as create; pre-checked permissions based on $role->permissions.
    PUT → admin.roles.update → RoleController::update()
    Variables: $role (with permissions), $permissions (all, grouped)
--}}
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
                            <input type="checkbox" class="form-check-input" name="is_staff" id="isStaff" value="1" {{ old('is_staff', $role->is_staff) ? 'checked' : '' }} {{ $role->name === 'admin' ? 'disabled' : '' }}>
                            <label class="form-check-label" for="isStaff">Staff / Admin Access</label>
                        </div>
                        <p class="text-muted small mt-2">
                            Allows login to the administrative dashboard.
                            @if($role->name === 'admin')
                                <span class="d-block text-warning">Locked for the admin role.</span>
                            @endif
                        </p>
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
                    <div class="mb-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 border-bottom pb-3">
                        <h6 class="card-title mb-0">Manage Privileges</h6>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2.5" data-call="toggleAllPrivileges" data-args="[true]">Select All</button>
                            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2.5" data-call="toggleAllPrivileges" data-args="[false]">Clear All</button>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <input type="text" id="permissionSearch" class="form-control" placeholder="🔍 Search privileges by name or module..." style="border-radius: 12px; padding: 10px 15px;">
                    </div>
                    
                    @foreach($permissions as $group => $groupPerms)
                        <div class="mb-4 permission-group-block">
                            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                                <h6 class="mb-0 text-muted font-weight-bold text-uppercase tx-12">{{ $group }}</h6>
                                <button type="button" class="btn btn-xs btn-outline-light text-primary" data-call="toggleGroup" data-args="[&quot;$el&quot;, &quot;{{ Str::slug($group) }}&quot;]">Select Group</button>
                            </div>
                            <div class="row">
                                @foreach($groupPerms as $perm)
                                <div class="col-md-4 mb-2 perm-item">
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
<script nonce="{{ Vite::cspNonce() }}">
function toggleGroup(btn, groupClass) {
    const checkboxes = document.querySelectorAll('.perm-' + groupClass);
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(cb => cb.checked = !allChecked);
    btn.textContent = allChecked ? 'Select Group' : 'Deselect Group';
}

function toggleAllPrivileges(check) {
    const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
    checkboxes.forEach(cb => cb.checked = check);
}

document.getElementById('permissionSearch')?.addEventListener('input', function() {
    const term = this.value.toLowerCase().trim();
    document.querySelectorAll('.permission-group-block').forEach(group => {
        let groupHasMatch = false;
        group.querySelectorAll('.perm-item').forEach(item => {
            const text = item.textContent.toLowerCase();
            const isMatch = text.includes(term);
            item.style.display = isMatch ? 'block' : 'none';
            if (isMatch) groupHasMatch = true;
        });
        group.style.display = (groupHasMatch || term === '') ? 'block' : 'none';
    });
});
</script>
@endpush
