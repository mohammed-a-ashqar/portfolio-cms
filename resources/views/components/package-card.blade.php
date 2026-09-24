@props(['package'])

<div @class([
    'card relative flex flex-col p-6',
    'ring-2 ring-accent-500' => $package->is_popular,
])>
    @if ($package->is_popular)
        <span class="badge absolute -top-3 bg-accent-600 text-white ring-accent-600 start-6">
            {{ __('pricing.popular') }}
        </span>
    @endif

    <h3 class="font-display text-lg font-semibold">{{ $package->name }}</h3>

    @if ($package->description)
        <p class="mt-1.5 text-sm text-ink-600 dark:text-ink-400">{{ $package->description }}</p>
    @endif

    <p class="mt-5 flex items-baseline gap-1.5">
        <span class="font-display text-3xl font-bold">{{ $package->price()->format() }}</span>
        @if ($suffix = $package->billing_period->suffix())
            <span class="text-sm text-ink-500 dark:text-ink-400">{{ $suffix }}</span>
        @endif
    </p>

    <ul class="mt-5 flex-1 space-y-2.5 text-sm">
        @foreach ($package->localisedFeatures() as $feature)
            <li class="flex gap-2.5">
                <svg class="mt-0.5 h-4 w-4 shrink-0 text-accent-600 dark:text-accent-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/>
                </svg>
                <span class="text-ink-700 dark:text-ink-300">{{ $feature }}</span>
            </li>
        @endforeach
    </ul>

    <dl class="mt-5 space-y-1 border-t border-ink-100 pt-4 text-xs text-ink-500 dark:border-ink-800 dark:text-ink-400">
        @if ($package->delivery_days)
            <div>{{ __('pricing.delivery_days', ['days' => $package->delivery_days]) }}</div>
        @endif
        @if ($package->revisions)
            <div>{{ __('pricing.revisions', ['count' => $package->revisions]) }}</div>
        @endif
    </dl>

    <a href="{{ route('quotes.create', ['package' => $package->getKey(), 'service' => $package->service_id]) }}"
       @class([
           'mt-5 w-full',
           'btn-primary' => $package->is_popular,
           'btn-ghost' => ! $package->is_popular,
       ])>
        {{ __('pricing.choose') }}
    </a>
</div>
