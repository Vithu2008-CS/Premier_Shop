{{--
    admin/roles/create.blade.php — Create role form
    =================================================
    Fields: name (slug), display_name, is_staff toggle, permissions checkboxes
    grouped by permission.group column.
    POST → admin.roles.store → RoleController::store()
    Variable: $permissions (grouped)
--}}
@extends('layouts.admin_noble')
@section('title', 'Create Role')

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
    <li class="breadcrumb-item active" aria-current="page">Create New Role</li>
  </ol>
</nav>

<form action="{{ route('admin.roles.store') }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Role Definition</h6>
                    <div class="mb-3">
                        <label class="form-label">Role Slug <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="e.g. manager" value="{{ old('name') }}" required pattern="[a-z_]+">
                        <small class="text-muted d-block mt-1">Lowercase and underscores only.</small>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Display Name <span class="text-danger">*</span></label>
                        <input type="text" name="display_name" class="form-control @error('display_name') is-invalid @enderror" placeholder="e.g. Area Manager" value="{{ old('display_name') }}" required>
                        @error('display_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="What can this role do?">{{ old('description') }}</textarea>
                    </div>
                    <div class="form-group mb-4">
                        <div class="form-check form-switch card-title mb-0">
                            <input type="checkbox" class="form-check-input" name="is_staff" id="isStaff" value="1" {{ old('is_staff') ? 'checked' : '' }}>
                            <label class="form-check-label" for="isStaff">Staff / Admin Access</label>
                        </div>
                        <p class="text-muted small mt-2">Allows login to the administrative dashboard.</p>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i data-feather="plus-circle" class="icon-sm mr-2"></i> Create Role
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 border-bottom pb-3">
                        <h6 class="card-title mb-0">Assign Privileges</h6>
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
                                        <label class="form-check-label">
                                            <input type="checkbox" name="permissions[]" class="form-check-input perm-{{ Str::slug($group) }}" value="{{ $perm->id }}" {{ in_array($perm->id, old('permissions', [])) ? 'checked' : '' }}>
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
