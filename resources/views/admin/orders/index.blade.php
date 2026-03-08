@extends('layouts.admin')
@section('title', 'Orders — Admin')

@section('content')
<div class="admin-topbar">
    <div>
        <h2>Orders</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item active">Orders</li>
            </ol>
        </nav>
    </div>
</div>

<div class="admin-card">
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr><th>Order #</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td><a href="{{ route('admin.orders.show', $order) }}" style="color:#A29BFE;text-decoration:none;font-weight:600;">{{ $order->order_number }}</a></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,#6C5CE7,#A29BFE);display:flex;align-items:center;justify-content:center;color:#fff;font-size:0.7rem;font-weight:700;">{{ substr($order->user->name, 0, 1) }}</div>
                                {{ $order->user->name }}
                            </div>
                        </td>
                        <td class="fw-bold">£{{ number_format($order->total, 2) }}</td>
                        <td>
                            <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                <select name="status" class="form-select form-select-sm d-inline" style="width:auto;background:rgba(255,255,255,0.05);border-color:var(--admin-border);color:#fff;border-radius:8px;" onchange="this.form.submit()">
                                    @foreach(['pending','processing','shipped','delivered','cancelled'] as $s)
                                        <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </td>
                        <td style="color:var(--admin-muted);">{{ $order->created_at->format('d M Y') }}</td>
                        <td><a href="{{ route('admin.orders.show', $order) }}" class="btn-icon"><i class="bi bi-eye"></i></a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-5" style="color:var(--admin-muted);"><i class="bi bi-receipt" style="font-size:2rem;display:block;margin-bottom:8px;"></i>No orders yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $orders->links() }}</div>
@endsection
