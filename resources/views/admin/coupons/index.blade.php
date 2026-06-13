{{--
    admin/coupons/index.blade.php — Coupon management list
    =======================================================
    Table of all coupons: code, type (percentage/fixed), value, usage stats, expiry, status.
    Toggle active/inactive inline. Links to create and edit.
    Variable: $coupons (paginated)
--}}
@extends('layouts.admin_noble')
@section('title', 'Coupon Management')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap');

.container-fluid { font-family: 'Inter', sans-serif; }
.rounded-4 { border-radius: 18px !important; }
.rounded-3 { border-radius: 12px !important; }

/* Soft badges */
.bg-soft-primary   { background: rgba(116, 48, 137,0.1) !important; color: #743089 !important; }
.bg-soft-success   { background: rgba(16,185,129,0.1) !important; color: #10b981 !important; }
.bg-soft-secondary { background: rgba(100,116,139,0.1) !important; color: #64748b !important; }
.bg-soft-info      { background: rgba(6,182,212,0.1) !important; color: #06b6d4 !important; }
.bg-soft-danger    { background: rgba(239,68,68,0.1) !important; color: #ef4444 !important; }
html[data-admin-theme="dark"] .bg-soft-primary   { background: rgba(167,139,250,0.15) !important; color: #a78bfa !important; }
html[data-admin-theme="dark"] .bg-soft-success   { background: rgba(52,211,153,0.15) !important; color: #34d399 !important; }
html[data-admin-theme="dark"] .bg-soft-secondary { background: rgba(148,163,184,0.15) !important; color: #94a3b8 !important; }
html[data-admin-theme="dark"] .bg-soft-info      { background: rgba(34,211,238,0.15) !important; color: #22d3ee !important; }
html[data-admin-theme="dark"] .bg-soft-danger    { background: rgba(248,113,113,0.15) !important; color: #f87171 !important; }

/* Themed cards */
html[data-admin-theme="light"] .theme-card-bg { background-color: #ffffff !important; }
html[data-admin-theme="dark"]  .theme-card-bg { background-color: #0c1427 !important; border: 1px solid rgba(255,255,255,0.05) !important; }
html[data-admin-theme="light"] .text-theme-dark-bold { color: #1e293b !important; }
html[data-admin-theme="dark"]  .text-theme-dark-bold { color: #f1f5f9 !important; }

/* Curved Buttons */
.btn-curved {
    border-radius: 30px !important;
    padding: 8px 22px !important;
    font-size: 0.82rem !important;
    font-weight: 700 !important;
    font-family: 'Outfit', sans-serif !important;
    background: linear-gradient(135deg,#743089,#a78bfa) !important;
    border: none !important;
    box-shadow: 0 4px 12px rgba(116, 48, 137,0.2) !important;
    color: #fff !important;
    transition: all 0.25s ease !important;
}
.btn-curved:hover { transform: translateY(-1px) !important; box-shadow: 0 6px 16px rgba(116, 48, 137,0.3) !important; color:#fff !important; }

/* Table hover */
tbody tr:hover { background-color: rgba(116, 48, 137,0.015) !important; }
html[data-admin-theme="dark"] tbody tr:hover { background-color: rgba(255,255,255,0.01) !important; }
html[data-admin-theme="dark"] td { color: #cbd5e1 !important; }

/* Mobile Card details style */
.text-muted-extra {
    color: #8b96a5 !important;
    font-size: 0.62rem !important;
    font-weight: 700 !important;
}
html[data-admin-theme="dark"] .text-muted-extra {
    color: #4b5563 !important;
}

/* Mobile cards theme border and divider */
.mobile-coupon-card {
    border: 1.5px solid rgba(0,0,0,0.06) !important;
}
.mobile-coupon-divider {
    border-top: 1px dashed rgba(0,0,0,0.06) !important;
}
html[data-admin-theme="dark"] .mobile-coupon-card {
    border-color: rgba(255,255,255,0.08) !important;
}
html[data-admin-theme="dark"] .mobile-coupon-divider {
    border-top-color: rgba(255,255,255,0.08) !important;
}

.coupon-table-row {
    border-bottom: 1px solid rgba(0,0,0,0.02) !important;
}
html[data-admin-theme="dark"] .coupon-table-row {
    border-bottom-color: rgba(255,255,255,0.04) !important;
}

@media (max-width: 767px) {
    .btn-curved { padding: 6px 16px !important; font-size: 0.78rem !important; }
}
</style>
@endpush

@section('content')
<div class="container-fluid px-0" style="padding-bottom: 40px;">

    {{-- Page Header --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h2 class="h3 mb-0 text-gray-800 fw-bold" style="font-family:'Outfit',sans-serif;">Coupon Management</h2>
            <p class="text-muted mb-0">Create, manage, and monitor promotional discount coupon codes</p>
        </div>
        <div class="col-md-6 text-right d-none d-md-block">
            <span class="badge bg-soft-primary px-3 py-2 rounded-pill font-weight-bold" style="font-size:0.8rem;">
                Total Active: {{ $coupons->where('is_active', true)->count() }}
            </span>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert"
         style="border-radius:12px;background:rgba(16,185,129,0.12);color:#10b981;padding:0.75rem 1rem;">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill mr-2" style="margin-right: 6px;"></i>
                <span class="font-weight-bold" style="font-size:0.82rem;">{{ session('success') }}</span>
            </div>
            <button type="button" class="close p-0" data-dismiss="alert" style="color:#10b981;opacity:0.8;line-height: 1;">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
    @endif

    {{-- Coupon Content Wrapper --}}
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4 theme-card-bg">
        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center flex-wrap" style="gap:12px;">
            <h5 class="card-title mb-0 font-weight-bold d-flex align-items-center text-theme-dark-bold" style="font-family:'Outfit',sans-serif;">
                <i class="bi bi-tag-fill text-primary mr-2" style="font-size:1.15rem; margin-right: 8px;"></i>
                Discount Coupons Directory
            </h5>
            <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary btn-curved d-inline-flex align-items-center justify-content-center">
                <i class="bi bi-plus-circle-fill mr-2" style="font-size:0.95rem; margin-right: 6px;"></i>
                <span class="d-none d-md-inline">Create Coupon</span>
                <span class="d-inline d-md-none">Create</span>
            </a>
        </div>

        <div class="card-body p-4 mt-2">
            
            {{-- DESKTOP VIEW: Table --}}
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="text-muted" style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.8px;border-bottom:1.5px solid rgba(0,0,0,0.04);">
                            <th class="ps-0 py-3 align-middle">Code</th>
                            <th class="py-3 align-middle">Type</th>
                            <th class="py-3 align-middle">Discount Value</th>
                            <th class="py-3 align-middle">Min Order</th>
                            <th class="py-3 align-middle">Expiry Date</th>
                            <th class="py-3 align-middle">Uses</th>
                            <th class="py-3 align-middle">Status</th>
                            <th class="py-3 align-middle text-right pr-0">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($coupons as $coupon)
                        <tr class="coupon-table-row" style="transition:all 0.2s ease;">
                            <td class="ps-0 py-3 align-middle">
                                <code class="p-1.5 px-3 border-0 bg-soft-primary rounded font-weight-bold" style="font-size: 0.85rem; font-family: monospace;">{{ $coupon->code }}</code>
                            </td>
                            <td class="align-middle text-uppercase small font-weight-bold">
                                {{ $coupon->discount_type }}
                            </td>
                            <td class="align-middle font-weight-bold text-theme-dark-bold" style="font-size:0.9rem;">
                                {{ $coupon->discount_type === 'percentage' ? $coupon->discount_value . '%' : '£' . number_format($coupon->discount_value, 2) }}
                            </td>
                            <td class="align-middle text-muted small" style="font-size:0.82rem;">
                                {{ $coupon->min_order_amount ? '£' . number_format($coupon->min_order_amount, 2) : 'No Min' }}
                            </td>
                            <td class="align-middle text-muted small" style="font-size:0.82rem;">
                                {{ $coupon->valid_until ? $coupon->valid_until->format('d M Y') : 'Infinite' }}
                            </td>
                            <td class="align-middle">
                                <span class="badge bg-soft-secondary font-weight-bold px-2.5 py-1" style="border-radius:12px;">
                                    {{ $coupon->times_used }} @if($coupon->usage_limit)/ {{ $coupon->usage_limit }}@endif
                                </span>
                            </td>
                            <td class="align-middle">
                                @if($coupon->is_active)
                                    <span class="badge bg-soft-success font-weight-bold px-2.5 py-1" style="border-radius:12px;">ACTIVE</span>
                                @else
                                    <span class="badge bg-soft-secondary font-weight-bold px-2.5 py-1" style="border-radius:12px;">INACTIVE</span>
                                @endif
                            </td>
                            <td class="text-right pr-0 align-middle">
                                <div class="dropdown">
                                    <button class="btn btn-link p-0" type="button" id="dropCoupon-{{ $coupon->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="bi bi-three-dots text-muted" style="font-size:1.15rem;"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right rounded-3 shadow border-0" aria-labelledby="dropCoupon-{{ $coupon->id }}" style="border-radius: 12px !important; padding: 6px;">
                                        <a class="dropdown-item d-flex align-items-center py-2 rounded-2" href="{{ route('admin.coupons.edit', $coupon) }}">
                                            <i class="bi bi-pencil mr-2 text-primary" style="margin-right: 6px;"></i> Edit
                                        </a>
                                        <div class="dropdown-divider" style="opacity: 0.08;"></div>
                                        <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" data-confirm="Truly delete this coupon?">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="dropdown-item d-flex align-items-center text-danger py-2 rounded-2">
                                                <i class="bi bi-trash mr-2" style="margin-right: 6px;"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-tag-fill d-block mb-3" style="font-size: 2.5rem; opacity: 0.35;"></i>
                                <p class="font-weight-bold mb-0">No coupons registered yet.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- MOBILE VIEW: Beautiful Responsive Cards --}}
            <div class="d-block d-md-none mt-2">
                @forelse($coupons as $coupon)
                <div class="d-flex align-items-stretch mb-3 p-0 rounded-4 theme-card-bg overflow-hidden mobile-coupon-card" style="min-height: 100px;">
                    <!-- Left side: Value Banner -->
                    <div class="d-flex flex-column align-items-center justify-content-center px-3 text-white text-center" 
                         style="background: linear-gradient(135deg, #743089, #a78bfa); min-width: 90px; max-width: 95px;">
                        <span class="font-weight-bold" style="font-family: 'Outfit'; font-size: 1.15rem; line-height: 1.1;">
                            {{ $coupon->discount_type === 'percentage' ? $coupon->discount_value . '%' : '£' . number_format($coupon->discount_value, 0) }}
                        </span>
                        <span class="small font-weight-bold mt-1 text-uppercase text-white-50" style="font-size: 0.58rem; letter-spacing: 0.5px;">OFF</span>
                    </div>

                    <!-- Right side: Details -->
                    <div class="flex-grow-1 p-3 d-flex flex-column justify-content-between position-relative" style="min-width: 0;">
                        <!-- Action Dropdown for mobile -->
                        <div class="position-absolute" style="top: 10px; right: 10px;">
                            <div class="dropdown">
                                <button class="btn btn-link p-0" type="button" id="dropCouponMob-{{ $coupon->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical text-muted"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right rounded-3 shadow border-0" aria-labelledby="dropCouponMob-{{ $coupon->id }}" style="border-radius: 12px !important; padding: 6px;">
                                    <a class="dropdown-item d-flex align-items-center py-2 rounded-2" href="{{ route('admin.coupons.edit', $coupon) }}">
                                        <i class="bi bi-pencil mr-2 text-primary" style="margin-right: 6px;"></i> Edit
                                    </a>
                                    <div class="dropdown-divider" style="opacity: 0.08;"></div>
                                    <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" data-confirm="Truly delete this coupon?">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="dropdown-item d-flex align-items-center text-danger py-2 rounded-2">
                                            <i class="bi bi-trash mr-2" style="margin-right: 6px;"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div>
                            <code class="p-1 px-2.5 border-0 bg-soft-primary rounded font-weight-bold d-inline-block mb-2" style="font-size: 0.78rem; font-family: monospace;">{{ $coupon->code }}</code>
                            
                            <div class="row g-2 mb-2 text-muted small" style="font-size: 0.75rem;">
                                <div class="col-6">
                                    <span class="d-block text-muted-extra text-uppercase" style="font-size: 0.58rem; letter-spacing: 0.3px;">Min Spend</span>
                                    <span class="font-weight-bold text-theme-dark-bold">
                                        {{ $coupon->min_order_amount ? '£' . number_format($coupon->min_order_amount, 2) : 'No Minimum' }}
                                    </span>
                                </div>
                                <div class="col-6">
                                    <span class="d-block text-muted-extra text-uppercase" style="font-size: 0.58rem; letter-spacing: 0.3px;">Usage (Uses)</span>
                                    <span class="font-weight-bold text-theme-dark-bold">
                                        {{ $coupon->times_used }}@if($coupon->usage_limit)/{{ $coupon->usage_limit }}@endif
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-2 mt-1 mobile-coupon-divider">
                            <span class="text-muted small" style="font-size: 0.68rem;">
                                <i class="bi bi-calendar-event mr-1" style="margin-right: 3px;"></i>
                                {{ $coupon->valid_until ? $coupon->valid_until->format('d M Y') : 'Infinite' }}
                            </span>
                            @if($coupon->is_active)
                                <span class="badge bg-soft-success font-weight-bold px-2 py-0.5" style="border-radius:10px; font-size: 0.62rem;">ACTIVE</span>
                            @else
                                <span class="badge bg-soft-secondary font-weight-bold px-2 py-0.5" style="border-radius:10px; font-size: 0.62rem;">INACTIVE</span>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5 text-muted card border-0">
                    <i class="bi bi-tag-fill d-block mb-3" style="font-size: 2.5rem; opacity: 0.35;"></i>
                    <p class="font-weight-bold mb-0">No coupons registered yet.</p>
                </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $coupons->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
@endsection
