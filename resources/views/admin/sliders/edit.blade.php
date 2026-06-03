@extends('layouts.admin_noble')
@section('title', 'Edit Slider')

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

/* Form inputs & select styling */
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

/* Border styling */
.border-bottom-subtle { border-bottom: 1.5px solid rgba(108,92,231,0.06) !important; }
html[data-admin-theme="dark"] .border-bottom-subtle { border-bottom: 1.5px solid rgba(255,255,255,0.05) !important; }

/* Live Hero Slide Mock-up Preview widget */
.preview-slide-container {
    border-radius: 18px !important;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0,0,0,0.04) !important;
    border: 1.5px solid rgba(0,0,0,0.05) !important;
}
html[data-admin-theme="dark"] .preview-slide-container {
    border-color: rgba(255,255,255,0.06) !important;
    box-shadow: 0 8px 25px rgba(0,0,0,0.25) !important;
}
.preview-slide {
    position: relative;
    width: 100%;
    padding-top: 56.25%; /* 16:9 ratio */
    background-color: #1a1a2e;
    background-position: center;
    background-size: cover;
    background-repeat: no-repeat;
    transition: background-image 0.4s ease;
}
.preview-slide-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(15,23,42,0.6) 0%, rgba(15,23,42,0.15) 75%);
    pointer-events: none;
}
.preview-slide-content {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 1.75rem;
    gap: 6px;
    color: #ffffff !important;
}
.preview-slide-content.align-left   { align-items: flex-start; text-align: left; }
.preview-slide-content.align-center { align-items: center;     text-align: center; }
.preview-slide-content.align-right  { align-items: flex-end;   text-align: right; }

