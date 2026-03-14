@props([
    'text',
    'href' => '#',
    'outlined' => false,
    'class' => ''
])

<a href="{{ $href }}" class="chronicle-button {{ $outlined ? 'btn-outline' : '' }} {{ $class }}">
    <span>
        <em>{{ $text }}</em>
    </span>
    <span>
        <em>{{ $text }}</em>
    </span>
</a>
