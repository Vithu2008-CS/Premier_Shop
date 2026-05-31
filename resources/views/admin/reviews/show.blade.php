@extends('layouts.admin_noble')
@section('title', 'Moderate Review REV-' . str_pad($review->id, 5, '0', STR_PAD_LEFT))

@section('content')
<div class="container-fluid px-0" style="padding-bottom: 120px;"> {{-- Leaves space for the floating action bar --}}
    <nav class="page-breadcrumb d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.reviews.index') }}">Reviews</a></li>
            <li class="breadcrumb-item active" aria-current="page">Review REV-{{ str_pad($review->id, 5, '0', STR_PAD_LEFT) }}</li>
        </ol>
        <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 d-inline-flex align-items-center">
            <i class="bi bi-arrow-left mr-2" style="font-size: 0.85rem;"></i> Back to List
        </a>
    </nav>

    <div class="row">
        {{-- Left Column: Review details, Customer, Product, Rating and Comment --}}
        <div class="col-lg-8 grid-margin stretch-card mb-4">
            <div class="card border-0 shadow-sm theme-card-bg w-100" style="border-radius: 18px !important; overflow: hidden;">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex align-items-center mb-4 pb-2 border-bottom-subtle">
                        <h5 class="card-title mb-0 font-weight-bold text-theme-dark-bold d-flex align-items-center" style="font-family: 'Outfit', sans-serif;">
                            <i class="bi bi-chat-square-text-fill text-primary mr-2" style="font-size: 1.25rem;"></i>
                            Customer Review Details
                        </h5>
                    </div>

                    {{-- Customer Info Card --}}
                    <h6 class="text-muted small font-weight-bold text-uppercase mb-3" style="letter-spacing: 0.8px;">Customer Information</h6>
                    <div class="d-flex align-items-center p-4 rounded-4 mb-4" style="background: rgba(108,92,231,0.03); border: 1.5px solid rgba(108,92,231,0.06);">
                        <div class="wd-52 h-52 rounded-circle bg-soft-primary text-primary font-weight-bold d-flex align-items-center justify-content-center mr-3 shadow-sm" style="font-size: 1.25rem; min-width: 52px; height: 52px;">
                            {{ strtoupper(substr($review->user->name, 0, 1)) }}
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 text-theme-dark-bold fw-700" style="font-size: 0.95rem;">{{ $review->user->name }}</h6>
                            <p class="text-muted small mb-0">{{ $review->user->email }}</p>
                        </div>
                    </div>

                    {{-- Product Association --}}
                    <h6 class="text-muted small font-weight-bold text-uppercase mb-3" style="letter-spacing: 0.8px;">Product Under Review</h6>
                    <div class="d-flex align-items-center p-3 rounded-4 mb-4 border" style="background: rgba(0,0,0,0.01); border-color: rgba(0,0,0,0.04) !important;">
                        <i class="bi bi-box-seam text-secondary mr-3" style="font-size: 1.5rem;"></i>
                        <div class="flex-grow-1">
                            <a href="{{ route('products.show', $review->product->slug) }}" target="_blank" class="font-weight-bold text-primary" style="font-size: 0.92rem;">
                                {{ $review->product->name }}
                            </a>
                            <p class="text-muted small mb-0 mt-0.5">Price: £{{ number_format($review->product->price, 2) }}</p>
                        </div>
                        <i class="bi bi-box-arrow-up-right text-muted d-none d-sm-block" style="font-size: 1rem;"></i>
                    </div>

                    {{-- Star Rating & Core Message --}}
                    <h6 class="text-muted small font-weight-bold text-uppercase mb-3" style="letter-spacing: 0.8px;">Review Content</h6>
                    <div class="p-4 rounded-4 mb-0" style="background: rgba(108,92,231,0.02); border-left: 4px solid #6c5ce7 !important;">
                        <div class="d-flex align-items-center mb-3">
                            <span class="text-muted small font-weight-bold mr-3">Rating Score:</span>
                            <div class="text-warning d-flex gap-0.5" style="font-size: 1.1rem;">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi {{ $i <= $review->rating ? 'bi-star-fill' : 'bi-star' }} mr-0.5"></i>
                                @endfor
                            </div>
                            <span class="badge px-3 py-1 bg-soft-primary font-weight-bold ml-3" style="font-size: 0.75rem; border-radius: 12px;">{{ $review->rating }} / 5 Stars</span>
                        </div>
                        
                        @if($review->title)
                            <h6 class="text-theme-dark-bold fw-700 mb-2 mt-3" style="font-size: 0.95rem;">"{{ $review->title }}"</h6>
                        @endif

                        <div class="text-theme-dark-bold small leading-normal p-3 rounded-3 bg-white" style="background-color: var(--statement-bg, #ffffff) !important; border: 1px solid rgba(0,0,0,0.04) !important; font-style: italic;">
                            "{{ $review->comment ?: 'No description or comment text provided by the customer.' }}"
                        </div>
                        
                        <div class="text-muted small mt-3">
                            Reviewed on: {{ $review->created_at->format('M d, Y \a\t H:i') }} ({{ $review->created_at->diffForHumans() }})
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Right Column: Process Moderation, Reply & Evidence Images --}}
        <div class="col-lg-4 flex-column d-flex gap-4">
            {{-- Process & Moderate Card --}}
            <div class="card border-0 shadow-sm theme-card-bg w-100 mb-4" style="border-radius: 18px !important;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4 pb-2 border-bottom-subtle">
                        <h5 class="card-title mb-0 font-weight-bold text-theme-dark-bold d-flex align-items-center" style="font-family: 'Outfit', sans-serif;">
                            <i class="bi bi-sliders text-info mr-2" style="font-size: 1.25rem;"></i>
                            Moderation & Reply
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

                    <form action="{{ route('admin.reviews.update', $review) }}" method="POST" id="process-review-form" class="process-review-form">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-4">
                            <label class="small text-muted font-weight-bold mb-2 d-flex align-items-center">
                                <i class="bi bi-flag text-primary mr-2"></i> Storefront Status
                            </label>
                            <select name="is_approved" class="form-control rounded-3" style="height: 42px !important;" required>
                                <option value="1" {{ $review->is_approved ? 'selected' : '' }}>Approved (Visible on Store)</option>
                                <option value="0" {{ ! $review->is_approved ? 'selected' : '' }}>Hidden (Storefront Hidden)</option>
                            </select>
                        </div>

                        <div class="form-group mb-4">
                            <label class="small text-muted font-weight-bold mb-2 d-flex align-items-center">
                                <i class="bi bi-reply-all-fill text-success mr-2"></i> Public Admin Reply
                            </label>
                            <textarea name="admin_reply" class="form-control rounded-3 text-dark-theme-aware" rows="6" style="resize: none;" placeholder="Draft a public reply under this customer review...">{{ old('admin_reply', $review->admin_reply) }}</textarea>
                            <small class="form-text text-muted" style="font-size: 0.72rem; line-height: 1.3;">
                                This response will appear publicly beneath the customer's comment on the storefront product page.
                            </small>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Review Photos Grid Card (Curved Square Thumbnails) --}}
            @if($review->photos && count($review->photos) > 0)
            <div class="card border-0 shadow-sm theme-card-bg w-100" style="border-radius: 18px !important;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3 pb-2 border-bottom-subtle">
                        <h5 class="card-title mb-0 font-weight-bold text-theme-dark-bold d-flex align-items-center" style="font-family: 'Outfit', sans-serif; font-size: 0.95rem;">
                            <i class="bi bi-images text-primary mr-2" style="font-size: 1.15rem;"></i>
                            Customer Uploaded Photos
                        </h5>
                    </div>

                    <p class="text-muted mb-3" style="font-size: 0.76rem; line-height: 1.45;">
                        Customer uploaded evidence ({{ count($review->photos) }}). Click a thumbnail to expand.
                    </p>
                    
                    <div class="evidence-grid">
                        @foreach($review->photos as $photo)
                        <div class="evidence-thumb-container" onclick="openLightbox('{{ Storage::url($photo) }}')">
                            <img src="{{ Storage::url($photo) }}" alt="Customer Review Photo" class="evidence-thumb">
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
            <span class="text-muted text-uppercase d-none d-sm-inline" style="font-size: 0.68rem; letter-spacing: 0.5px; font-weight: 600; white-space: nowrap; line-height: 1;">Product Review:</span>
            <span class="fw-bold text-nowrap floating-bar-title" style="font-size: 0.85rem; line-height: 1;" id="floating-return-title">REV-{{ str_pad($review->id, 5, '0', STR_PAD_LEFT) }}</span>
        </div>
    </div>
    <div class="button-group">
        <button type="button" class="btn btn-danger" onclick="event.preventDefault(); if(confirm('Are you sure you want to permanently delete this customer review? This action cannot be undone.')) document.getElementById('delete-review-form').submit();">
            <i class="bi bi-trash mr-2"></i> Delete
        </button>
        <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-light">
            Cancel
        </a>
        <button type="submit" form="process-review-form" class="btn btn-primary">
            <i class="bi bi-check2-circle mr-2"></i> Save
        </button>
    </div>
</div>

{{-- Hidden Deletion Form --}}
<form id="delete-review-form" action="{{ route('admin.reviews.destroy', $review) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

{{-- Premium Lightbox Overlay Modal --}}
<div id="premiumLightbox" class="premium-lightbox" onclick="closeLightbox()">
    <button class="lightbox-close-btn" onclick="closeLightbox()">&times;</button>
    <div class="lightbox-content-wrapper" onclick="event.stopPropagation()">
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
.gap-0.5 { gap: 0.15rem; }
.gap-2.5 { gap: 0.6rem; }
.mr-0.5 { margin-right: 0.12rem; }
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

/* Floating Custom Pink-Rose Delete Button (Mirroring Reference Screenshot!) */
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
<script>
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
