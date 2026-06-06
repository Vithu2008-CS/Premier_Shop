@extends('layouts.admin_noble')
@section('title', 'Create Slider')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap');

.container-fluid { font-family: 'Inter', sans-serif; }
html[data-admin-theme="light"] .theme-card-bg { background-color: #ffffff !important; }
html[data-admin-theme="dark"]  .theme-card-bg { background-color: #0c1427 !important; border: 1px solid rgba(255,255,255,0.05) !important; }
html[data-admin-theme="light"] .text-theme-dark-bold { color: #1e293b !important; }
html[data-admin-theme="dark"]  .text-theme-dark-bold { color: #f1f5f9 !important; }

.form-control, .form-select {
    border-radius: 12px !important; border: 1.5px solid rgba(0,0,0,0.07) !important;
    padding: 0.5rem 0.95rem !important; font-size: 0.84rem !important;
    transition: all 0.25s ease !important; background-color: #ffffff !important; color: #1e293b !important;
}
.form-control:focus, .form-select:focus {
    border-color: #6c5ce7 !important; box-shadow: 0 0 0 3.5px rgba(108,92,231,0.15) !important;
}
html[data-admin-theme="dark"] .form-control, html[data-admin-theme="dark"] .form-select {
    background-color: #080f1d !important; border-color: rgba(255,255,255,0.08) !important; color: #e2e8f0 !important;
}
html[data-admin-theme="dark"] .form-control:focus, html[data-admin-theme="dark"] .form-select:focus {
    border-color: #a78bfa !important; box-shadow: 0 0 0 3.5px rgba(167,139,250,0.2) !important;
}
.form-control.is-invalid { border-color: #ff3366 !important; box-shadow: 0 0 0 3.5px rgba(255,51,102,0.15) !important; }

.border-bottom-subtle { border-bottom: 1.5px solid rgba(108,92,231,0.06) !important; }
html[data-admin-theme="dark"] .border-bottom-subtle { border-bottom: 1.5px solid rgba(255,255,255,0.05) !important; }

/* Type selector cards */
.type-card {
    border: 2px solid rgba(0,0,0,0.06); border-radius: 14px;
    padding: 14px 16px; cursor: pointer; transition: all 0.2s ease;
    background: transparent;
}
.type-card:hover { border-color: rgba(108,92,231,0.3); background: rgba(108,92,231,0.03); }
.type-card.selected { border-color: #6c5ce7; background: rgba(108,92,231,0.06); }
html[data-admin-theme="dark"] .type-card { border-color: rgba(255,255,255,0.07); }
html[data-admin-theme="dark"] .type-card:hover { border-color: rgba(167,139,250,0.35); background: rgba(167,139,250,0.05); }
html[data-admin-theme="dark"] .type-card.selected { border-color: #a78bfa; background: rgba(167,139,250,0.1); }
.type-card-icon { width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1rem; margin-bottom:8px; }
.type-card-label { font-size:0.82rem; font-weight:700; font-family:'Outfit',sans-serif; margin-bottom:2px; }
.type-card-desc  { font-size:0.7rem; color:#94a3b8; line-height:1.4; }
.type-card-size  { font-size:0.65rem; color:#a78bfa; font-family:monospace; margin-top:4px; }

/* 9-Position picker */
.pos-picker-wrap { display:flex; align-items:flex-start; gap:16px; flex-wrap:wrap; }
.pos-picker-grid {
    display:grid; grid-template-columns:repeat(3,1fr); gap:5px;
    padding:10px; border-radius:14px;
    background:rgba(108,92,231,0.04); border:1.5px solid rgba(108,92,231,0.1);
    width:110px; flex-shrink:0;
}
html[data-admin-theme="dark"] .pos-picker-grid {
    background:rgba(167,139,250,0.06); border-color:rgba(167,139,250,0.15);
}
.pos-cell {
    width:28px; height:28px; border-radius:7px;
    border:1.5px solid rgba(108,92,231,0.12);
    background:transparent; cursor:pointer;
    display:flex; align-items:center; justify-content:center;
    transition:all 0.15s ease; font-size:0.75rem; color:#94a3b8;
    line-height:1;
}
.pos-cell:hover { background:rgba(108,92,231,0.15); border-color:rgba(108,92,231,0.3); color:#6c5ce7; }
.pos-cell.active {
    background:linear-gradient(135deg,#6c5ce7,#a78bfa);
    border-color:transparent; color:#ffffff;
    box-shadow:0 2px 8px rgba(108,92,231,0.3);
}
html[data-admin-theme="dark"] .pos-cell { border-color:rgba(167,139,250,0.15); color:#64748b; }
html[data-admin-theme="dark"] .pos-cell:hover { background:rgba(167,139,250,0.15); border-color:rgba(167,139,250,0.35); color:#a78bfa; }
.pos-label {
    font-size:0.72rem; color:#6c5ce7; font-weight:600;
    background:rgba(108,92,231,0.08); padding:3px 10px; border-radius:20px;
    display:inline-block; margin-top:4px;
}
html[data-admin-theme="dark"] .pos-label { color:#a78bfa; background:rgba(167,139,250,0.12); }

/* Live preview */
.preview-slide-container {
    border-radius: 18px !important; overflow: hidden;
    box-shadow: 0 8px 25px rgba(0,0,0,0.04) !important;
    border: 1.5px solid rgba(0,0,0,0.05) !important;
}
html[data-admin-theme="dark"] .preview-slide-container {
    border-color: rgba(255,255,255,0.06) !important;
    box-shadow: 0 8px 25px rgba(0,0,0,0.25) !important;
}
.preview-slide {
    position: relative; width: 100%;
    padding-top: 56.25%; /* 16:9 default, updated by JS */
    background-color: #1a1a2e; background-position: center;
    background-size: cover; background-repeat: no-repeat;
}
.preview-btn-layer {
    position: absolute; inset: 0; display: flex;
    flex-direction: column; padding: 1rem; pointer-events: none;
}
.preview-btn-layer.pos-top-left     { justify-content:flex-start; align-items:flex-start; }
.preview-btn-layer.pos-top-center   { justify-content:flex-start; align-items:center; }
.preview-btn-layer.pos-top-right    { justify-content:flex-start; align-items:flex-end; }
.preview-btn-layer.pos-middle-left  { justify-content:center;     align-items:flex-start; }
.preview-btn-layer.pos-middle-center{ justify-content:center;     align-items:center; }
.preview-btn-layer.pos-middle-right { justify-content:center;     align-items:flex-end; }
.preview-btn-layer.pos-bottom-left  { justify-content:flex-end;   align-items:flex-start; }
.preview-btn-layer.pos-bottom-center{ justify-content:flex-end;   align-items:center; }
.preview-btn-layer.pos-bottom-right { justify-content:flex-end;   align-items:flex-end; }
.preview-cta-btn {
    display:inline-flex; align-items:center; gap:5px;
    padding:5px 14px; border-radius:30px;
    background:#ffffff; color:#1a1a2e;
    font-size:0.72rem; font-weight:700;
    box-shadow:0 2px 8px rgba(0,0,0,0.18);
    white-space:nowrap; pointer-events:none;
}
.preview-meta {
    padding: 12px 16px; display: flex; gap: 8px; flex-wrap: wrap; align-items: center;
}
.preview-meta-badge {
    padding: 3px 10px; border-radius: 20px; font-size: 0.65rem;
    font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;
}

/* Floating Action Bar */
.floating-save-bar {
    position: fixed; bottom: 24px; left: calc(50% + 120px);
    transform: translateX(-50%); z-index: 1000;
    width: calc(100% - 32px - 240px); max-width: 920px;
    background: rgba(255,255,255,0.9) !important;
    backdrop-filter: blur(12px) !important; -webkit-backdrop-filter: blur(12px) !important;
    border: 1px solid rgba(0,0,0,0.08) !important;
    box-shadow: 0 10px 30px rgba(0,0,0,0.06) !important;
    border-radius: 50px !important; transition: all 0.3s ease;
}
html[data-admin-theme="dark"] .floating-save-bar {
    background: rgba(15,23,42,0.9) !important; border-color: rgba(255,255,255,0.08) !important;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3) !important;
}
.floating-save-bar .btn {
    display:inline-flex; align-items:center; justify-content:center;
    height:38px !important; min-width:100px !important; padding:0 22px !important;
    font-size:0.82rem !important; font-weight:700 !important;
    border-radius:30px !important; transition:all 0.2s ease !important;
}
.floating-save-bar .btn-outline-secondary {
    border: 1.5px solid rgba(0,0,0,0.15) !important; background:transparent !important; color:#475569 !important;
}
.floating-save-bar .btn-outline-secondary:hover { background:rgba(0,0,0,0.04) !important; color:#1e293b !important; }
html[data-admin-theme="dark"] .floating-save-bar .btn-outline-secondary {
    border-color: rgba(255,255,255,0.25) !important; color: #cbd5e1 !important;
}
html[data-admin-theme="dark"] .floating-save-bar .btn-outline-secondary:hover {
    background:rgba(255,255,255,0.08) !important; color:#fff !important;
}
.floating-save-bar .btn-primary {
    background:linear-gradient(135deg,#6c5ce7,#a78bfa) !important;
    border:none !important; color:#fff !important; box-shadow:0 4px 12px rgba(108,92,231,0.2) !important;
}
.floating-save-bar .btn-primary:hover {
    transform:translateY(-1px) !important; box-shadow:0 6px 16px rgba(108,92,231,0.3) !important; color:#fff !important;
}
.floating-bar-title { color:#0f172a !important; }
html[data-admin-theme="dark"] .floating-bar-title { color:#ffffff !important; }
.pulse-dot {
    width:8px; height:8px; background:#10b981; border-radius:50%;
    display:inline-block; animation:blinkDot 1.5s infinite ease-in-out;
}
@keyframes blinkDot {
    0%,100% { opacity:0.3; transform:scale(0.9); }
    50%      { opacity:1;   transform:scale(1.15); }
}
@media (max-width:991px) { .floating-save-bar { left:50% !important; width:calc(100% - 32px) !important; } }
@media (max-width:575px) {
    .floating-save-bar { border-radius:20px !important; padding:12px 16px !important; bottom:16px !important; flex-direction:column; gap:10px; align-items:stretch !important; width:calc(100% - 24px) !important; }
    .floating-save-bar .btn { min-width:0 !important; flex:1; }
}
</style>
@endpush

@section('content')
<div class="container-fluid px-0" style="padding-bottom: 120px;">

    <nav class="page-breadcrumb d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.sliders.index') }}">Sliders</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create</li>
        </ol>
        <a href="{{ route('admin.sliders.index') }}"
           class="btn btn-outline-primary btn-sm d-inline-flex align-items-center"
           style="border-radius:30px !important;font-weight:700;font-family:'Outfit';border:1.5px solid #6c5ce7;color:#6c5ce7;padding:6px 18px;">
            <i class="bi bi-arrow-left" style="margin-right:6px;"></i> Back
        </a>
    </nav>

    @if($errors->any())
        <div class="alert alert-danger rounded-3 mb-4">
            <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="row g-4">
        {{-- Left: Form --}}
        <div class="col-lg-7">
            <form id="slider-form" action="{{ route('admin.sliders.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Slider Type --}}
                <div class="card border-0 shadow-sm theme-card-bg mb-4" style="border-radius:18px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3 pb-2 border-bottom-subtle">
                            <h5 class="card-title mb-0 fw-bold text-theme-dark-bold d-flex align-items-center" style="font-family:'Outfit';">
                                <i class="bi bi-collection text-primary me-2" style="font-size:1.1rem;"></i>
                                Slider Zone
                            </h5>
                        </div>

                        <input type="hidden" name="type" id="fType" value="{{ old('type', request('type', 'slider')) }}">
                        <div class="row g-2" id="typeCards">
                            <div class="col-12 col-md-4">
                                <div class="type-card {{ old('type', request('type','slider')) === 'slider' ? 'selected' : '' }}" data-type="slider">
                                    <div class="type-card-icon bg-soft-primary"><i class="bi bi-display"></i></div>
                                    <div class="type-card-label text-theme-dark-bold">Main Hero</div>
                                    <div class="type-card-desc">Full-width carousel at the top of the homepage.</div>
                                    <div class="type-card-size">1920×1080 / 800×1200</div>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="type-card {{ old('type', request('type','slider')) === 'slider_mid' ? 'selected' : '' }}" data-type="slider_mid">
                                    <div class="type-card-icon bg-soft-success"><i class="bi bi-layout-text-window"></i></div>
                                    <div class="type-card-label text-theme-dark-bold">Sub Banner 1</div>
                                    <div class="type-card-desc">After the New Arrivals section.</div>
                                    <div class="type-card-size">1920×600 / 800×800</div>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="type-card {{ old('type', request('type','slider')) === 'slider_top' ? 'selected' : '' }}" data-type="slider_top">
                                    <div class="type-card-icon bg-soft-warning"><i class="bi bi-layout-text-sidebar"></i></div>
                                    <div class="type-card-label text-theme-dark-bold">Sub Banner 2</div>
                                    <div class="type-card-desc">After the Recently Viewed section.</div>
                                    <div class="type-card-size">1920×600 / 800×800</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Image + CTA --}}
                <div class="card border-0 shadow-sm theme-card-bg mb-4" style="border-radius:18px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3 pb-2 border-bottom-subtle">
                            <h5 class="card-title mb-0 fw-bold text-theme-dark-bold d-flex align-items-center" style="font-family:'Outfit';">
                                <i class="bi bi-image text-primary me-2" style="font-size:1.1rem;"></i>
                                Banner Image & Button
                            </h5>
                        </div>

                        {{-- Image sources --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small text-muted fw-bold">Upload Image</label>
                                <input id="fImageFile" type="file" name="image_file"
                                       class="form-control @error('image_file') is-invalid @enderror" accept="image/*">
                                <div class="form-text small text-muted mt-1" id="sizeHint">
                                    Main: 1920×1080 (desktop) · 800×1200 (mobile)
                                </div>
                                @error('image_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted fw-bold">Or External Image URL</label>
                                <input id="fImageLink" type="url" name="image_link"
                                       class="form-control @error('image_link') is-invalid @enderror"
                                       placeholder="https://…/banner.jpg" value="{{ old('image_link') }}">
                                @error('image_link')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- Button text + link --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small text-muted fw-bold">Button Label</label>
                                <input id="fButtonText" type="text" name="button_text"
                                       class="form-control @error('button_text') is-invalid @enderror"
                                       placeholder="e.g. Shop Now" value="{{ old('button_text') }}" maxlength="50">
                                @error('button_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted fw-bold">Button Link (Product / Page URL)</label>
                                <input id="fLinkUrl" type="url" name="link_url"
                                       class="form-control @error('link_url') is-invalid @enderror"
                                       placeholder="{{ url('/products') }}" value="{{ old('link_url') }}">
                                @error('link_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- Button Position Picker --}}
                        <div class="mb-3">
                            <label class="form-label small text-muted fw-bold d-block mb-2">Button Position</label>
                            <div class="pos-picker-wrap">
                                <div class="pos-picker-grid" id="posPickerGrid">
                                    <button type="button" class="pos-cell" data-pos="top-left"      title="Top Left">↖</button>
                                    <button type="button" class="pos-cell" data-pos="top-center"    title="Top Center">↑</button>
                                    <button type="button" class="pos-cell" data-pos="top-right"     title="Top Right">↗</button>
                                    <button type="button" class="pos-cell" data-pos="middle-left"   title="Middle Left">←</button>
                                    <button type="button" class="pos-cell" data-pos="middle-center" title="Center">⊕</button>
                                    <button type="button" class="pos-cell" data-pos="middle-right"  title="Middle Right">→</button>
                                    <button type="button" class="pos-cell" data-pos="bottom-left"   title="Bottom Left">↙</button>
                                    <button type="button" class="pos-cell" data-pos="bottom-center" title="Bottom Center">↓</button>
                                    <button type="button" class="pos-cell" data-pos="bottom-right"  title="Bottom Right">↘</button>
                                </div>
                                <div>
                                    <div class="text-muted small mb-1">Selected:</div>
                                    <div class="pos-label" id="posLabel">Bottom Center</div>
                                    <input type="hidden" name="button_position" id="fButtonPos" value="{{ old('button_position','bottom-center') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Settings --}}
                <div class="card border-0 shadow-sm theme-card-bg" style="border-radius:18px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3 pb-2 border-bottom-subtle">
                            <h5 class="card-title mb-0 fw-bold text-theme-dark-bold d-flex align-items-center" style="font-family:'Outfit';">
                                <i class="bi bi-gear text-primary me-2" style="font-size:1.1rem;"></i>
                                Settings
                            </h5>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label small text-muted fw-bold">Internal Label (Admin Only)</label>
                                <input id="fTitle" type="text" name="title"
                                       class="form-control @error('title') is-invalid @enderror"
                                       placeholder="e.g. Summer Sale 2026" value="{{ old('title') }}" maxlength="255">
                                <div class="form-text small text-muted">Not shown on the storefront.</div>
                                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted fw-bold">Display Priority</label>
                                <input type="number" name="order_priority"
                                       class="form-control @error('order_priority') is-invalid @enderror"
                                       value="{{ old('order_priority', 0) }}" min="0">
                                <div class="form-text small text-muted">Lower = shown first.</div>
                                @error('order_priority')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3 d-flex align-items-end pb-1">
                                <div class="form-check">
                                    <input type="checkbox" name="is_active" id="fActive" class="form-check-input" value="1" checked>
                                    <label class="form-check-label fw-bold text-theme-dark-bold small" for="fActive">
                                        Live on storefront
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>

        {{-- Right: Live Preview --}}
        <div class="col-lg-5">
            <div class="d-flex flex-column gap-4">

                <div class="card preview-slide-container border-0 p-0 overflow-hidden shadow-sm theme-card-bg">
                    <div class="preview-meta border-bottom-subtle">
                        <span class="fw-bold text-theme-dark-bold small text-uppercase" style="font-family:'Outfit';letter-spacing:0.5px;font-size:0.73rem;">
                            Live Preview
                        </span>
                        <span class="ms-auto preview-meta-badge badge bg-secondary bg-opacity-10 text-secondary" id="previewType">Main Hero</span>
                    </div>

                    <div class="preview-slide" id="previewSlide">
                        <div class="preview-btn-layer pos-bottom-center" id="previewBtnLayer">
                            <span class="preview-cta-btn" id="previewCta" style="display:none;">Shop Now <i class="bi bi-arrow-right"></i></span>
                        </div>
                    </div>

                    <div class="preview-meta">
                        <span class="preview-meta-badge badge bg-success bg-opacity-10 text-success" id="previewStatus">Active</span>
                        <span class="preview-meta-badge badge bg-secondary bg-opacity-10 text-secondary" id="previewPos">Position: bottom-center</span>
                    </div>
                </div>

                <div class="card border-0 shadow-sm theme-card-bg p-4" style="border-radius:18px;">
                    <h6 class="card-title fw-bold text-primary mb-3 d-flex align-items-center" style="font-family:'Outfit';font-size:0.88rem;">
                        <i class="bi bi-info-circle me-2"></i> Design Guidelines
                    </h6>
                    <ul class="text-muted small mb-0" style="padding-left:18px;line-height:1.7;">
                        <li>Upload the image at the recommended resolution for best quality.</li>
                        <li>The <strong>Button Label</strong> and <strong>Link URL</strong> are both required for the button to appear on the storefront.</li>
                        <li>Use the <strong>Position Picker</strong> to place the button exactly where it works best with your image composition.</li>
                        <li>Keep button text short — 2–4 words work best (e.g. "Shop Now", "View Sale").</li>
                    </ul>
                </div>

            </div>
        </div>
    </div>

</div>

{{-- Floating Action Bar --}}
<div class="floating-save-bar d-flex align-items-center justify-content-between px-4 py-3">
    <div class="d-flex align-items-center gap-2" style="font-family:'Outfit',sans-serif;">
        <span class="pulse-dot"></span>
        <span class="text-muted text-uppercase d-none d-sm-inline" style="font-size:0.67rem;letter-spacing:0.5px;font-weight:600;">New Slider:</span>
        <span class="fw-bold text-nowrap floating-bar-title" style="font-size:0.84rem;font-family:monospace;" id="floatingLabel">PENDING</span>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.sliders.index') }}" class="btn btn-outline-secondary">Cancel</a>
        <button type="submit" form="slider-form" class="btn btn-primary">
            <i class="bi bi-check2-circle" style="margin-right:6px;"></i> Create Slider
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    /* ── Refs ─────────────────────────────────── */
    const fTitle      = document.getElementById('fTitle');
    const fButtonText = document.getElementById('fButtonText');
    const fImageFile  = document.getElementById('fImageFile');
    const fImageLink  = document.getElementById('fImageLink');
    const fActive     = document.getElementById('fActive');
    const fType       = document.getElementById('fType');
    const fButtonPos  = document.getElementById('fButtonPos');

    const previewSlide    = document.getElementById('previewSlide');
    const previewBtnLayer = document.getElementById('previewBtnLayer');
    const previewCta      = document.getElementById('previewCta');
    const previewStatus   = document.getElementById('previewStatus');
    const previewPos      = document.getElementById('previewPos');
    const previewType     = document.getElementById('previewType');
    const floatingLabel   = document.getElementById('floatingLabel');
    const sizeHint        = document.getElementById('sizeHint');
    const posLabel        = document.getElementById('posLabel');
    const posGrid         = document.getElementById('posPickerGrid');

    /* ── Type Cards ───────────────────────────── */
    const TYPE_META = {
        slider:     { label: 'Main Hero',    badge: 'Main Hero',    aspect: '56.25%', size: 'Main: 1920×1080 (desktop) · 800×1200 (mobile)' },
        slider_mid: { label: 'Sub Banner 1', badge: 'Sub Banner 1', aspect: '31.25%', size: 'Sub: 1920×600 (desktop) · 800×800 (mobile)' },
        slider_top: { label: 'Sub Banner 2', badge: 'Sub Banner 2', aspect: '31.25%', size: 'Sub: 1920×600 (desktop) · 800×800 (mobile)' },
    };

    document.querySelectorAll('#typeCards .type-card').forEach(function (card) {
        card.addEventListener('click', function () {
            document.querySelectorAll('#typeCards .type-card').forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            const t = this.getAttribute('data-type');
            fType.value = t;
            const meta = TYPE_META[t] || TYPE_META.slider;
            if (previewSlide) previewSlide.style.paddingTop = meta.aspect;
            if (sizeHint) sizeHint.textContent = meta.size;
            if (previewType) previewType.textContent = meta.badge;
        });
    });

    // Init preview aspect ratio
    (function () {
        const t = fType.value || 'slider';
        const meta = TYPE_META[t] || TYPE_META.slider;
        if (previewSlide) previewSlide.style.paddingTop = meta.aspect;
        if (previewType) previewType.textContent = meta.badge;
    })();

    /* ── Position Picker ──────────────────────── */
    const POS_LABELS = {
        'top-left':'Top Left','top-center':'Top Center','top-right':'Top Right',
        'middle-left':'Middle Left','middle-center':'Center','middle-right':'Middle Right',
        'bottom-left':'Bottom Left','bottom-center':'Bottom Center','bottom-right':'Bottom Right',
    };

    function selectPos(pos) {
        if (!pos) return;
        fButtonPos.value = pos;
        posGrid.querySelectorAll('.pos-cell').forEach(c => c.classList.toggle('active', c.getAttribute('data-pos') === pos));
        if (posLabel) posLabel.textContent = POS_LABELS[pos] || pos;
        if (previewBtnLayer) {
            previewBtnLayer.className = 'preview-btn-layer pos-' + pos;
        }
        if (previewPos) previewPos.textContent = 'Position: ' + (POS_LABELS[pos] || pos);
    }

    posGrid.querySelectorAll('.pos-cell').forEach(function (cell) {
        cell.addEventListener('click', function () { selectPos(this.getAttribute('data-pos')); });
    });
    selectPos(fButtonPos.value || 'bottom-center');

    /* ── Update preview state ─────────────────── */
    function updatePreview() {
        const btn = fButtonText ? fButtonText.value.trim() : '';
        if (previewCta) {
            if (btn) {
                previewCta.style.display = 'inline-flex';
                previewCta.innerHTML = btn + ' <i class="bi bi-arrow-right" style="margin-left:5px;"></i>';
            } else {
                previewCta.style.display = 'none';
            }
        }

        if (previewStatus) {
            const active = fActive && fActive.checked;
            previewStatus.className = 'preview-meta-badge badge ' + (active ? 'bg-success bg-opacity-10 text-success' : 'bg-secondary bg-opacity-10 text-secondary');
            previewStatus.textContent = active ? 'Active' : 'Inactive';
        }

        const title = fTitle ? fTitle.value.trim() : '';
        if (floatingLabel) floatingLabel.textContent = title || 'PENDING';
    }

    if (fButtonText) fButtonText.addEventListener('input', updatePreview);
    if (fTitle)      fTitle.addEventListener('input', updatePreview);
    if (fActive)     fActive.addEventListener('change', updatePreview);

    /* ── Image preview ────────────────────────── */
    if (fImageFile) {
        fImageFile.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                const url = URL.createObjectURL(this.files[0]);
                previewSlide.style.backgroundImage = 'url(' + url + ')';
            }
        });
    }
    if (fImageLink) {
        fImageLink.addEventListener('input', function () {
            const val = this.value.trim();
            if (val) previewSlide.style.backgroundImage = 'url(' + val + ')';
        });
    }

    updatePreview();
})();
</script>
@endpush
