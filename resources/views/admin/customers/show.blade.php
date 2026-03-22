@extends('layouts.admin_noble')
@section('title', 'Customer: ' . $customer->name)

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}">Customers</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $customer->name }}</li>
  </ol>
</nav>

<div class="row profile-body">
    {{-- Left Sidebar: Profile Details --}}
    <div class="col-md-4 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-column align-items-center text-center">
                    <div class="wd-100 h-100 rounded-circle bg-light-primary d-flex align-items-center justify-content-center mb-3 text-primary font-weight-bold" style="font-size: 2.5rem;">
                        {{ substr($customer->name, 0, 1) }}
                    </div>
                    <h5 class="mb-0">{{ $customer->name }}</h5>
                    <p class="text-muted small mb-3">{{ $customer->email }}</p>
                    <span class="badge badge-pill badge-primary px-3 mb-4">{{ $customer->role?->display_name ?? 'Basic Customer' }}</span>
                </div>

                <div class="mt-2">
                    <h6 class="card-title mb-3">Contact Information</h6>
                    <div class="mb-3">
                        <label class="tx-11 font-weight-bold mb-0 text-uppercase d-block mb-1">Phone:</label>
                        <p class="text-muted">{{ $customer->phone ?? 'Not provided' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="tx-11 font-weight-bold mb-0 text-uppercase d-block mb-1">Address:</label>
                        <p class="text-muted small">{{ $customer->address ?? 'No address on file' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="tx-11 font-weight-bold mb-0 text-uppercase d-block mb-1">Date of Birth:</label>
                        <p class="text-muted">
                            @if($customer->dob)
                                {{ \Carbon\Carbon::parse($customer->dob)->format('d M Y') }} 
                                <span class="badge badge-light-secondary ml-1">{{ $customer->age }} yrs</span>
                            @else
                                <span class="italic small text-muted">Not provided</span>
                            @endif
                        </p>
                    </div>
                    <div class="mb-0">
                        <label class="tx-11 font-weight-bold mb-0 text-uppercase d-block mb-1">Member Since:</label>
                        <p class="text-muted">{{ $customer->created_at->format('M d, Y') }}</p>
                    </div>
                </div>

                @if($customer->isUnder16())
                <div class="alert alert-warning mt-4 mb-0 d-flex align-items-center">
                    <i data-feather="alert-triangle" class="icon-sm mr-2 text-warning"></i>
                    <span class="small font-weight-bold">Under 16 Restriction</span>
                </div>
                @endif

                @if(auth()->user()->hasPermission('roles.update'))
                <hr class="my-4">
                <h6 class="card-title mb-3">Manage Permissions</h6>
                <form action="{{ route('admin.customers.updateRole', $customer) }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="form-group mb-2">
                        <select name="role_id" class="form-control form-control-sm">
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ $customer->role_id == $role->id ? 'selected' : '' }}>{{ $role->display_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-outline-primary btn-sm btn-block">Update Role</button>
                </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Right Content: Order History --}}
    <div class="col-md-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Order History</h6>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Date</th>
                                <th>Items</th>
                                <th>Status</th>
                                <th class="text-right">Total</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customer->orders->take(10) as $order)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order) }}" class="font-weight-bold text-primary">
                                        {{ $order->order_number }}
                                    </a>
                                </td>
                                <td class="text-muted small">{{ $order->created_at->format('M d, Y') }}</td>
                                <td>{{ $order->items_count ?? $order->items->count() }} Items</td>
                                <td>
                                    @php $colors = ['pending'=>'warning','processing'=>'info','shipped'=>'primary','delivered'=>'success','cancelled'=>'danger']; @endphp
                                    <span class="badge badge-{{ $colors[$order->status] ?? 'secondary' }} py-1">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="text-right font-weight-bold">£{{ number_format($order->total, 2) }}</td>
                                <td class="text-right">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-outline-light btn-icon btn-sm" title="View Order">
                                        <i data-feather="eye" class="text-muted"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <p>No orders recorded for this customer yet.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($customer->orders->count() > 10)
                    <div class="text-center mt-3">
                        <p class="small text-muted">Showing last 10 orders only.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection