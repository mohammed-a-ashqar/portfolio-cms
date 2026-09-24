@props([
    'label',
    'value',
    'sub' => null,
    'href' => null,
    'accent' => false,
])

@php
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }} @if ($href) href="{{ $href }}" @endif
    @class([
        'card block p-5 transition',
        'hover:-translate-y-0.5 hover:shadow-md' => (bool) $href,
        'ring-1 ring-accent-500/40' => $accent,
    ])>

    <p class="text-sm text-ink-500 dark:text-ink-400">{{ $label }}</p>

    <p @class([
        'mt-1.5 font-display text-3xl font-bold tabular-nums',
        'text-accent-600 dark:text-accent-400' => $accent,
    ])>{{ $value }}</p>

    @if ($sub)
        <p class="mt-1 text-xs text-ink-400">{{ $sub }}</p>
    @endif
</{{ $tag }}>
