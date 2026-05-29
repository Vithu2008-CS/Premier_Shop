@extends('layouts.admin_noble')
@section('title', 'Edit Slider')

@push('styles')
<style>
.slider-form-wrap { display: flex; gap: 1.5rem; align-items: flex-start; }
.slider-form-col  { flex: 1 1 0; min-width: 0; }
.slider-preview-col {
    width: 380px;
    flex-shrink: 0;
    position: sticky;
    top: 80px;
}
@media (max-width: 1100px) {
    .slider-form-wrap  { flex-direction: column; }
    .slider-preview-col { width: 100%; position: static; }
}
.preview-card {
    border-radius: 16px; overflow: hidden;
    border: 1px solid var(--border-color, #e8e8f7);
    background: var(--card-bg, #fff);
}
.preview-label {
    padding: 10px 16px 0;
    font-size: 0.7rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.45;
}
.preview-slide {
    position: relative; width: 100%; padding-top: 56.25%;
    background: #1a1a2e center/cover no-repeat; overflow: hidden;
    transition: background-image 0.4s ease;
}
.preview-slide-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(135deg, rgba(0,0,0,0.55) 0%, rgba(0,0,0,0.15) 70%);
    pointer-events: none;
}
.preview-slide-content {
    position: absolute; inset: 0;
    display: flex; flex-direction: column; justify-content: center;
    padding: 1.5rem; gap: 6px;
}
.preview-slide-content.align-left   { align-items: flex-start; text-align: left; }
.preview-slide-content.align-center { align-items: center;     text-align: center; }
.preview-slide-content.align-right  { align-items: flex-end;   text-align: right; }
.preview-title-lead { font-size: 0.6rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.2em; color: rgba(255,255,255,0.8); }
.preview-title-accent { font-size: 1.15rem; font-weight: 900; color: #fff; line-height: 1.2; text-shadow: 0 2px 8px rgba(0,0,0,0.3); }
.preview-subtitle { font-size: 0.68rem; color: rgba(255,255,255,0.7); max-width: 220px; line-height: 1.4; }
.preview-cta { display: inline-flex; align-items: center; gap: 5px; padding: 5px 14px; border-radius: 8px; background: #6c5ce7; color: #fff; font-size: 0.68rem; font-weight: 700; margin-top: 4px; }
.preview-meta { padding: 10px 16px 12px; display: flex; gap: 8px; flex-wrap: wrap; }
.preview-meta-badge { padding: 3px 10px; border-radius: 20px; font-size: 0.68rem; font-weight: 700; }
</style>
@endpush

@section('content')
<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.sliders.index') }}">Sliders</a></li>
        <li class="breadcrumb-item active">Edit Slider</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="font-family:'Outfit',sans-serif;">Edit Slider</h4>
        <p class="text-muted small mb-0">Update the content and appearance of this hero banner.</p>
    </div>
    <a href="{{ route('admin.sliders.index') }}" class="btn btn-outline-secondary btn-sm">
        <i data-feather="arrow-left" style="width:14px;height:14px;"></i> Back
    </a>
</div>

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

<div class="slider-form-wrap">

    {{-- ── Form Column ──────────────────────────────────────────── --}}
    <div class="slider-form-col">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form id="sliderForm" action="{{ route('admin.sliders.update', $slider) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Title</label>
                            <input id="fTitle" type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title', $slider->title) }}" maxlength="255">
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Subtitle</label>
                            <input id="fSubtitle" type="text" name="subtitle" class="form-control @error('subtitle') is-invalid @enderror"
                                   value="{{ old('subtitle', $slider->subtitle) }}" maxlength="255">
                            @error('subtitle')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Replace Image</label>
                            <input id="fImageFile" type="file" name="image_file" class="form-control @error('image_file') is-invalid @enderror" accept="image/*">
                            <div class="form-text">Leave blank to keep current image.</div>
                            @error('image_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Or External Image URL</label>
                            <input id="fImageLink" type="url" name="image_link" class="form-control @error('image_link') is-invalid @enderror"
                                   placeholder="https://example.com/banner.jpg" value="{{ str_contains($slider->image_path, 'http') ? $slider->image_path : '' }}">
                            <div class="form-text">Leave blank to keep current image.</div>
                            @error('image_link')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Link URL</label>
                            <input id="fLinkUrl" type="url" name="link_url" class="form-control @error('link_url') is-invalid @enderror"
                                   value="{{ old('link_url', $slider->link_url) }}">
                            @error('link_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Button Label</label>
                            <input id="fButtonText" type="text" name="button_text" class="form-control @error('button_text') is-invalid @enderror"
                                   value="{{ old('button_text', $slider->button_text) }}" maxlength="50">
                            @error('button_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Text Alignment</label>
                            <select id="fAlign" name="text_align" class="form-select">
                                <option value="left"   {{ old('text_align', $slider->text_align) == 'left'   ? 'selected' : '' }}>Left</option>
                                <option value="center" {{ old('text_align', $slider->text_align) == 'center' ? 'selected' : '' }}>Center</option>
                                <option value="right"  {{ old('text_align', $slider->text_align) == 'right'  ? 'selected' : '' }}>Right</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Display Priority</label>
                            <input type="number" name="order_priority" class="form-control"
                                   value="{{ old('order_priority', $slider->order_priority) }}" min="0">
                        </div>
                        <div class="col-md-4 d-flex align-items-end pb-1">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" id="fActive" class="form-check-input" value="1"
                                       {{ $slider->is_active ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="fActive">Visible on storefront</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i data-feather="save" style="width:15px;height:15px;"></i> Update Slider
                        </button>
                        <a href="{{ route('admin.sliders.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Live Preview Column ───────────────────────────────────── --}}
    <div class="slider-preview-col">
        <div class="preview-card">
            <div class="preview-label">Live Preview — 16:9</div>
            <div class="preview-slide" id="previewSlide" style="background-image: url('{{ $currentImg }}');">
                <div class="preview-slide-overlay"></div>
                <div class="preview-slide-content align-{{ $slider->text_align ?? 'center' }}" id="previewContent">
                    @php
                        $prevWords = explode(' ', $slider->title ?? 'Slide');
                        $prevAccent = count($prevWords) > 1 ? array_pop($prevWords) : ($slider->title ?? 'Slide');
                        $prevLead   = count($prevWords) > 0 ? implode(' ', $prevWords) : '';
                    @endphp
                    @if($prevLead)
                        <div class="preview-title-lead" id="previewLead">{{ $prevLead }}</div>
                    @else
                        <div class="preview-title-lead" id="previewLead" style="display:none;"></div>
                    @endif
                    <div class="preview-title-accent" id="previewAccent">{{ $prevAccent }}</div>
                    <div class="preview-subtitle" id="previewSub" {{ $slider->subtitle ? '' : 'style=display:none' }}>{{ $slider->subtitle }}</div>
                    <div class="preview-cta" id="previewCta">{{ $slider->button_text ?: 'Shop Now' }} →</div>
                </div>
            </div>
            <div class="preview-meta">
                <span class="preview-meta-badge badge {{ $slider->is_active ? 'bg-success bg-opacity-10 text-success' : 'bg-secondary bg-opacity-10 text-secondary' }}" id="previewStatus">
                    {{ $slider->is_active ? 'Active' : 'Inactive' }}
                </span>
                <span class="preview-meta-badge badge bg-secondary bg-opacity-10 text-secondary" id="previewAlign">
                    Align: {{ $slider->text_align ?? 'center' }}
                </span>
            </div>
        </div>
        <p class="text-muted mt-2" style="font-size:0.72rem;">Preview updates as you type. Actual appearance depends on the background image.</p>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof feather !== 'undefined') feather.replace();

    const previewSlide   = document.getElementById('previewSlide');
    const previewContent = document.getElementById('previewContent');
    const previewLead    = document.getElementById('previewLead');
    const previewAccent  = document.getElementById('previewAccent');
    const previewSub     = document.getElementById('previewSub');
    const previewCta     = document.getElementById('previewCta');
    const previewStatus  = document.getElementById('previewStatus');
    const previewAlign   = document.getElementById('previewAlign');

    function updateTitle(val) {
        const words = (val || 'Slider').trim().split(/\s+/);
        if (words.length > 1) {
            previewAccent.textContent = words.pop();
            previewLead.textContent   = words.join(' ');
            previewLead.style.display = '';
        } else {
            previewAccent.textContent = words[0] || 'Slider';
            previewLead.style.display = 'none';
        }
    }

    document.getElementById('fTitle')?.addEventListener('input', e => updateTitle(e.target.value));

    document.getElementById('fSubtitle')?.addEventListener('input', function () {
        previewSub.textContent   = this.value;
        previewSub.style.display = this.value ? '' : 'none';
    });

    document.getElementById('fButtonText')?.addEventListener('input', function () {
        previewCta.textContent = (this.value || 'Shop Now') + ' →';
    });

    document.getElementById('fAlign')?.addEventListener('change', function () {
        previewContent.className = 'preview-slide-content align-' + this.value;
        previewAlign.textContent = 'Align: ' + this.value;
    });

    document.getElementById('fActive')?.addEventListener('change', function () {
        if (this.checked) {
            previewStatus.className   = 'preview-meta-badge badge bg-success bg-opacity-10 text-success';
            previewStatus.textContent = 'Active';
        } else {
            previewStatus.className   = 'preview-meta-badge badge bg-secondary bg-opacity-10 text-secondary';
            previewStatus.textContent = 'Inactive';
        }
    });

    document.getElementById('fImageFile')?.addEventListener('change', function () {
        if (this.files[0]) {
            previewSlide.style.backgroundImage = 'url(' + URL.createObjectURL(this.files[0]) + ')';
        }
    });

    document.getElementById('fImageLink')?.addEventListener('input', function () {
        if (this.value) previewSlide.style.backgroundImage = 'url(' + this.value + ')';
    });
});
</script>
@endpush
