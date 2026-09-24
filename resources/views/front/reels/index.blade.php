@extends('layouts.front')

@section('title', __('reels.showcase_title') . ' — ' . config('app.name'))
@section('meta_description', __('reels.showcase_subtitle'))

@section('content')

<section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">

    <header class="mb-10 max-w-2xl">
        <h1 class="font-display text-3xl font-bold tracking-tight sm:text-4xl">
            {{ __('reels.showcase_title') }}
        </h1>
        <p class="mt-3 text-lg text-ink-600 dark:text-ink-400">
            {{ __('reels.showcase_subtitle') }}
        </p>
    </header>

    {{-- Platform filter. Only shows platforms that actually have reels. --}}
    @php
        $available = collect($counts)->filter(fn ($count) => $count > 0);
    @endphp

    @if ($available->count() > 1)
        <div class="mb-8 flex flex-wrap gap-2">
            <a href="{{ route('reels.index') }}"
               @class([
                   'badge px-3 py-1.5 text-sm transition',
                   'bg-ink-900 text-white ring-ink-900 dark:bg-white dark:text-ink-900 dark:ring-white' => ! $provider,
                   'bg-white text-ink-600 ring-ink-200 hover:bg-ink-50 dark:bg-ink-900 dark:text-ink-300 dark:ring-ink-700' => (bool) $provider,
               ])>
                {{ __('reels.all_providers') }}
                <span class="ms-1.5 opacity-60">{{ $available->sum() }}</span>
            </a>

            @foreach ($available as $key => $count)
                @php $case = \App\Enums\ReelProvider::from($key); @endphp
                <a href="{{ route('reels.index', ['provider' => $key]) }}"
                   @class([
                       'badge px-3 py-1.5 text-sm transition',
                       'bg-ink-900 text-white ring-ink-900 dark:bg-white dark:text-ink-900 dark:ring-white' => $provider === $case,
                       'bg-white text-ink-600 ring-ink-200 hover:bg-ink-50 dark:bg-ink-900 dark:text-ink-300 dark:ring-ink-700' => $provider !== $case,
                   ])>
                    {{ $case->label() }}
                    <span class="ms-1.5 opacity-60">{{ $count }}</span>
                </a>
            @endforeach
        </div>
    @endif

    @if ($reels->isEmpty())
        <x-ui.empty :title="__('reels.empty')" />
    @else
        <div x-data="reelPlayer" class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
            @foreach ($reels as $reel)
                <x-reel-tile :reel="$reel" />
            @endforeach

            <x-reel-modal />
        </div>

        <div class="mt-10">
            {{ $reels->links() }}
        </div>
    @endif

</section>

@endsection
