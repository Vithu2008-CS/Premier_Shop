@extends('layouts.admin')
@section('title', 'Driver Monitoring — Admin')

@section('content')
<div class="admin-topbar">
    <div>
        <h2>Driver Monitoring</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item active">Drivers</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('admin.drivers.create') }}" class="btn btn-admin"><i class="bi bi-plus-circle me-1"></i> Add New Driver</a>
</div>

<div class="admin-card">
    <div class="table-responsive">
        <table class="table admin-table">
            <thead>
                <tr>
                    <th>Driver Name</th>
                    <th>Status</th>
                    <th>Processing Orders</th>
                    <th>Joined</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($drivers as $driver)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:32px;height:32px;border-radius:50%;background:rgba(108,92,231,0.2);display:flex;align-items:center;justify-content:center;color:#A29BFE;font-weight:600;font-size:0.8rem;">
                                {{ substr($driver->name, 0, 1) }}
                            </div>
                            {{ $driver->name }}
                        </div>
                    </td>
                    <td>
                        @if($driver->is_on_duty)
                            <span class="badge bg-success">On Duty</span>
                        @else
                            <span class="badge bg-secondary">Off Duty</span>
                        @endif
                    </td>
                    <td>
                        <span class="fw-bold">{{ $driver->processing_orders_count }}</span> active
                    </td>
                    <td>{{ $driver->created_at->format('M d, Y') }}</td>
                    <td class="text-end">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.drivers.edit', $driver) }}" class="btn btn-sm btn-light" title="Edit Driver">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.drivers.destroy', $driver) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this driver?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light text-danger" title="Delete Driver">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
                @if($drivers->isEmpty())
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No drivers found.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
