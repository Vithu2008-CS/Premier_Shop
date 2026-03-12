@extends('layouts.admin')
@section('title', 'Customers — Admin')

@section('content')
<div class="admin-topbar">
    <div>
        <h2>Customers</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item active">Customers</li>
            </ol>
        </nav>
    </div>
    <span class="badge badge-status bg-gradient-primary" style="font-size:0.85rem;">{{ $customers->total() }} total</span>
</div>

<div class="admin-card">
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr><th>Customer</th><th>Email</th><th>Role</th><th>Phone</th><th>Age</th><th>Orders</th><th>Joined</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#00B894,#00CEC9);display:flex;align-items:center;justify-content:center;color:#fff;font-size:0.75rem;font-weight:700;">{{ substr($customer->name, 0, 1) }}</div>
                                <div>
                                    <div class="fw-bold" style="color:#fff;">{{ $customer->name }}</div>
                                    @if($customer->isUnder16()) <small class="text-warning">Under 16</small> @endif
                                </div>
                            </div>
                        </td>
                        <td style="color:var(--admin-muted);">{{ $customer->email }}</td>
                        <td>
                            <span class="badge badge-status" style="background:rgba(108,92,231,0.15);color:#A29BFE;">{{ $customer->role?->display_name ?? '—' }}</span>
                        </td>
                        <td>{{ $customer->phone ?? '—' }}</td>
                        <td>
                            @if($customer->dob)
                                {{ $customer->age }} yrs
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-status" style="background:rgba({{ $customer->orders_count > 0 ? '0,184,148' : '255,255,255' }},0.12);color:{{ $customer->orders_count > 0 ? '#00B894' : 'var(--admin-muted)' }};">{{ $customer->orders_count }}</span>
                        </td>
                        <td style="color:var(--admin-muted);font-size:0.85rem;">{{ $customer->created_at->format('d M Y') }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.customers.show', $customer) }}" class="btn-icon"><i class="bi bi-eye"></i></a>
                                <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this customer?')">
                                    @csrf @method('DELETE')
                                    <button class="btn-icon btn-icon-danger"><i class="bi bi-trash3"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center py-5" style="color:var(--admin-muted);"><i class="bi bi-people" style="font-size:2rem;display:block;margin-bottom:8px;"></i>No customers yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $customers->links() }}</div>
@endsection
