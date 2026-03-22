@extends('layouts.admin_noble')
@section('title', 'Roles')

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item active" aria-current="page">Roles & Permissions</li>
  </ol>
</nav>

<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h6 class="card-title mb-0">Role Management</h6>
            @if(auth()->user()->hasPermission('roles.create'))
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-icon-text">
                <i class="btn-icon-prepend" data-feather="shield"></i>
                Create New Role
            </a>
            @endif
        </div>
        
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Role Identity</th>
                <th>Description</th>
                <th>Staff Access</th>
                <th>Users Count</th>
                <th>Privileges</th>
                <th class="text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($roles as $role)
                <tr>
                  <td>
                    <span class="font-weight-bold d-block text-primary">{{ $role->display_name }}</span>
                    <small class="text-muted">{{ $role->name }}</small>
                  </td>
                  <td class="text-muted small">{{ Str::limit($role->description, 60) }}</td>
                  <td>
                    @if($role->is_staff)
                        <span class="badge badge-success">STAFF</span>
                    @else
                        <span class="badge badge-light">GUEST</span>
                    @endif
                  </td>
                  <td>
                    <span class="badge badge-pill badge-primary">{{ $role->users_count }}</span>
                  </td>
                  <td>
                    <span class="badge badge-outline-info">{{ $role->permissions_count }} Permissions</span>
                  </td>
                  <td class="text-right">
                    <div class="dropdown">
                        <button class="btn btn-link p-0" type="button" id="dropRole-{{ $role->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <i class="icon-lg text-muted pb-3px" data-feather="more-horizontal"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropRole-{{ $role->id }}">
                          @if(auth()->user()->hasPermission('roles.update'))
                          <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.roles.edit', $role) }}">
                              <i data-feather="edit" class="icon-sm mr-2"></i> Edit Permissions
                          </a>
                          @endif
                          
                          @if(!in_array($role->name, ['admin', 'customer']) && auth()->user()->hasPermission('roles.delete'))
                          <div class="dropdown-divider"></div>
                          <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" onsubmit="return confirm('Truly delete this role? Standard roles cannot be deleted.');">
                              @csrf @method('DELETE')
                              <button type="submit" class="dropdown-item d-flex align-items-center text-danger">
                                  <i data-feather="trash-2" class="icon-sm mr-2"></i> Delete Role
                              </button>
                          </form>
                          @endif
                        </div>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
