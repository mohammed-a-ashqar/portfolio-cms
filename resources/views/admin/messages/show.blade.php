@extends('layouts.admin')

@section('title', $message->subject ?: __('contact.singular'))

@section('content')

<div class="mb-5">
    <a href="{{ route('admin.messages.index') }}" class="text-sm text-ink-500 hover:text-accent-600 dark:text-ink-400">
        &larr; {{ __('contact.title') }}
    </a>
</div>

<div class="mx-auto max-w-3xl space-y-6">
    <div class="card p-6">
        <div class="mb-5 flex flex-wrap items-start justify-between gap-3 border-b border-ink-100 pb-5 dark:border-ink-800">
            <div>
                <h2 class="font-display text-lg font-bold">{{ $message->name }}</h2>
                <p class="mt-0.5 text-sm text-ink-500 dark:text-ink-400">
                    <a href="mailto:{{ $message->email }}" class="hover:text-accent-600">{{ $message->email }}</a>
                    @if ($message->phone) · {{ $message->phone }} @endif
                </p>
            </div>
            <div class="text-end">
                <x-ui.badge :classes="$message->status->badgeClasses()">{{ $message->status->label() }}</x-ui.badge>
                <p class="mt-1.5 text-xs text-ink-400">{{ $message->created_at->toDayDateTimeString() }}</p>
            </div>
        </div>

        @if ($message->subject)
            <h3 class="mb-3 font-semibold">{{ $message->subject }}</h3>
        @endif

        <div class="whitespace-pre-line text-sm leading-relaxed text-ink-700 dark:text-ink-300">{{ $message->message }}</div>
    </div>

    <div class="flex flex-wrap gap-3">
        <a href="mailto:{{ $message->email }}?subject={{ rawurlencode('Re: ' . ($message->subject ?? '')) }}" class="btn-primary">
            {{ __('contact.messages.replied') }} &rarr;
        </a>

        @can('delete', $message)
            <form method="POST" action="{{ route('admin.messages.destroy', $message) }}"
                  onsubmit="return confirm('{{ __('admin.actions.confirm_delete') }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger">{{ __('admin.actions.delete') }}</button>
            </form>
        @endcan
    </div>
</div>

@endsection
