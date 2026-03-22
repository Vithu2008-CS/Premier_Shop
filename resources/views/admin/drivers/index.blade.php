@extends('layouts.admin_noble')
@section('title', 'Driver Monitoring')

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item active" aria-current="page">Drivers</li>
  </ol>
</nav>

<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h6 class="card-title mb-0">Driver Monitoring</h6>
            <a href="{{ route('admin.drivers.create') }}" class="btn btn-primary btn-icon-text">
                <i class="btn-icon-prepend" data-feather="user-plus"></i>
                Add New Driver
            </a>
        </div>
        
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Driver Name</th>
                <th>Duty Status</th>
                <th>Active Workload</th>
                <th>Date Joined</th>
                <th class="text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($drivers as $driver)
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                        <div class="wd-30 h-30 rounded-circle bg-light-warning d-flex align-items-center justify-content-center mr-2 text-warning font-weight-bold" style="font-size: 0.7rem;">
                            {{ substr($driver->name, 0, 1) }}
                        </div>
                        {{ $driver->name }}
                    </div>
                  </td>
                  <td>
                    @if($driver->is_on_duty)
                        <span class="badge badge-success">ON DUTY</span>
                    @else
                        <span class="badge badge-light">OFF DUTY</span>
                    @endif
                  </td>
                  <td>
                    <div class="d-flex align-items-center">
                        <span class="font-weight-bold mr-2">{{ $driver->processing_orders_count }}</span>
                        <span class="text-muted small">active orders</span>
                    </div>
                  </td>
                  <td class="text-muted small">{{ $driver->created_at->format('M d, Y') }}</td>
                  <td class="text-right">
                    <div class="dropdown">
                        <button class="btn btn-link p-0" type="button" id="dropDriver-{{ $driver->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <i class="icon-lg text-muted pb-3px" data-feather="more-horizontal"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropDriver-{{ $driver->id }}">
                          <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.drivers.edit', $driver) }}">
                              <i data-feather="edit-2" class="icon-sm mr-2"></i> Edit Account
                          </a>
                          <div class="dropdown-divider"></div>
                          <form action="{{ route('admin.drivers.destroy', $driver) }}" method="POST" onsubmit="return confirm('Truly remove this driver account?');">
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
                  <td colspan="5" class="text-center py-5 text-muted">No drivers registered yet.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
