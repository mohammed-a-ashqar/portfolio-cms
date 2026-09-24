@extends('layouts.admin')

@section('title', __('quotes.title'))

@section('content')

<div class="mb-5 flex flex-wrap gap-1.5">
    <a href="{{ route('admin.quotes.index') }}"
       @class([
           'badge px-3 py-1.5 text-sm',
           'bg-ink-900 text-white ring-ink-900 dark:bg-white dark:text-ink-900 dark:ring-white' => empty($filters['status']),
           'bg-white text-ink-600 ring-ink-200 dark:bg-ink-900 dark:text-ink-300 dark:ring-ink-700' => ! empty($filters['status']),
       ])>
        {{ __('front.projects.filter_all') }} <span class="ms-1 opacity-60">{{ array_sum($counts) }}</span>
    </a>
    @foreach ($statuses as $value => $label)
        <a href="{{ route('admin.quotes.index', ['status' => $value]) }}"
           @class([
               'badge px-3 py-1.5 text-sm',
               'bg-ink-900 text-white ring-ink-900 dark:bg-white dark:text-ink-900 dark:ring-white' => ($filters['status'] ?? null) === $value,
               'bg-white text-ink-600 ring-ink-200 dark:bg-ink-900 dark:text-ink-300 dark:ring-ink-700' => ($filters['status'] ?? null) !== $value,
           ])>
            {{ $label }} <span class="ms-1 opacity-60">{{ $counts[$value] ?? 0 }}</span>
        </a>
    @endforeach
</div>

<form method="GET" class="mb-5 flex gap-2">
    <input type="hidden" name="status" value="{{ $filters['status'] ?? '' }}">
    <input type="search" name="search" value="{{ $filters['search'] ?? '' }}"
           placeholder="{{ __('admin.actions.search') }}" class="field max-w-sm">
    <button type="submit" class="btn-ghost">{{ __('admin.actions.search') }}</button>
</form>

@if ($quotes->isEmpty())
    <x-ui.empty :title="__('admin.table.empty')" />
@else
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-ink-100 bg-ink-50 text-xs uppercase tracking-wide text-ink-500 dark:border-ink-800 dark:bg-ink-800/50 dark:text-ink-400">
                    <tr>
                        <th class="px-5 py-3 text-start font-semibold">{{ __('quotes.reference') }}</th>
                        <th class="px-5 py-3 text-start font-semibold">{{ __('contact.fields.name') }}</th>
                        <th class="px-5 py-3 text-start font-semibold">{{ __('front.services.title') }}</th>
                        <th class="px-5 py-3 text-start font-semibold">{{ __('quotes.budget') }}</th>
                        <th class="px-5 py-3 text-start font-semibold">{{ __('admin.table.status') }}</th>
                        <th class="px-5 py-3 text-start font-semibold">{{ __('admin.table.created') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-50 dark:divide-ink-800/60">
                    @foreach ($quotes as $quote)
                        <tr class="cursor-pointer transition hover:bg-ink-50 dark:hover:bg-ink-800/40"
                            onclick="window.location='{{ route('admin.quotes.show', $quote) }}'">
                            <td class="px-5 py-3 font-mono text-xs font-semibold">{{ $quote->reference }}</td>
                            <td class="px-5 py-3">
                                <p class="font-medium">{{ $quote->name }}</p>
                                <p class="text-xs text-ink-500 dark:text-ink-400">{{ $quote->company ?: $quote->email }}</p>
                            </td>
                            <td class="px-5 py-3 text-ink-600 dark:text-ink-400">{{ $quote->service?->title ?? '—' }}</td>
                            <td class="px-5 py-3 text-ink-600 dark:text-ink-400">{{ $quote->budgetRange() ?? __('quotes.no_budget') }}</td>
                            <td class="px-5 py-3">
                                <x-ui.badge :classes="$quote->status->badgeClasses()">{{ $quote->status->label() }}</x-ui.badge>
                            </td>
                            <td class="px-5 py-3 text-ink-500 dark:text-ink-400">{{ $quote->created_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">{{ $quotes->links() }}</div>
@endif

@endsection
