@extends('layouts.admin')

@section('title', $quote->reference)

@section('content')

<div class="mb-5">
    <a href="{{ route('admin.quotes.index') }}" class="text-sm text-ink-500 hover:text-accent-600 dark:text-ink-400">
        &larr; {{ __('quotes.title') }}
    </a>
</div>

<div class="grid gap-6 lg:grid-cols-3">

    <div class="space-y-6 lg:col-span-2">
        <div class="card p-6">
            <div class="mb-5 flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="font-display text-xl font-bold">{{ $quote->name }}</h2>
                    <p class="mt-1 text-sm text-ink-500 dark:text-ink-400">
                        <a href="mailto:{{ $quote->email }}" class="hover:text-accent-600">{{ $quote->email }}</a>
                        @if ($quote->phone) · {{ $quote->phone }} @endif
                    </p>
                </div>
                <x-ui.badge :classes="$quote->status->badgeClasses()" class="text-sm">{{ $quote->status->label() }}</x-ui.badge>
            </div>

            <dl class="mb-6 grid gap-4 rounded-xl bg-ink-50 p-4 text-sm sm:grid-cols-2 dark:bg-ink-800/50">
                <div>
                    <dt class="text-xs text-ink-500 dark:text-ink-400">{{ __('contact.fields.company') }}</dt>
                    <dd class="font-medium">{{ $quote->company ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-ink-500 dark:text-ink-400">{{ __('quotes.budget') }}</dt>
                    <dd class="font-medium">{{ $quote->budgetRange() ?? __('quotes.no_budget') }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-ink-500 dark:text-ink-400">{{ __('front.services.title') }}</dt>
                    <dd class="font-medium">{{ $quote->service?->title ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-ink-500 dark:text-ink-400">{{ __('front.services.packages') }}</dt>
                    <dd class="font-medium">{{ $quote->package?->name ?? '—' }}</dd>
                </div>
                @if ($amount = $quote->quotedAmount())
                    <div class="sm:col-span-2">
                        <dt class="text-xs text-ink-500 dark:text-ink-400">{{ __('quotes.quoted_amount') }}</dt>
                        <dd class="font-display text-lg font-bold text-accent-600 dark:text-accent-400">{{ $amount->format() }}</dd>
                    </div>
                @endif
            </dl>

            <div class="whitespace-pre-line text-sm leading-relaxed text-ink-700 dark:text-ink-300">{{ $quote->message }}</div>
        </div>

        @if ($quote->admin_notes)
            <div class="card p-6">
                <h3 class="mb-2 text-sm font-semibold">{{ __('quotes.admin_notes') }}</h3>
                <p class="whitespace-pre-line text-sm text-ink-600 dark:text-ink-400">{{ $quote->admin_notes }}</p>
            </div>
        @endif
    </div>

    <div class="space-y-6">
        {{-- Workflow: only the transitions the state machine allows are offered. --}}
        @can('transition', $quote)
            @if (count($transitions) > 0)
                <form method="POST" action="{{ route('admin.quotes.transition', $quote) }}"
                      x-data="{ status: '{{ $transitions[0]->value }}' }"
                      class="card space-y-4 p-6">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="label" for="status">{{ __('admin.table.status') }}</label>
                        <select name="status" id="status" x-model="status" class="field">
                            @foreach ($transitions as $transition)
                                <option value="{{ $transition->value }}">{{ $transition->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div x-show="status === '{{ \App\Enums\QuoteStatus::Quoted->value }}'" x-cloak>
                        <label class="label" for="quoted_amount">{{ __('quotes.quoted_amount') }} ({{ $quote->currency }})</label>
                        <input type="number" name="quoted_amount" id="quoted_amount" min="0" step="any" class="field">
                    </div>

                    <div>
                        <label class="label" for="admin_notes">{{ __('quotes.admin_notes') }}</label>
                        <textarea name="admin_notes" id="admin_notes" rows="3" class="field">{{ old('admin_notes', $quote->admin_notes) }}</textarea>
                    </div>

                    <button type="submit" class="btn-primary w-full">{{ __('admin.actions.update') }}</button>
                </form>
            @endif
        @endcan

        <div class="card space-y-2 p-6 text-xs text-ink-500 dark:text-ink-400">
            <p>{{ __('admin.table.created') }}: {{ $quote->created_at->toDayDateTimeString() }}</p>
            @if ($quote->responded_at)
                <p>{{ __('enums.quote_status.quoted') }}: {{ $quote->responded_at->toDayDateTimeString() }}</p>
            @endif
            @if ($quote->ip_address)
                <p class="font-mono">IP: {{ $quote->ip_address }}</p>
            @endif
        </div>

        @can('delete', $quote)
            <form method="POST" action="{{ route('admin.quotes.destroy', $quote) }}"
                  onsubmit="return confirm('{{ __('admin.actions.confirm_delete') }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger w-full">{{ __('admin.actions.delete') }}</button>
            </form>
        @endcan
    </div>
</div>

@endsection
