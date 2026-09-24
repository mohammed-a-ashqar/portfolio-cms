@php
    $locales = config('portfolio.locales');
    $active = app()->getLocale();
@endphp

<div x-data="{ open: false }" class="relative">
    <button type="button" @click="open = !open" @click.outside="open = false"
            class="flex items-center gap-1.5 rounded-lg border border-ink-200 px-2.5 py-2 text-sm font-medium text-ink-700 transition hover:bg-ink-100 dark:border-ink-700 dark:text-ink-200 dark:hover:bg-ink-800"
            :aria-expanded="open" aria-haspopup="true">
        <span>{{ $locales[$active]['flag'] ?? '🌐' }}</span>
        <span class="hidden sm:inline">{{ $locales[$active]['native'] ?? $active }}</span>
        <svg class="h-4 w-4 text-ink-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/>
        </svg>
    </button>

    <div x-show="open" x-cloak x-transition
         class="absolute z-50 mt-2 w-44 overflow-hidden rounded-xl border border-ink-200 bg-white shadow-lg end-0 dark:border-ink-700 dark:bg-ink-900">
        @foreach ($locales as $code => $meta)
            {{-- fullUrlWithQuery keeps filters and pagination when switching. --}}
            <a href="{{ request()->fullUrlWithQuery(['lang' => $code]) }}"
               @class([
                   'flex items-center gap-2 px-3 py-2.5 text-sm transition',
                   'bg-accent-50 font-semibold text-accent-700 dark:bg-accent-500/10 dark:text-accent-300' => $code === $active,
                   'text-ink-700 hover:bg-ink-50 dark:text-ink-300 dark:hover:bg-ink-800' => $code !== $active,
               ])>
                <span>{{ $meta['flag'] }}</span>
                <span>{{ $meta['native'] }}</span>
            </a>
        @endforeach
    </div>
</div>
