@extends('layouts.admin')
@section('title', $customer->name . ' — Customer')

@section('content')
<div class="admin-topbar">
    <div>
        <h2>{{ $customer->name }}</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}">Customers</a></li>
                <li class="breadcrumb-item active">{{ $customer->name }}</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('admin.customers.index') }}" class="btn btn-admin-outline"><i class="bi bi-arrow-left me-1"></i> Back</a>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="admin-card text-center">
            <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#6C5CE7,#A29BFE);display:flex;align-items:center;justify-content:center;color:#fff;font-size:2rem;font-weight:700;margin:0 auto 16px;">{{ substr($customer->name, 0, 1) }}</div>
            <h4 class="fw-bold mb-1">{{ $customer->name }}</h4>
            <p style="color:var(--admin-muted);margin-bottom:16px;">{{ $customer->email }}</p>
            <hr style="border-color:var(--admin-border);">
            <div class="text-start mt-3">
                <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid var(--admin-border);">
                    <span style="color:var(--admin-muted);"><i class="bi bi-telephone me-2"></i>Phone</span>
                    <span>{{ $customer->phone ?? 'Not provided' }}</span>
                </div>
                <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid var(--admin-border);">
                    <span style="color:var(--admin-muted);"><i class="bi bi-geo-alt me-2"></i>Address</span>
                    <span>{{ $customer->address ?? 'Not provided' }}</span>
                </div>
                <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid var(--admin-border);">
                    <span style="color:var(--admin-muted);"><i class="bi bi-calendar me-2"></i>DOB</span>
                    <span>
                        @if($customer->dob)
                        {{ \Carbon\Carbon::parse($customer->dob)->format('d M Y') }} ({{ $customer->age }} yrs)
                        @else
                        Not provided
                        @endif
                    </span>
                </div>
                <div class="d-flex justify-content-between py-2">
                    <span style="color:var(--admin-muted);"><i class="bi bi-clock me-2"></i>Joined</span>
                    <span>{{ $customer->created_at->format('d M Y') }}</span>
                </div>
                @if($customer->isUnder16())
                <div class="alert alert-warning mt-3 mb-0" style="border-radius:10px;">
                    <i class="bi bi-exclamation-triangle me-1"></i> Under 16 — Cannot purchase age-restricted items
                </div>
                @endif

                {{-- Role Assignment --}}
                <hr style="border-color:var(--admin-border);">
                <div class="mt-3">
                    <label class="form-label"><i class="bi bi-shield-lock me-1"></i>User Role</label>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-primary px-3 py-2">{{ $customer->role?->display_name ?? 'No Role' }}</span>
                    </div>
                    @if(auth()->user()->hasPermission('roles.update'))
                    <form action="{{ route('admin.customers.updateRole', $customer) }}" method="POST">
                        @csrf @method('PATCH')
                        <div class="input-group input-group-sm">
                            <select name="role_id" class="form-select form-select-sm">
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ $customer->role_id == $role->id ? 'selected' : '' }}>{{ $role->display_name }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-admin btn-sm" type="submit">Update</button>
                        </div>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="admin-card">
            <div class="card-title">Order History</div>
            @if($customer->orders->count() > 0)
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customer->orders as $order)
                    <tr>
                        <td><a href="{{ route('admin.orders.show', $order) }}" style="color:#A29BFE;text-decoration:none;font-weight:600;">{{ $order->order_number }}</a></td>
                        <td style="color:var(--admin-muted);">{{ $order->created_at->format('d M Y') }}</td>
                        <td class="fw-bold">£{{ number_format($order->total, 2) }}</td>
                        <td>
                            @php $colors = ['pending'=>'warning','processing'=>'info','shipped'=>'primary','delivered'=>'success','cancelled'=>'danger']; @endphp
                            <span class="badge badge-status bg-{{ $colors[$order->status] ?? 'secondary' }}">{{ ucfirst($order->status) }}</span>
                        </td>
                        <td><a href="{{ route('admin.orders.show', $order) }}" class="btn-icon"><i class="bi bi-eye"></i></a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="text-center py-5" style="color:var(--admin-muted);">
                <i class="bi bi-bag-x" style="font-size:2.5rem;display:block;margin-bottom:10px;"></i>
                No orders yet
            </div>
            @endif
        </div>
    </div>
</div>
@endsection