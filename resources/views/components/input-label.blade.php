@props(['value'])

<label {{ $attributes->merge(['class' => 'form-label fw-bold mb-1']) }}>
    {{ $value ?? $slot }}
</label>