.preview-title-lead {
    font-size: 0.72rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.15em;
    color: rgba(255, 255, 255, 0.75);
    font-family: 'Outfit', sans-serif;
    line-height: 1;
}
.preview-title-accent {
    font-size: 1.4rem;
    font-weight: 900;
    color: #ffffff;
    line-height: 1.2;
    word-break: break-word;
    text-shadow: 0 2px 10px rgba(0,0,0,0.35);
    font-family: 'Outfit', sans-serif;
}
.preview-subtitle {
    font-size: 0.78rem;
    color: rgba(255, 255, 255, 0.8);
    max-width: 250px;
    line-height: 1.35;
}
.preview-cta {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 14px;
    border-radius: 30px;
    background: linear-gradient(135deg,#6c5ce7,#a78bfa);
    color: #ffffff !important;
    font-size: 0.72rem;
    font-weight: 700;
    margin-top: 4px;
    white-space: nowrap;
    border: none;
    box-shadow: 0 4px 10px rgba(108,92,231,0.2);
}

.preview-meta {
    padding: 12px 16px;
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}
.preview-meta-badge {
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 0.65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Floating Save Action Bar */
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
.floating-save-bar .btn-danger {
    background: transparent !important; border: 1.8px solid #ff3366 !important; color: #ff3366 !important;
}
.floating-save-bar .btn-danger:hover { background: rgba(255,51,102,0.05) !important; box-shadow: 0 4px 12px rgba(255,51,102,0.15) !important; transform: translateY(-1px) !important; color: #ff3366 !important; }
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
            <li class="breadcrumb-item"><a href="{{ route('admin.sliders.index') }}">Sliders</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
        <a href="{{ route('admin.sliders.index') }}"
           class="btn btn-outline-primary btn-sm rounded-pill px-3 d-inline-flex align-items-center"
           style="border-radius:30px !important;font-weight:700;font-family:'Outfit';border:1.5px solid #6c5ce7;color:#6c5ce7;">
            <i class="bi bi-arrow-left mr-2" style="font-size:0.85rem;margin-right:6px;"></i> Back to Sliders
        </a>
    </nav>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-danger rounded-3 mb-4">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    @php
        $currentImg = str_starts_with($slider->image_path, 'http')
            ? $slider->image_path
            : asset('storage/' . $slider->image_path);
    @endphp

    <div class="row">
        {{-- Left: Registration Form --}}
        <div class="col-lg-7 mb-4">
            <form id="slider-edit-form" action="{{ route('admin.sliders.update', $slider) }}" method="POST" enctype="multipart/form-data" class="d-flex flex-column gap-4 w-100">
                @csrf
                @method('PUT')

                {{-- Banner Configurations Card --}}
                <div class="card border-0 shadow-sm theme-card-bg" style="border-radius:18px !important;">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex align-items-center mb-4 pb-2 border-bottom-subtle">
                            <h5 class="card-title mb-0 font-weight-bold text-theme-dark-bold d-flex align-items-center" style="font-family:'Outfit',sans-serif;">
                                <i class="bi bi-image text-primary mr-2" style="font-size:1.25rem;margin-right:8px;"></i>
                                Edit Banner Config
                            </h5>
                        </div>

                        {{-- Title & Subtitle --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small text-muted font-weight-bold">Title <span class="text-danger">*</span></label>
                                <input id="fTitle" type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                       value="{{ old('title', $slider->title) }}" maxlength="255" required>
                                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small text-muted font-weight-bold">Subtitle</label>
                                <input id="fSubtitle" type="text" name="subtitle" class="form-control @error('subtitle') is-invalid @enderror"
                                       value="{{ old('subtitle', $slider->subtitle) }}" maxlength="255">
                                @error('subtitle')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- Image Sources --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small text-muted font-weight-bold">Replace Image File</label>
                                <input id="fImageFile" type="file" name="image_file" class="form-control @error('image_file') is-invalid @enderror" accept="image/*">
                                <div class="form-text small text-muted">Leave blank to keep existing image file.</div>
                                @error('image_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small text-muted font-weight-bold">Or External Image URL</label>
                                <input id="fImageLink" type="url" name="image_link" class="form-control @error('image_link') is-invalid @enderror"
                                       placeholder="https://example.com/banner.jpg" 
                                       value="{{ str_contains($slider->image_path, 'http') ? $slider->image_path : '' }}">
                                <div class="form-text small text-muted">Leave blank to keep existing image path.</div>
                                @error('image_link')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- CTA --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small text-muted font-weight-bold">Link URL</label>
                                <input id="fLinkUrl" type="url" name="link_url" class="form-control @error('link_url') is-invalid @enderror"
                                       value="{{ old('link_url', $slider->link_url) }}">
                                @error('link_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small text-muted font-weight-bold">Button Label</label>
                                <input id="fButtonText" type="text" name="button_text" class="form-control @error('button_text') is-invalid @enderror"
                                       value="{{ old('button_text', $slider->button_text) }}" maxlength="50">
                                @error('button_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- Settings --}}
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label small text-muted font-weight-bold">Text Alignment</label>
                                <select id="fAlign" name="text_align" class="form-select">
                                    <option value="left"   {{ old('text_align', $slider->text_align) == 'left' ? 'selected' : '' }}>Left</option>
                                    <option value="center" {{ old('text_align', $slider->text_align) == 'center' ? 'selected' : '' }}>Center</option>
                                    <option value="right"  {{ old('text_align', $slider->text_align) == 'right' ? 'selected' : '' }}>Right</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label small text-muted font-weight-bold">Display Priority</label>
                                <input type="number" name="order_priority" class="form-control @error('order_priority') is-invalid @enderror" 
                                       value="{{ old('order_priority', $slider->order_priority) }}" min="0">
                                @error('order_priority')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 mb-3 d-flex align-items-end pb-2">
                                <div class="form-check">
                                    <label class="form-check-label font-weight-bold text-theme-dark-bold small cursor-pointer" for="fActive">
                                        <input type="checkbox" name="is_active" id="fActive" class="form-check-input" value="1" 
                                               {{ old('is_active', $slider->is_active) ? 'checked' : '' }}>
                                        Visible on storefront carousel
                                    </label>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>

        {{-- Right: Storefront Mock-up Slide Live Preview --}}
        <div class="col-lg-5 mb-4">
            <div class="d-flex flex-column gap-4 h-100 justify-content-between">
                
                {{-- Live Slider Banner Widget --}}
                <div class="card preview-slide-container border-0 p-0 overflow-hidden shadow-sm theme-card-bg">
                    <div class="preview-meta border-bottom-subtle">
                        <span class="font-weight-bold text-theme-dark-bold small text-uppercase" style="font-family: 'Outfit'; letter-spacing: 0.5px; font-size: 0.75rem;">Live Carousel Preview</span>
                    </div>

                    {{-- Mock Hero Banner slide --}}
                    <div class="preview-slide" id="previewSlide" style="background-image: url('{{ $currentImg }}');">
                        <div class="preview-slide-overlay"></div>
                        <div class="preview-slide-content align-{{ $slider->text_align ?? 'center' }}" id="previewContent">
                            @php
                                $prevWords = explode(' ', $slider->title ?? 'Slide');
                                $prevAccent = count($prevWords) > 1 ? array_pop($prevWords) : ($slider->title ?? 'Slide');
                                $prevLead   = count($prevWords) > 0 ? implode(' ', $prevWords) : '';
                            @endphp
                            <div class="preview-title-lead" id="previewLead" {{ $prevLead ? '' : 'style=display:none' }}>{{ $prevLead }}</div>
                            <div class="preview-title-accent" id="previewAccent">{{ $prevAccent }}</div>
                            <div class="preview-subtitle" id="previewSub" {{ $slider->subtitle ? '' : 'style=display:none' }}>{{ $slider->subtitle }}</div>
                            <a href="javascript:void(0);" class="preview-cta" id="previewCta">{{ $slider->button_text ?: 'Shop Now' }} →</a>
                        </div>
                    </div>

                    {{-- Badges footer --}}
                    <div class="preview-meta">
                        <span class="preview-meta-badge badge {{ $slider->is_active ? 'bg-success bg-opacity-10 text-success' : 'bg-secondary bg-opacity-10 text-secondary' }}" id="previewStatus">
                            {{ $slider->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        <span class="preview-meta-badge badge bg-secondary bg-opacity-10 text-secondary" id="previewAlign">Align: {{ $slider->text_align ?? 'center' }}</span>
                    </div>
                </div>

                {{-- Image Banner Tips guidelines card --}}
                <div class="card border-0 shadow-sm theme-card-bg p-4 rounded-4 mt-2">
                    <h6 class="card-title fw-bold text-primary mb-3 d-flex align-items-center" style="font-family:'Outfit',sans-serif; font-size: 0.9rem;">
                        <i class="bi bi-info-circle mr-2" style="margin-right: 6px;"></i> Slider Design Guidelines
                    </h6>
                    <ul class="text-muted small pl-3 mb-0" style="line-height: 1.6; padding-left: 20px;">
                        <li>Upload an image file or provide an external direct link to set the background of your slide.</li>
                        <li><strong>Text Alignment</strong> allows positioning text elements based on the content of the image.</li>
                        <li>Use clear visual contrast (avoid placing white text on a bright white image background).</li>
                        <li>The final word of your **Title** automatically becomes a highlighted accent heading.</li>
                    </ul>
                </div>

            </div>
        </div>
    </div>

</div>

{{-- Sticky Action Bar --}}
<div class="floating-save-bar d-flex align-items-center justify-content-between px-4 py-3">
    <div class="d-flex align-items-center" style="font-family:'Outfit',sans-serif;gap:8px;">
        <span class="pulse-green"></span>
        <span class="text-muted text-uppercase d-none d-sm-inline" style="font-size:0.68rem;letter-spacing:0.5px;font-weight:600;white-space:nowrap;">Active Banner:</span>
        <span class="font-weight-bold text-nowrap floating-bar-title text-theme-dark-bold" style="font-size:0.85rem; font-family: monospace; letter-spacing: 0.5px;">{{ $slider->title }}</span>
    </div>
    <div class="button-group">
        <button type="button" class="btn btn-danger"
                onclick="if(confirm('Permanently delete this home slider banner? This action cannot be undone.'))document.getElementById('delete-slider-form').submit();">
            <i class="bi bi-trash" style="margin-right:6px;"></i> Delete
        </button>
        <a href="{{ route('admin.sliders.index') }}" class="btn btn-outline-light">Cancel</a>
        <button type="submit" form="slider-edit-form" class="btn btn-primary">
            <i class="bi bi-check2-circle" style="margin-right:6px;"></i> Update Slider
        </button>
    </div>
</div>

{{-- Hidden Delete Form --}}
<form id="delete-slider-form" action="{{ route('admin.sliders.destroy', $slider) }}" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
$(function() {
    'use strict';

    const fTitle      = $('#fTitle');
    const fSubtitle   = $('#fSubtitle');
    const fButtonText = $('#fButtonText');
    const fAlign      = $('#fAlign');
    const fActive     = $('#fActive');
    const fImageFile  = $('#fImageFile');
    const fImageLink  = $('#fImageLink');

    const previewSlide    = $('#previewSlide');
    const previewContent  = $('#previewContent');
    const previewLead     = $('#previewLead');
    const previewAccent   = $('#previewAccent');
    const previewSub      = $('#previewSub');
    const previewCta      = $('#previewCta');
    const previewStatus   = $('#previewStatus');
    const previewAlign    = $('#previewAlign');
    const floatingTitle   = $('.floating-bar-title');

    function updatePreview() {
        // Handle Title parsing
        let titleVal = fTitle.val().trim();
        floatingTitle.text(titleVal.length > 0 ? titleVal : 'PENDING');

        if (titleVal.length > 0) {
            const words = titleVal.split(/\s+/);
            if (words.length > 1) {
                const accent = words.pop();
                const lead = words.join(' ');
                previewAccent.text(accent);
                previewLead.text(lead).show();
            } else {
                previewAccent.text(titleVal);
                previewLead.hide();
            }
        } else {
            previewAccent.text('New Banner Banner');
            previewLead.hide();
        }

        // Handle Subtitle
        let subVal = fSubtitle.val().trim();
        if (subVal.length > 0) {
            previewSub.text(subVal).show();
        } else {
            previewSub.hide();
        }

        // Handle CTA Button Label
        let btnText = fButtonText.val().trim();
        previewCta.text((btnText.length > 0 ? btnText : 'Shop Now') + ' →');

        // Handle Align
        let alignVal = fAlign.val() || 'center';
        previewContent.removeClass('align-left align-center align-right').addClass('align-' + alignVal);
        previewAlign.text('Align: ' + alignVal);

        // Handle Active Visibility status
        if (fActive.is(':checked')) {
            previewStatus.removeClass('bg-secondary text-secondary').addClass('bg-success text-success').text('Active');
        } else {
            previewStatus.removeClass('bg-success text-success').addClass('bg-secondary text-secondary').text('Inactive');
        }
    }

    // Attach event listeners
    fTitle.on('input', updatePreview);
    fSubtitle.on('input', updatePreview);
    fButtonText.on('input', updatePreview);
    fAlign.on('change', updatePreview);
    fActive.on('change', updatePreview);

    // Image file selection listener
    fImageFile.on('change', function() {
        if (this.files && this.files[0]) {
            const url = URL.createObjectURL(this.files[0]);
            previewSlide.css('background-image', 'url(' + url + ')');
        }
    });

    // External URL text input listener
    fImageLink.on('input', function() {
        let val = $(this).val().trim();
        if (val.length > 0) {
            previewSlide.css('background-image', 'url(' + val + ')');
        }
    });

    // Initialize preview state
    updatePreview();
});
</script>
@endpush
