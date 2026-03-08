@extends('layouts.admin')
@section('title', 'Coupons — Admin')

@section('content')
<div class="admin-topbar">
    <div>
        <h2>Coupons</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item active">Coupons</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('admin.coupons.create') }}" class="btn btn-admin"><i class="bi bi-plus-lg me-1"></i> Create Coupon</a>
</div>

<div class="admin-card">
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr><th>Code</th><th>Type</th><th>Value</th><th>Min Order</th><th>Valid Until</th><th>Used</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($coupons as $coupon)
                    <tr>
                        <td>
                            <span style="font-family:monospace;font-weight:700;color:#A29BFE;background:rgba(108,92,231,0.1);padding:4px 10px;border-radius:6px;">{{ $coupon->code }}</span>
                        </td>
                        <td>
                            <span class="badge badge-status" style="background:rgba(0,206,201,0.12);color:#00CEC9;">{{ ucfirst($coupon->discount_type) }}</span>
                        </td>
                        <td class="fw-bold">{{ $coupon->discount_type === 'percentage' ? $coupon->discount_value . '%' : '£' . number_format($coupon->discount_value, 2) }}</td>
                        <td>{{ $coupon->min_order_amount ? '£' . number_format($coupon->min_order_amount, 2) : '—' }}</td>
                        <td style="color:var(--admin-muted);">{{ $coupon->valid_until ? $coupon->valid_until->format('d M Y') : '—' }}</td>
                        <td>{{ $coupon->times_used }}{{ $coupon->usage_limit ? '/' . $coupon->usage_limit : '' }}</td>
                        <td>
                            @if($coupon->is_active)
                                <span class="badge badge-status" style="background:rgba(0,184,148,0.12);color:#00B894;">Active</span>
                            @else
                                <span class="badge badge-status" style="background:rgba(255,255,255,0.05);color:var(--admin-muted);">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn-icon"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">
                                    @csrf @method('DELETE')
                                    <button class="btn-icon btn-icon-danger"><i class="bi bi-trash3"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center py-5" style="color:var(--admin-muted);"><i class="bi bi-tag" style="font-size:2rem;display:block;margin-bottom:8px;"></i>No coupons yet. <a href="{{ route('admin.coupons.create') }}" style="color:#A29BFE;">Create your first coupon</a></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $coupons->links() }}</div>
@endsection
