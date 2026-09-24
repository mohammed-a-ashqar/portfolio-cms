@extends('layouts.admin')

@section('title', __('admin.dashboard'))

@section('content')

{{-- Stat tiles --}}
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <x-admin.stat-tile :label="__('admin.stats.projects')" :value="$totalProjects"
                       :sub="__('admin.stats.published') . ': ' . ($projectCounts['published'] ?? 0)"
                       :href="route('admin.projects.index')" />

    <x-admin.stat-tile :label="__('admin.stats.open_quotes')" :value="$openQuotes"
                       :href="route('admin.quotes.index')" accent />

    <x-admin.stat-tile :label="__('admin.stats.unread_messages')" :value="$unreadMessages"
                       :href="route('admin.messages.index')" />

    <x-admin.stat-tile :label="__('admin.stats.reels')" :value="$reelCount"
                       :href="route('admin.reels.index')" />
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-3">

    {{-- Trend: a plain CSS bar chart. No charting library for six numbers. --}}
    <div class="card p-5 lg:col-span-2">
        <h2 class="mb-5 text-sm font-semibold text-ink-700 dark:text-ink-300">
            {{ __('admin.stats.quotes_trend') }}
        </h2>

        @php $peak = max(1, max($quotesTrend ?: [0])); @endphp

        <div class="flex h-44 items-end gap-3">
            @foreach ($quotesTrend as $month => $count)
                <div class="flex flex-1 flex-col items-center gap-2">
                    <span class="text-xs font-semibold text-ink-500 dark:text-ink-400">{{ $count }}</span>
                    <div class="w-full rounded-t-lg bg-accent-500/80 transition-all"
                         style="height: {{ max(4, (int) round($count / $peak * 130)) }}px"
                         title="{{ $month }}: {{ $count }}"></div>
                    <span class="text-[11px] text-ink-400">{{ \Illuminate\Support\Carbon::parse($month . '-01')->isoFormat('MMM') }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Quote status breakdown --}}
    <div class="card p-5">
        <h2 class="mb-4 text-sm font-semibold text-ink-700 dark:text-ink-300">{{ __('quotes.title') }}</h2>
        <ul class="space-y-2.5">
            @foreach ($quoteCounts as $status => $count)
                @php $case = \App\Enums\QuoteStatus::from($status); @endphp
                <li class="flex items-center justify-between text-sm">
                    <x-ui.badge :classes="$case->badgeClasses()">{{ $case->label() }}</x-ui.badge>
                    <span class="font-semibold tabular-nums">{{ $count }}</span>
                </li>
            @endforeach
        </ul>
    </div>
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-2">

    {{-- Recent quotes --}}
    <div class="card overflow-hidden">
        <div class="flex items-center justify-between border-b border-ink-100 px-5 py-3.5 dark:border-ink-800">
            <h2 class="text-sm font-semibold">{{ __('admin.stats.recent_quotes') }}</h2>
            <a href="{{ route('admin.quotes.index') }}" class="text-xs font-semibold text-accent-600 dark:text-accent-400">
                {{ __('admin.actions.view') }}
            </a>
        </div>

        @forelse ($recentQuotes as $quote)
            <a href="{{ route('admin.quotes.show', $quote) }}"
               class="flex items-center justify-between gap-3 border-b border-ink-50 px-5 py-3 transition last:border-0 hover:bg-ink-50 dark:border-ink-800/60 dark:hover:bg-ink-800/50">
                <div class="min-w-0">
                    <p class="truncate text-sm font-medium">{{ $quote->name }}</p>
                    <p class="truncate text-xs text-ink-500 dark:text-ink-400">{{ $quote->reference }}</p>
                </div>
                <x-ui.badge :classes="$quote->status->badgeClasses()">{{ $quote->status->label() }}</x-ui.badge>
            </a>
        @empty
            <p class="px-5 py-8 text-center text-sm text-ink-400">{{ __('admin.table.empty') }}</p>
        @endforelse
    </div>

    {{-- Recent messages --}}
    <div class="card overflow-hidden">
        <div class="flex items-center justify-between border-b border-ink-100 px-5 py-3.5 dark:border-ink-800">
            <h2 class="text-sm font-semibold">{{ __('admin.stats.recent_messages') }}</h2>
            <a href="{{ route('admin.messages.index') }}" class="text-xs font-semibold text-accent-600 dark:text-accent-400">
                {{ __('admin.actions.view') }}
            </a>
        </div>

        @forelse ($recentMessages as $message)
            <a href="{{ route('admin.messages.show', $message) }}"
               class="flex items-center justify-between gap-3 border-b border-ink-50 px-5 py-3 transition last:border-0 hover:bg-ink-50 dark:border-ink-800/60 dark:hover:bg-ink-800/50">
                <div class="min-w-0">
                    <p class="truncate text-sm font-medium">{{ $message->name }}</p>
                    <p class="truncate text-xs text-ink-500 dark:text-ink-400">{{ $message->subject ?: $message->email }}</p>
                </div>
                <x-ui.badge :classes="$message->status->badgeClasses()">{{ $message->status->label() }}</x-ui.badge>
            </a>
        @empty
            <p class="px-5 py-8 text-center text-sm text-ink-400">{{ __('admin.table.empty') }}</p>
        @endforelse
    </div>
</div>

@endsection
