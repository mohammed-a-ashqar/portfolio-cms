@props(['title' => null, 'icon' => 'inbox'])

<div {{ $attributes->class(['flex flex-col items-center justify-center rounded-2xl border border-dashed border-ink-300 px-6 py-16 text-center dark:border-ink-700']) }}>
    <svg class="mb-3 h-10 w-10 text-ink-300 dark:text-ink-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H6.911a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661Z"/>
    </svg>
    <p class="text-sm font-medium text-ink-500 dark:text-ink-400">{{ $title ?? $slot }}</p>
</div>
