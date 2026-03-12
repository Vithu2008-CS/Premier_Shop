@extends('layouts.admin')
@section('title', 'Roles — Admin Dashboard')

@section('content')
<div class="admin-topbar">
    <div>
        <h2>Roles & Permissions</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Roles</li>
            </ol>
        </nav>
    </div>
    @if(auth()->user()->hasPermission('roles.create'))
    <a href="{{ route('admin.roles.create') }}" class="btn btn-admin"><i class="bi bi-plus-lg me-2"></i>New Role</a>
    @endif
</div>

<div class="admin-card">
    <div class="table-responsive">
        <table class="table admin-table mb-0">
            <thead>
                <tr>
                    <th>Role</th>
                    <th>Description</th>
                    <th>Staff Access</th>
                    <th>Users</th>
                    <th>Permissions</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $role)
                <tr>
                    <td>
                        <div class="fw-bold">{{ $role->display_name }}</div>
                        <small class="text-muted">{{ $role->name }}</small>
                    </td>
                    <td><small class="text-muted">{{ $role->description }}</small></td>
                    <td>
                        @if($role->is_staff)
                            <span class="badge bg-success">Yes</span>
                        @else
                            <span class="badge bg-secondary">No</span>
                        @endif
                    </td>
                    <td><span class="badge bg-primary">{{ $role->users_count }}</span></td>
                    <td><span class="badge bg-info">{{ $role->permissions_count }}</span></td>
                    <td class="text-end">
                        @if(auth()->user()->hasPermission('roles.update'))
                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn-icon" title="Edit"><i class="bi bi-pencil"></i></a>
                        @endif
                        @if(!in_array($role->name, ['admin', 'customer']) && auth()->user()->hasPermission('roles.delete'))
                        <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-icon btn-icon-danger" title="Delete"><i class="bi bi-trash"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
