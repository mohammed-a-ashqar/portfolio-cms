@props(['classes' => 'bg-ink-100 text-ink-700 ring-ink-600/20'])

<span {{ $attributes->class(['badge', $classes]) }}>{{ $slot }}</span>
