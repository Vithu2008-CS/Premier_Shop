{{--
    admin/coupons/create.blade.php — Create coupon form
    =====================================================
    Fields: code, type (percentage/fixed), value, min_order_amount, max_uses,
            expires_at, is_active. JS toggles value label based on type selection.
    POST → admin.coupons.store → CouponController::store()
--}}
@extends('layouts.admin_noble')
@section('title', 'Create Coupon')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap');

.container-fluid { font-family: 'Inter', sans-serif; }
.rounded-4 { border-radius: 18px !important; }
.rounded-3 { border-radius: 12px !important; }
.gap-4     { gap: 24px !important; }

/* Themed cards */
html[data-admin-theme="light"] .theme-card-bg { background-color: #ffffff !important; }
html[data-admin-theme="dark"]  .theme-card-bg { background-color: #0c1427 !important; border: 1px solid rgba(255,255,255,0.05) !important; }
html[data-admin-theme="light"] .text-theme-dark-bold { color: #1e293b !important; }
html[data-admin-theme="dark"]  .text-theme-dark-bold { color: #f1f5f9 !important; }

/* Soft colour tokens */
.bg-soft-primary   { background: rgba(108,92,231,0.1) !important;  color: #6c5ce7 !important; }
.bg-soft-success   { background: rgba(16,185,129,0.1) !important;   color: #10b981 !important; }
.bg-soft-secondary { background: rgba(100,116,139,0.1) !important;  color: #64748b !important; }
.bg-soft-warning   { background: rgba(245,158,11,0.1) !important;   color: #f59e0b !important; }

/* Form inputs */
.form-control, .form-select {
    border-radius: 12px !important;
    border: 1.5px solid rgba(0,0,0,0.07) !important;
    padding: 0.5rem 0.95rem !important;
    font-size: 0.84rem !important;
    transition: all 0.25s ease !important;
    background-color: #ffffff !important;
    color: #1e293b !important;
}
.form-control:focus, .form-select:focus {
    border-color: #6c5ce7 !important;
    box-shadow: 0 0 0 3.5px rgba(108,92,231,0.15) !important;
}
html[data-admin-theme="dark"] .form-control, html[data-admin-theme="dark"] .form-select {
    background-color: #080f1d !important;
    border-color: rgba(255,255,255,0.08) !important;
    color: #e2e8f0 !important;
}
html[data-admin-theme="dark"] .form-control:focus, html[data-admin-theme="dark"] .form-select:focus {
    border-color: #a78bfa !important;
    box-shadow: 0 0 0 3.5px rgba(167,139,250,0.2) !important;
}
.form-control.is-invalid, .form-select.is-invalid {
    border-color: #ff3366 !important;
    box-shadow: 0 0 0 3.5px rgba(255,51,102,0.15) !important;
}
html[data-admin-theme="dark"] .form-control.is-invalid, html[data-admin-theme="dark"] .form-select.is-invalid {
    border-color: #ff3366 !important;
    box-shadow: 0 0 0 3.5px rgba(255,51,102,0.25) !important;
}

/* Border */
.border-bottom-subtle { border-bottom: 1.5px solid rgba(108,92,231,0.06) !important; }
html[data-admin-theme="dark"] .border-bottom-subtle { border-bottom: 1.5px solid rgba(255,255,255,0.05) !important; }

/* ── Coupon Live Ticket Preview ── */
.coupon-ticket-preview {
    position: relative;
    background: linear-gradient(135deg, #6c5ce7 0%, #8555e3 100%) !important;
    color: #ffffff !important;
    border-radius: 18px !important;
    box-shadow: 0 10px 25px rgba(108, 92, 231, 0.25) !important;
    border: none !important;
    overflow: hidden;
}
.ticket-cutout-left, .ticket-cutout-right {
    position: absolute;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    z-index: 10;
    top: 140px; /* Specific height matching dashed divider */
    background: #f9fafb; /* Light theme bg */
}
html[data-admin-theme="dark"] .ticket-cutout-left,
html[data-admin-theme="dark"] .ticket-cutout-right {
    background: #0c1427; /* Dark theme bg */
}
.ticket-cutout-left { left: -10px; }
.ticket-cutout-right { right: -10px; }

.ticket-dashed-divider {
    border-top: 2px dashed rgba(255, 255, 255, 0.28);
    margin: 25px 0;
    width: 100%;
}

/* Floating save bar */
.floating-save-bar {
    position: fixed; bottom: 24px; left: calc(50% + 120px);
    transform: translateX(-50%); z-index: 1000;
    width: calc(100% - 32px - 240px); max-width: 920px;
    background: rgba(255,255,255,0.85) !important;
    backdrop-filter: blur(12px) !important; -webkit-backdrop-filter: blur(12px) !important;
    border: 1px solid rgba(0,0,0,0.08) !important;
    box-shadow: 0 10px 30px rgba(0,0,0,0.06) !important;
    border-radius: 50px !important;
    transition: all 0.3s ease;
}
html[data-admin-theme="dark"] .floating-save-bar {
    background: rgba(15,23,42,0.85) !important;
    border-color: rgba(255,255,255,0.08) !important;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3) !important;
}
.floating-save-bar .button-group { display: flex; align-items: center; gap: 12px; }
.floating-save-bar .btn {
    display: inline-flex; align-items: center; justify-content: center;
    height: 38px !important; min-width: 100px !important;
    padding: 0 22px !important; font-size: 0.82rem !important; font-weight: 700 !important;
    border-radius: 30px !important; transition: all 0.2s ease !important;
}
.floating-save-bar .btn-outline-light {
    border: 1.5px solid rgba(0,0,0,0.15) !important; background: transparent !important; color: #475569 !important;
}
.floating-save-bar .btn-outline-light:hover { background: rgba(0,0,0,0.04) !important; color: #1e293b !important; }
html[data-admin-theme="dark"] .floating-save-bar .btn-outline-light { border-color: rgba(255,255,255,0.3) !important; color: #fff !important; }
html[data-admin-theme="dark"] .floating-save-bar .btn-outline-light:hover { background: rgba(255,255,255,0.1) !important; }
.floating-save-bar .btn-primary {
    background: linear-gradient(135deg,#6c5ce7,#a78bfa) !important;
    border: none !important; color: #fff !important;
    box-shadow: 0 4px 12px rgba(108,92,231,0.2) !important;
}
.floating-save-bar .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(108,92,231,0.3) !important; color:#fff !important; }
.floating-save-bar .floating-bar-title { color: #0f172a !important; }
html[data-admin-theme="dark"] .floating-save-bar .floating-bar-title { color: #ffffff !important; }

/* Pulsing dot */
.pulse-green {
    width: 8px; height: 8px; background: #10b981; border-radius: 50%;
    display: inline-block; animation: blinkDot 1.5s infinite ease-in-out;
}
@keyframes blinkDot {
    0%,100% { opacity: 0.3; transform: scale(0.9); }
    50%      { opacity: 1;   transform: scale(1.15); }
}

@media (max-width: 991px) { .floating-save-bar { left: 50% !important; width: calc(100% - 32px) !important; } }
@media (max-width: 575px) {
    .floating-save-bar {
        border-radius: 20px !important; padding: 12px 16px !important;
        bottom: 16px !important; flex-direction: column; gap: 10px;
        align-items: stretch !important; text-align: center;
        width: calc(100% - 24px) !important;
    }
    .floating-save-bar .button-group { width: 100%; gap: 8px; }
    .floating-save-bar .btn { min-width: 0 !important; padding: 0 8px !important; font-size: 0.76rem !important; flex: 1; }
    .card-body.p-4.p-md-5 { padding: 1.25rem !important; }
}
</style>
@endpush

@section('content')
<div class="container-fluid px-0" style="padding-bottom: 120px;">

    {{-- Breadcrumb --}}
    <nav class="page-breadcrumb d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.coupons.index') }}">Coupons</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create</li>
        </ol>
        <a href="{{ route('admin.coupons.index') }}"
           class="btn btn-outline-primary btn-sm rounded-pill px-3 d-inline-flex align-items-center"
           style="border-radius:30px !important;font-weight:700;font-family:'Outfit';border:1.5px solid #6c5ce7;color:#6c5ce7;">
            <i class="bi bi-arrow-left mr-2" style="font-size:0.85rem;margin-right:6px;"></i> Back to Coupons
        </a>
    </nav>

    <div class="row">
        {{-- Left: Registration Form --}}
        <div class="col-lg-7 mb-4">
            <form action="{{ route('admin.coupons.store') }}" method="POST" id="coupon-create-form" class="d-flex flex-column gap-4 w-100">
                @csrf

                {{-- Coupon Info --}}
                <div class="card border-0 shadow-sm theme-card-bg" style="border-radius:18px !important;">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex align-items-center mb-4 pb-2 border-bottom-subtle">
                            <h5 class="card-title mb-0 font-weight-bold text-theme-dark-bold d-flex align-items-center" style="font-family:'Outfit',sans-serif;">
                                <i class="bi bi-tag-fill text-primary mr-2" style="font-size:1.25rem;margin-right:8px;"></i>
                                Coupon Configurations
                            </h5>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small text-muted font-weight-bold">Coupon Code <span class="text-danger">*</span></label>
                                <input type="text" name="code" id="coupon_code" class="form-control text-uppercase @error('code') is-invalid @enderror"
                                       value="{{ old('code') }}" required placeholder="e.g. SAVE20">
                                @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label small text-muted font-weight-bold">Discount Type</label>
                                <select name="discount_type" id="discount_type" class="form-control">
                                    <option value="percentage">Percentage (%)</option>
                                    <option value="fixed">Fixed (£)</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label small text-muted font-weight-bold">Discount Value <span class="text-danger">*</span></label>
                                <input type="number" name="discount_value" id="discount_value" class="form-control @error('discount_value') is-invalid @enderror"
                                       value="{{ old('discount_value', '0.00') }}" step="0.01" min="0" required>
                                @error('discount_value')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label small text-muted font-weight-bold">Minimum Order Amount (£)</label>
                                <input type="number" name="min_order_amount" id="min_order" class="form-control"
                                       step="0.01" min="0" value="{{ old('min_order_amount') }}" placeholder="0.00">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label small text-muted font-weight-bold">Valid From</label>
                                <input type="datetime-local" name="valid_from" class="form-control"
                                       value="{{ old('valid_from', now()->format('Y-m-d\TH:i')) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label small text-muted font-weight-bold">Valid Until</label>
                                <input type="datetime-local" name="valid_until" id="valid_until" class="form-control"
                                       value="{{ old('valid_until') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small text-muted font-weight-bold">Usage Limit (per code)</label>
                                <input type="number" name="usage_limit" class="form-control" min="1"
                                       value="{{ old('usage_limit') }}" placeholder="Unlimited if empty">
                            </div>
                            <div class="col-md-6 mb-3 d-flex align-items-end">
                                <div class="form-check mb-3">
                                    <label class="form-check-label font-weight-bold text-theme-dark-bold small cursor-pointer">
                                        <input type="checkbox" name="is_active" class="form-check-input" value="1" checked>
                                        Coupon is active and redeemable
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- Right: Guidelines / Interactive Ticket Preview Card --}}
        <div class="col-lg-5 mb-4">
            <div class="d-flex flex-column gap-4 h-100 justify-content-between">
                
                {{-- Live Coupon Ticket --}}
                <div class="card coupon-ticket-preview p-4 d-flex flex-column justify-content-between position-relative" style="min-height: 240px;">
                    <div class="ticket-cutout-left"></div>
                    <div class="ticket-cutout-right"></div>

                    <!-- Ticket Header -->
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center" style="gap: 8px;">
                            <i class="bi bi-tag-fill" style="font-size: 1.25rem;"></i>
                            <span class="font-weight-bold" style="font-family: 'Outfit'; font-size: 0.88rem; letter-spacing: 0.5px; text-transform: uppercase;">Premier Coupon</span>
                        </div>
                        <span class="badge bg-white text-primary font-weight-bold" style="font-size: 0.65rem; border-radius: 20px; padding: 4px 10px;">OFFICIAL</span>
                    </div>

                    <!-- Ticket Value -->
                    <div class="text-center my-3">
                        <h2 class="font-weight-extrabold mb-0" id="preview_val_display" style="font-family: 'Outfit'; font-size: 2.5rem; letter-spacing: -1px;">
                            0% OFF
                        </h2>
                        <span class="text-white-50 small font-weight-bold" id="preview_min_spend_display" style="font-size: 0.72rem; letter-spacing: 0.5px;">NO MINIMUM SPEND REQUIRED</span>
                    </div>

                    <div class="ticket-dashed-divider"></div>

                    <!-- Ticket Code & Expiry -->
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="d-block text-white-50 font-weight-bold" style="font-size: 0.58rem; letter-spacing: 0.5px; text-transform: uppercase;">PROMO CODE</span>
                            <span class="font-weight-bold" id="preview_code_display" style="font-family: monospace; font-size: 1.15rem; letter-spacing: 1px;">COUPONCODE</span>
                        </div>
                        <div class="text-right">
                            <span class="d-block text-white-50 font-weight-bold" style="font-size: 0.58rem; letter-spacing: 0.5px; text-transform: uppercase;">EXPIRY</span>
                            <span class="font-weight-bold small" id="preview_expiry_display" style="font-size: 0.75rem;">NEVER EXPIRES</span>
                        </div>
                    </div>
                </div>

                {{-- Tips Guidelines Card --}}
                <div class="card border-0 shadow-sm theme-card-bg p-4 rounded-4 mt-2">
                    <h6 class="card-title fw-bold text-primary mb-3 d-flex align-items-center" style="font-family:'Outfit',sans-serif; font-size: 0.9rem;">
                        <i class="bi bi-info-circle mr-2" style="margin-right: 6px;"></i> Coupon Rules
                    </h6>
                    <ul class="text-muted small pl-3 mb-0" style="line-height: 1.6; padding-left: 20px;">
                        <li><strong>Percentage</strong> type applies a relative discount to the cart total.</li>
                        <li><strong>Fixed</strong> amount discounts subtract a flat cash rate.</li>
                        <li>Min spend prevents checkout application if the items subtotal is below the threshold.</li>
                        <li>Leaving the expiry date empty creates a permanent/infinite duration code.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Floating Action Bar --}}
<div class="floating-save-bar d-flex align-items-center justify-content-between px-4 py-3">
    <div class="d-flex align-items-center" style="font-family:'Outfit',sans-serif;gap:8px;">
        <span class="pulse-green"></span>
        <span class="text-muted text-uppercase d-none d-sm-inline" style="font-size:0.68rem;letter-spacing:0.5px;font-weight:600;white-space:nowrap;">New Coupon:</span>
        <span class="font-weight-bold text-nowrap floating-bar-title" style="font-size:0.85rem; font-family: monospace; letter-spacing: 0.5px;">PENDING</span>
    </div>
    <div class="button-group">
        <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-light">Cancel</a>
        <button type="submit" form="coupon-create-form" class="btn btn-primary">
            <i class="bi bi-check2-circle" style="margin-right:6px;"></i> Create Coupon
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    'use strict';

    const codeInput     = $('#coupon_code');
    const typeSelect    = $('#discount_type');
    const valInput      = $('#discount_value');
    const minInput      = $('#min_order');
    const dateInput     = $('#valid_until');

    const previewCode     = $('#preview_code_display');
    const previewVal      = $('#preview_val_display');
    const previewMin      = $('#preview_min_spend_display');
    const previewExpiry   = $('#preview_expiry_display');
    const floatingTitle   = $('.floating-bar-title');

    function updatePreview() {
        // Code
        let code = codeInput.val().trim().toUpperCase();
        previewCode.text(code.length > 0 ? code : 'COUPONCODE');
        floatingTitle.text(code.length > 0 ? code : 'PENDING');

        // Discount value
        let val = parseFloat(valInput.val()) || 0;
        let type = typeSelect.val();
        if (type === 'percentage') {
            previewVal.text(val + '% OFF');
        } else {
            previewVal.text('£' + val.toFixed(2) + ' OFF');
        }

        // Min Order
        let min = parseFloat(minInput.val()) || 0;
        previewMin.text(min > 0 ? 'MINIMUM SPEND REQUIRED: £' + min.toFixed(2) : 'NO MINIMUM SPEND REQUIRED');

        // Expiry
        let dateVal = dateInput.val();
        if (dateVal) {
            let date = new Date(dateVal);
            let options = { day: 'numeric', month: 'short', year: 'numeric' };
            previewExpiry.text(date.toLocaleDateString('en-GB', options).toUpperCase());
        } else {
            previewExpiry.text('NEVER EXPIRES');
        }
    }

    // Attach listeners
    codeInput.on('input', updatePreview);
    typeSelect.on('change', updatePreview);
    valInput.on('input', updatePreview);
    minInput.on('input', updatePreview);
    dateInput.on('change', updatePreview);

    // Initial load
    updatePreview();
});
</script>
@endpush
