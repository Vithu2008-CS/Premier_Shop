@extends('layouts.admin_noble')
@section('title', 'Return Request REQ-' . str_pad($return->id, 5, '0', STR_PAD_LEFT))

@section('content')
<div class="container-fluid px-0" style="padding-bottom: 120px;"> {{-- Leaves space for the floating action bar --}}
    <nav class="page-breadcrumb d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.returns.index') }}">Returns</a></li>
            <li class="breadcrumb-item active" aria-current="page">Request REQ-{{ str_pad($return->id, 5, '0', STR_PAD_LEFT) }}</li>
        </ol>
        <a href="{{ route('admin.returns.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 d-inline-flex align-items-center">
            <i class="bi bi-arrow-left mr-2" style="font-size: 0.85rem;"></i> Back to List
        </a>
    </nav>

    <div class="row">
        {{-- Left Column: Request Details --}}
        <div class="col-lg-8 grid-margin stretch-card mb-4">
            <div class="card border-0 shadow-sm theme-card-bg w-100" style="border-radius: 18px !important; overflow: hidden;">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex align-items-center mb-4 pb-2 border-bottom-subtle">
                        <h5 class="card-title mb-0 font-weight-bold text-theme-dark-bold d-flex align-items-center" style="font-family: 'Outfit', sans-serif;">
                            <i class="bi bi-file-earmark-text-fill text-primary mr-2" style="font-size: 1.25rem;"></i>
                            Return Request Details
                        </h5>
                    </div>

                    {{-- Customer & Order Info Card --}}
                    <h6 class="text-muted small font-weight-bold text-uppercase mb-3" style="letter-spacing: 0.8px;">Customer & Order Information</h6>
                    <div class="d-flex align-items-center p-4 rounded-4 mb-4" style="background: rgba(108,92,231,0.03); border: 1.5px solid rgba(108,92,231,0.06);">
                        <div class="wd-52 h-52 rounded-circle bg-soft-primary text-primary font-weight-bold d-flex align-items-center justify-content-center mr-3 shadow-sm" style="font-size: 1.25rem; min-width: 52px; height: 52px;">
                            {{ substr($return->user->name, 0, 1) }}
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 text-theme-dark-bold fw-700" style="font-size: 0.95rem;">{{ $return->user->name }}</h6>
                            <p class="text-muted small mb-0">{{ $return->user->email }}</p>
                        </div>
                        <div class="text-right d-none d-sm-block">
                            <span class="text-muted small d-block font-weight-bold text-uppercase" style="letter-spacing: 0.8px; font-size: 0.65rem;">Order Number</span>
                            <a href="{{ route('admin.orders.show', $return->order) }}" class="font-weight-bold text-primary mt-1 d-block" style="font-size: 0.9rem;">
                                {{ $return->order->order_number }}
                            </a>
                        </div>
                    </div>

                    {{-- Mobile Order Info link --}}
                    <div class="d-block d-sm-none mb-4 p-3 rounded-3" style="background: rgba(0,0,0,0.015); border: 1px solid rgba(0,0,0,0.04);">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small font-weight-bold">Order Number:</span>
                            <a href="{{ route('admin.orders.show', $return->order) }}" class="font-weight-bold text-primary" style="font-size: 0.88rem;">
                                {{ $return->order->order_number }}
                            </a>
                        </div>
                    </div>

                    {{-- Reason & Statement Section --}}
                    <h6 class="text-muted small font-weight-bold text-uppercase mb-3" style="letter-spacing: 0.8px;">Reason & Statement</h6>
                    <div class="p-4 rounded-4 mb-4 border-0" style="background: rgba(108,92,231,0.02); border-left: 4px solid #6c5ce7 !important;">
                        <div class="mb-2">
                            <span class="text-muted small font-weight-bold">Reason:</span>
                            <span class="badge px-3 py-1 bg-soft-secondary font-weight-bold ml-2" style="font-size: 0.75rem; border-radius: 12px;">{{ $return->reason }}</span>
                        </div>
                        <div class="text-theme-dark-bold small leading-normal mt-3 p-3 rounded-3 bg-white border-0" style="background-color: var(--statement-bg, #ffffff) !important; border: 1px solid rgba(0,0,0,0.04) !important; font-style: italic;">
                            "{{ $return->customer_note ?: 'No additional notes provided by customer.' }}"
                        </div>
                    </div>

                    {{-- Items Being Returned Table --}}
                    <h6 class="text-muted small font-weight-bold text-uppercase mb-3" style="letter-spacing: 0.8px;">Items Being Returned</h6>
                    <div class="table-responsive rounded-4 border-0" style="border: 1px solid rgba(108,92,231,0.06) !important;">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr class="text-uppercase small text-muted font-weight-bold" style="background: rgba(108,92,231,0.02); border-bottom: 2px solid rgba(108,92,231,0.06); letter-spacing: 0.5px; font-size: 0.72rem;">
                                    <th class="pl-4 py-3">Product</th>
                                    <th class="text-center py-3">Qty Returned</th>
                                    <th class="text-right py-3">Price Each</th>
                                    <th class="text-right pr-4 py-3">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totalReturnVal = 0; @endphp
                                @foreach($return->items as $item)
                                    @php 
                                        $sub = $item->quantity * $item->orderItem->price;
                                        $totalReturnVal += $sub; 
                                    @endphp
                                    <tr class="table-row-hover-effect" style="border-bottom: 1px solid rgba(108,92,231,0.04);">
                                        <td class="pl-4 py-3 align-middle font-weight-bold text-theme-dark-bold" style="font-size: 0.9rem;">
                                            {{ $item->orderItem->product->name }}
                                        </td>
                                        <td class="text-center py-3 font-weight-bold text-theme-dark-bold" style="font-size: 0.9rem;">{{ $item->quantity }}</td>
                                        <td class="text-right py-3 text-muted font-weight-medium" style="font-size: 0.88rem;">£{{ number_format($item->orderItem->price, 2) }}</td>
                                        <td class="text-right pr-4 py-3 font-weight-bold text-primary" style="font-size: 0.92rem;">£{{ number_format($sub, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr style="background: rgba(108,92,231,0.01);">
                                    <th colspan="3" class="text-right py-3 border-0 font-weight-bold text-muted small" style="font-size: 0.8rem;">Max Value of Returned Items:</th>
                                    <th class="text-right pr-4 py-3 border-0 text-primary font-weight-extrabold" style="font-size: 1.15rem; font-family: 'Outfit', sans-serif;">£{{ number_format($totalReturnVal, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Process & Evidence --}}
        <div class="col-lg-4 flex-column d-flex gap-4">
            {{-- Process Return Card --}}
            <div class="card border-0 shadow-sm theme-card-bg w-100 mb-4" style="border-radius: 18px !important;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4 pb-2 border-bottom-subtle">
                        <h5 class="card-title mb-0 font-weight-bold text-theme-dark-bold d-flex align-items-center" style="font-family: 'Outfit', sans-serif;">
                            <i class="bi bi-sliders text-info mr-2" style="font-size: 1.25rem;"></i>
                            Process Return
                        </h5>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px; background-color: rgba(16, 185, 129, 0.12); color: #10b981; padding: 0.75rem 1rem;">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle-fill mr-2" style="font-size: 1rem;"></i>
                                <span class="font-weight-bold" style="font-size: 0.8rem;">{{ session('success') }}</span>
                            </div>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px; background-color: rgba(239, 68, 68, 0.12); color: #ef4444; padding: 0.75rem 1rem;">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-exclamation-circle-fill mr-2" style="font-size: 1rem;"></i>
                                <span class="font-weight-bold" style="font-size: 0.8rem;">{{ $errors->first() }}</span>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('admin.returns.update', $return) }}" method="POST" id="process-return-form" class="process-return-form">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-4">
                            <label class="small text-muted font-weight-bold mb-2 d-flex align-items-center">
                                <i class="bi bi-flag text-primary mr-2"></i> Update Status
                            </label>
                            @php
                                $statusOptions = [
                                    'pending'  => 'Pending',
                                    'approved' => 'Approved (Restores Stock)',
                                    'rejected' => 'Rejected',
                                    'refunded' => 'Refund Processed',
                                ];
                            @endphp
                            <select name="status" class="form-control rounded-3" style="height: 42px !important;" required>
                                @foreach($statusOptions as $value => $label)
                                    <option value="{{ $value }}"
                                        {{ $return->status == $value ? 'selected' : '' }}
                                        {{ $return->canTransitionTo($value) ? '' : 'disabled' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @if($return->status === 'refunded')
                                <small class="form-text text-muted" style="font-size: 0.72rem;">Refund processed — status is final. Notes can still be edited.</small>
                            @endif
                        </div>

                        <div class="form-group mb-4">
                            <label class="small text-muted font-weight-bold mb-2 d-flex align-items-center">
                                <i class="bi bi-cash-coin text-success mr-2"></i> Refund Amount (£)
                            </label>
                            <input type="number" step="0.01" name="refund_amount" class="form-control rounded-3" value="{{ old('refund_amount', $return->refund_amount > 0 ? $return->refund_amount : $totalReturnVal) }}" style="height: 42px !important;">
                            <small class="form-text text-muted" style="font-size: 0.72rem;">Enter the exact amount to refund the customer.</small>
                        </div>

                        <div class="form-group mb-4">
                            <label class="small text-muted font-weight-bold mb-2 d-flex align-items-center">
                                <i class="bi bi-chat-left-dots text-muted mr-2"></i> Admin Notes (Visible to Customer)
                            </label>
                            <textarea name="admin_note" class="form-control rounded-3 text-dark-theme-aware" rows="4" style="resize: none;" placeholder="Provide feedback or update detail to customer...">{{ old('admin_note', $return->admin_note) }}</textarea>
                        </div>

                        @if($return->status == 'approved')
                        <div class="mt-3 text-success p-2.5 rounded-3 d-flex align-items-center gap-2" style="background: rgba(16,185,129,0.06); font-size: 0.75rem; border: 1px solid rgba(16,185,129,0.12);">
                            <i class="bi bi-check-circle-fill mr-2" style="font-size: 0.85rem;"></i>
                            <span class="font-weight-bold">Stock has been restored for these items.</span>
                        </div>
                        @endif
                    </form>
                </div>
            </div>

            {{-- Customer Evidence Grid Card (Moved to Right Column & Smallened) --}}
            @if($return->photo_path)
            <div class="card border-0 shadow-sm theme-card-bg w-100" style="border-radius: 18px !important;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3 pb-2 border-bottom-subtle">
                        <h5 class="card-title mb-0 font-weight-bold text-theme-dark-bold d-flex align-items-center" style="font-family: 'Outfit', sans-serif; font-size: 0.95rem;">
                            <i class="bi bi-images text-primary mr-2" style="font-size: 1.15rem;"></i>
                            Evidence Photos
                        </h5>
                    </div>
                    
                    @php
                        $photos = [];
                        if ($return->photo_path) {
                            $decoded = json_decode($return->photo_path, true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                $photos = $decoded;
                            } else {
                                $photos = [$return->photo_path];
                            }
                        }
                    @endphp

                    <p class="text-muted mb-3" style="font-size: 0.76rem; line-height: 1.45;">
                        Customer uploaded evidence ({{ count($photos) }}). Click a thumbnail to expand.
                    </p>
                    
                    <div class="evidence-grid">
                        @foreach($photos as $photo)
                        <div class="evidence-thumb-container" data-call="openLightbox" data-args="[&quot;{{ asset('storage/' . $photo) }}&quot;]">
                            <img src="{{ asset('storage/' . $photo) }}" alt="Evidence Thumbnail" class="evidence-thumb">
                            <div class="evidence-thumb-overlay">
                                <i class="bi bi-zoom-in text-white" style="font-size: 1.1rem;"></i>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Modern Floating Action Bar --}}
<div class="floating-save-bar d-flex align-items-center justify-content-between px-4 py-3 border shadow-lg rounded-pill">
    <div class="d-flex align-items-center gap-2" style="font-family: 'Outfit', sans-serif;">
        <span class="live-indicator me-1"></span>
        <div class="d-flex align-items-baseline gap-2">
            <span class="text-muted text-uppercase d-none d-sm-inline" style="font-size: 0.68rem; letter-spacing: 0.5px; font-weight: 600; white-space: nowrap; line-height: 1;">Return Request:</span>
            <span class="fw-bold text-nowrap floating-bar-title" style="font-size: 0.85rem; line-height: 1;" id="floating-return-title">REQ-{{ str_pad($return->id, 5, '0', STR_PAD_LEFT) }}</span>
        </div>
    </div>
    <div class="button-group">
        <button type="button" class="btn btn-danger" data-confirm="Are you sure you want to permanently delete this return request? This action cannot be undone." data-submit-form="delete-return-form">
            <i class="bi bi-trash mr-1"></i> Delete
        </button>
        <a href="{{ route('admin.returns.index') }}" class="btn btn-outline-light">
            Cancel
        </a>
        <button type="submit" form="process-return-form" class="btn btn-primary">
            <i class="bi bi-check2-circle mr-1"></i> Save
        </button>
    </div>
</div>

{{-- Hidden Deletion Form --}}
<form id="delete-return-form" action="{{ route('admin.returns.destroy', $return) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

{{-- Premium Lightbox Overlay Modal --}}
<div id="premiumLightbox" class="premium-lightbox" data-call="closeLightbox">
    <button class="lightbox-close-btn" data-call="closeLightbox">&times;</button>
    <div class="lightbox-content-wrapper" data-stop>
        <img id="lightboxImage" src="" alt="Expanded Evidence" class="lightbox-img">
    </div>
</div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap');

.container-fluid {
    font-family: 'Inter', sans-serif;
}

/* Explicit curve utilities */
.rounded-3 { border-radius: 12px !important; }
.rounded-4 { border-radius: 18px !important; }
.gap-1.5 { gap: 0.35rem; }
.gap-2.5 { gap: 0.6rem; }
.mr-1.5 { margin-right: 0.35rem; }
.p-2.5 { padding: 0.6rem !important; }
.leading-normal { line-height: 1.5; }
.fw-700 { font-weight: 700; }
.fw-800 { font-weight: 800; }

/* Soft background styles */
.bg-soft-primary { background: rgba(108, 92, 231, 0.1) !important; color: #6c5ce7 !important; }
.bg-soft-secondary { background: rgba(100, 116, 139, 0.1) !important; color: #64748b !important; }
.bg-soft-success { background: rgba(16, 185, 129, 0.1) !important; color: #10b981 !important; }
.bg-soft-warning { background: rgba(245, 158, 11, 0.1) !important; color: #f59e0b !important; }
.bg-soft-danger { background: rgba(239, 68, 68, 0.1) !important; color: #ef4444 !important; }

/* Evidence Grid and Thumbnails (Square shape with curved edges) */
.evidence-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(72px, 1fr));
    gap: 12px;
    margin-top: 8px;
}
.evidence-thumb-container {
    aspect-ratio: 1 / 1;
    border-radius: 12px !important; /* Edge curve */
    overflow: hidden;
    border: 1.5px solid rgba(0, 0, 0, 0.08) !important;
    background: #fafafa;
    cursor: pointer;
    position: relative;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}
html[data-admin-theme="dark"] .evidence-thumb-container {
    border-color: rgba(255, 255, 255, 0.08) !important;
    background: #080f1d !important;
}
.evidence-thumb-container:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(108, 92, 231, 0.15) !important;
    border-color: #6c5ce7 !important;
}
html[data-admin-theme="dark"] .evidence-thumb-container:hover {
    border-color: #a78bfa !important;
    box-shadow: 0 6px 12px rgba(167, 139, 250, 0.2) !important;
}
.evidence-thumb {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
}
.evidence-thumb-overlay {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0, 0, 0, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.25s ease;
}
.evidence-thumb-container:hover .evidence-thumb-overlay {
    opacity: 1;
}

/* Symmetrical forms and selects */
.form-control, select.form-control {
    border-radius: 12px !important;
    border: 1.5px solid rgba(0, 0, 0, 0.07) !important;
    padding: 0.5rem 0.95rem !important;
    font-size: 0.84rem !important;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
    background-color: var(--input-bg, #ffffff) !important;
    color: var(--input-color, #1e293b) !important;
}
.form-control:focus, select.form-control:focus {
    border-color: #6c5ce7 !important;
    box-shadow: 0 0 0 3.5px rgba(108, 92, 231, 0.15) !important;
}

/* Table interactions */
.table-row-hover-effect {
    transition: background-color 0.2s ease;
}
.table-row-hover-effect:hover {
    background-color: rgba(108, 92, 231, 0.015) !important;
}

/* Theme adaptation */
html[data-admin-theme="light"] .theme-card-bg {
    background-color: #ffffff !important;
}
html[data-admin-theme="dark"] .theme-card-bg {
    background-color: #0c1427 !important;
    border: 1px solid rgba(255, 255, 255, 0.05) !important;
}
html[data-admin-theme="light"] .text-theme-dark-bold {
    color: #1e293b !important;
}
html[data-admin-theme="dark"] .text-theme-dark-bold {
    color: #f1f5f9 !important;
    --statement-bg: #070d19;
}
html[data-admin-theme="dark"] .table-row-hover-effect:hover {
    background-color: rgba(255, 255, 255, 0.01) !important;
}
html[data-admin-theme="dark"] .bg-white {
    background-color: #070d19 !important;
}
html[data-admin-theme="dark"] td {
    color: #cbd5e1 !important;
}
html[data-admin-theme="dark"] .form-control {
    background-color: #080f1d !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
    color: #e2e8f0 !important;
}
html[data-admin-theme="dark"] .form-control:focus {
    border-color: #a78bfa !important;
    box-shadow: 0 0 0 3.5px rgba(167, 139, 250, 0.2) !important;
}

/* Border styles */
.border-bottom-subtle {
    border-bottom: 1.5px solid rgba(108, 92, 231, 0.06) !important;
}
html[data-admin-theme="dark"] .border-bottom-subtle {
    border-bottom: 1.5px solid rgba(255, 255, 255, 0.05) !important;
}

/* Premium Buttons */
.btn {
    border-radius: 30px !important;
    font-weight: 700 !important;
    font-family: 'Outfit', sans-serif !important;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
}
.btn-primary {
    background: linear-gradient(135deg, #6c5ce7, #a78bfa) !important;
    border: none !important;
    box-shadow: 0 4px 12px rgba(108, 92, 231, 0.2) !important;
    color: #ffffff !important;
}
.btn-primary:hover {
    transform: translateY(-1px) !important;
    box-shadow: 0 6px 16px rgba(108, 92, 231, 0.3) !important;
}
.btn-outline-primary {
    border: 1.5px solid #6c5ce7 !important;
    color: #6c5ce7 !important;
    background: transparent !important;
}
.btn-outline-primary:hover {
    background-color: #6c5ce7 !important;
    color: #ffffff !important;
    transform: translateY(-1px);
}
html[data-admin-theme="dark"] .btn-outline-primary {
    border-color: #a78bfa !important;
    color: #a78bfa !important;
}
html[data-admin-theme="dark"] .btn-outline-primary:hover {
    background-color: #a78bfa !important;
    color: #0c1427 !important;
}

/* Floating Action Bar styling */
.floating-save-bar {
    position: fixed;
    bottom: 24px;
    left: calc(50% + 120px);
    transform: translateX(-50%);
    z-index: 1000;
    width: calc(100% - 32px - 240px);
    max-width: 920px;
    background: rgba(255, 255, 255, 0.8) !important;
    backdrop-filter: blur(12px) !important;
    -webkit-backdrop-filter: blur(12px) !important;
    border: 1px solid rgba(0, 0, 0, 0.08) !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06) !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
html[data-admin-theme="dark"] .floating-save-bar {
    background: rgba(15, 23, 42, 0.8) !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3) !important;
}
.floating-save-bar .button-group {
    display: flex;
    align-items: center;
    gap: 12px;
}
.floating-save-bar .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 38px !important;
    min-width: 110px !important;
    padding: 0 24px !important;
    font-size: 0.82rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.3px;
    border-radius: 30px !important;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
}
.floating-save-bar .btn-outline-light {
    border: 1.5px solid rgba(0, 0, 0, 0.15) !important;
    background: transparent !important;
    color: #475569 !important;
}
.floating-save-bar .btn-outline-light:hover {
    background: rgba(0, 0, 0, 0.04) !important;
    border-color: rgba(0, 0, 0, 0.25) !important;
    color: #1e293b !important;
}
html[data-admin-theme="dark"] .floating-save-bar .btn-outline-light {
    border: 1.5px solid rgba(255, 255, 255, 0.3) !important;
    background: transparent !important;
    color: #ffffff !important;
}
html[data-admin-theme="dark"] .floating-save-bar .btn-outline-light:hover {
    background: rgba(255, 255, 255, 0.1) !important;
    border-color: rgba(255, 255, 255, 0.4) !important;
    color: #ffffff !important;
}
.floating-save-bar .btn-primary {
    border: 1.5px solid transparent !important;
    background: var(--ps-gradient, linear-gradient(135deg, #6c5ce7, #a78bfa)) !important;
    color: #ffffff !important;
    box-shadow: 0 4px 12px rgba(108, 92, 231, 0.2) !important;
}
.floating-save-bar .btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(108, 92, 231, 0.3) !important;
    color: #ffffff !important;
}

/* Floating Custom Pink-Rose Delete Button (Mirroring Screenshot!) */
.floating-save-bar .btn-danger {
    background-color: transparent !important;
    border: 1.8px solid #ff3366 !important;
    color: #ff3366 !important;
    box-shadow: none !important;
}
.floating-save-bar .btn-danger:hover {
    background-color: rgba(255, 51, 102, 0.05) !important;
    border-color: #ff3366 !important;
    box-shadow: 0 4px 12px rgba(255, 51, 102, 0.15) !important;
    transform: translateY(-1px) !important;
    color: #ff3366 !important;
}
.floating-save-bar .btn-danger:active {
    transform: scale(0.97) !important;
}

.floating-save-bar .floating-bar-title {
    color: #0f172a !important;
}
html[data-admin-theme="dark"] .floating-save-bar .floating-bar-title {
    color: #ffffff !important;
}
.floating-save-bar .text-muted {
    color: #64748b !important;
}
html[data-admin-theme="dark"] .floating-save-bar .text-muted {
    color: #94a3b8 !important;
}

.live-indicator {
    width: 8px;
    height: 8px;
    background: #10b981;
    border-radius: 50%;
    display: inline-block;
    animation: blinkIndicator 1.5s infinite ease-in-out;
}
@keyframes blinkIndicator {
    0%, 100% { opacity: 0.3; transform: scale(0.9); }
    50% { opacity: 1; transform: scale(1.15); }
}

@media (max-width: 991px) {
    .floating-save-bar {
        left: 50% !important;
        width: calc(100% - 32px) !important;
    }
}
@media (max-width: 575px) {
    .floating-save-bar {
        border-radius: 18px !important;
        padding: 10px 16px !important;
        bottom: 12px;
        flex-direction: column;
        gap: 10px;
        align-items: stretch !important;
        text-align: center;
    }
    .floating-save-bar .d-flex {
        justify-content: center;
    }
}

/* Premium Lightbox Overlay Styles */
.premium-lightbox {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(15, 23, 42, 0.95);
    z-index: 9999;
    display: none;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    animation: fadeIn 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
.lightbox-content-wrapper {
    max-width: 90%;
    max-height: 85%;
    border-radius: 16px !important; /* Edge conner / curved corners on wide image! */
    overflow: hidden;
    border: 2px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    transform: scale(0.95);
    animation: zoomIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}
@keyframes zoomIn {
    to { transform: scale(1); }
}
.lightbox-img {
    max-width: 100%;
    max-height: 80vh;
    display: block;
    object-fit: contain;
    background: #000;
}
.lightbox-close-btn {
    position: absolute;
    top: 24px;
    right: 32px;
    background: transparent;
    border: none;
    color: #ffffff;
    font-size: 2.5rem;
    cursor: pointer;
    opacity: 0.7;
    transition: all 0.2s ease;
    outline: none;
}
.lightbox-close-btn:hover {
    opacity: 1;
    transform: scale(1.1);
}

/* Breadcrumb styles */
.page-breadcrumb .breadcrumb {
    background: transparent;
    padding: 0;
}
.page-breadcrumb .breadcrumb-item a {
    color: #6c5ce7;
    font-weight: 600;
}
html[data-admin-theme="dark"] .page-breadcrumb .breadcrumb-item a {
    color: #a78bfa;
}
</style>

{{-- Premium Lightbox DOM Handler Script --}}
<script nonce="{{ Vite::cspNonce() }}">
function openLightbox(src) {
    const lightbox = document.getElementById('premiumLightbox');
    const img = document.getElementById('lightboxImage');
    img.src = src;
    lightbox.style.display = 'flex';
    document.body.style.overflow = 'hidden'; // Lock background scrolling
}

function closeLightbox() {
    const lightbox = document.getElementById('premiumLightbox');
    lightbox.style.display = 'none';
    document.body.style.overflow = ''; // Unlock scrolling
}
</script>
@endsection
