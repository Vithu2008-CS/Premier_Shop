{{-- Reusable slider card. Expects $slider (Promotion). --}}
@php
    $imgSrc = str_starts_with($slider->image_path, 'http')
        ? $slider->image_path
        : asset('storage/' . $slider->image_path);

    $posArrow = match($slider->button_position ?? 'bottom-center') {
        'top-left'      => '↖', 'top-center'    => '↑', 'top-right'     => '↗',
        'middle-left'   => '←', 'middle-center'  => '⊕', 'middle-right' => '→',
        'bottom-left'   => '↙', 'bottom-center'  => '↓', 'bottom-right'  => '↘',
        default         => '⊕',
    };
@endphp

<div class="card slider-card theme-card-bg">
    <div class="slider-card-thumb">
        <img src="{{ $imgSrc }}" alt="{{ $slider->title }}" class="slider-card-thumb-img" loading="lazy">
        <div class="slider-card-overlay"></div>

        <div class="slider-card-badges">
            <span class="slider-badge {{ $slider->is_active ? 'active' : 'inactive' }}">
                {{ $slider->is_active ? 'Live' : 'Off' }}
            </span>
            <span class="slider-badge priority">#{{ $slider->order_priority }}</span>
        </div>

        <div class="slider-pos-tag" title="Button position: {{ $slider->button_position ?? 'bottom-center' }}">
            {{ $posArrow }}
        </div>

        @if($slider->button_text)
        <div class="slider-cta-chip">
            <span>{{ $slider->button_text }}</span>
        </div>
        @endif
    </div>

    <div class="slider-card-body">
        <div class="slider-card-label text-theme-dark-bold">{{ $slider->title ?: '— No Label —' }}</div>
        @if($slider->link_url)
            <a href="{{ $slider->link_url }}" target="_blank" rel="noopener" class="slider-link-chip mt-1">
                <i data-feather="link" style="width:10px;height:10px;flex-shrink:0;"></i>
                {{ $slider->link_url }}
            </a>
        @endif
    </div>

    <div class="slider-card-footer">
        <a href="{{ route('admin.sliders.edit', $slider) }}" class="slider-action-btn btn-edit">
            <i data-feather="edit-2" style="width:11px;height:11px;"></i> Edit
        </a>

        <form action="{{ route('admin.sliders.toggle-active', $slider) }}" method="POST" class="d-inline m-0">
            @csrf @method('PATCH')
            <button type="submit" class="slider-action-btn {{ $slider->is_active ? 'btn-toggle-on' : 'btn-toggle-off' }}">
                <i data-feather="{{ $slider->is_active ? 'eye-off' : 'eye' }}" style="width:11px;height:11px;"></i>
                {{ $slider->is_active ? 'Disable' : 'Enable' }}
            </button>
        </form>

        <form action="{{ route('admin.sliders.destroy', $slider) }}" method="POST"
              onsubmit="return confirm('Delete this slider permanently?');" class="d-inline m-0">
            @csrf @method('DELETE')
            <button type="submit" class="slider-action-btn btn-delete" title="Delete">
                <i data-feather="trash-2" style="width:11px;height:11px;"></i>
            </button>
        </form>
    </div>
</div>
