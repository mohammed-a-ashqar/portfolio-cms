@extends('layouts.admin')

@section('title', __('contact.title'))

@section('content')

<form method="GET" class="mb-5 flex flex-wrap gap-2">
    <select name="status" class="field w-44" onchange="this.form.submit()">
        <option value="">{{ __('admin.table.status') }}</option>
        @foreach ($statuses as $value => $label)
            <option value="{{ $value }}" @selected(($filters['status'] ?? null) === $value)>{{ $label }}</option>
        @endforeach
    </select>
    <input type="search" name="search" value="{{ $filters['search'] ?? '' }}"
           placeholder="{{ __('admin.actions.search') }}" class="field max-w-sm">
    <button type="submit" class="btn-ghost">{{ __('admin.actions.search') }}</button>
</form>

@if ($messages->isEmpty())
    <x-ui.empty :title="__('admin.table.empty')" />
@else
    <div class="card divide-y divide-ink-50 overflow-hidden dark:divide-ink-800/60">
        @foreach ($messages as $message)
            <a href="{{ route('admin.messages.show', $message) }}"
               @class([
                   'flex items-start justify-between gap-4 px-5 py-4 transition hover:bg-ink-50 dark:hover:bg-ink-800/40',
                   'bg-accent-50/40 dark:bg-accent-500/5' => $message->status === \App\Enums\MessageStatus::Unread,
               ])>
                <div class="min-w-0">
                    <p @class(['truncate text-sm', 'font-semibold' => $message->status === \App\Enums\MessageStatus::Unread])>
                        {{ $message->name }}
                        <span class="font-normal text-ink-500 dark:text-ink-400">&lt;{{ $message->email }}&gt;</span>
                    </p>
                    @if ($message->subject)
                        <p class="truncate text-sm font-medium">{{ $message->subject }}</p>
                    @endif
                    <p class="mt-0.5 line-clamp-1 text-sm text-ink-500 dark:text-ink-400">{{ $message->message }}</p>
                </div>
                <div class="shrink-0 text-end">
                    <x-ui.badge :classes="$message->status->badgeClasses()">{{ $message->status->label() }}</x-ui.badge>
                    <p class="mt-1.5 text-xs text-ink-400">{{ $message->created_at->diffForHumans() }}</p>
                </div>
            </a>
        @endforeach
    </div>

    <div class="mt-6">{{ $messages->links() }}</div>
@endif

@endsection
