@extends('layouts.admin')

@section('title', __('reels.title'))

@section('content')

<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <div class="flex flex-wrap gap-1.5">
        <a href="{{ route('admin.reels.index') }}"
           @class([
               'badge px-3 py-1.5 text-sm',
               'bg-ink-900 text-white ring-ink-900 dark:bg-white dark:text-ink-900 dark:ring-white' => ! $provider,
               'bg-white text-ink-600 ring-ink-200 dark:bg-ink-900 dark:text-ink-300 dark:ring-ink-700' => (bool) $provider,
           ])>
            {{ __('reels.all_providers') }}
        </a>

        @foreach ($counts as $key => $count)
            @continue($count === 0)
            @php $case = \App\Enums\ReelProvider::from($key); @endphp
            <a href="{{ route('admin.reels.index', ['provider' => $key]) }}"
               @class([
                   'badge px-3 py-1.5 text-sm',
                   'bg-ink-900 text-white ring-ink-900 dark:bg-white dark:text-ink-900 dark:ring-white' => $provider === $key,
                   'bg-white text-ink-600 ring-ink-200 dark:bg-ink-900 dark:text-ink-300 dark:ring-ink-700' => $provider !== $key,
               ])>
                {{ $case->label() }} <span class="ms-1 opacity-60">{{ $count }}</span>
            </a>
        @endforeach
    </div>

    <a href="{{ route('admin.reels.create') }}" class="btn-primary">{{ __('reels.actions.import') }}</a>
</div>

@if ($reels->isEmpty())
    <x-ui.empty :title="__('reels.empty')" />
@else
    {{-- Drag a tile onto another to reorder; the new order is POSTed and the
         page reloads so the server order is always what you see. --}}
    <div x-data="sortableGrid('{{ route('admin.reels.reorder') }}')"
         class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">

        @foreach ($reels as $reel)
            <div data-id="{{ $reel->getKey() }}"
                 draggable="true"
                 @dragstart="start($event, {{ $reel->getKey() }})"
                 @dragover="over($event)"
                 @drop="drop($event, {{ $reel->getKey() }})"
                 class="card group relative cursor-move overflow-hidden p-0"
                 :class="dragging === {{ $reel->getKey() }} ? 'opacity-40' : ''">

                <div class="relative bg-ink-200 dark:bg-ink-800 reel-frame">
                    @if ($reel->poster_url)
                        <img src="{{ $reel->poster_url }}" alt="" loading="lazy"
                             class="absolute inset-0 h-full w-full object-cover">
                    @else
                        <div class="absolute inset-0 flex items-center justify-center text-xs font-semibold text-ink-500"
                             style="background: linear-gradient(140deg, {{ $reel->provider->brandColor() }}22, {{ $reel->provider->brandColor() }}55)">
                            {{ $reel->provider->label() }}
                        </div>
                    @endif

                    <span class="absolute top-2 rounded-full px-2 py-0.5 text-[10px] font-semibold text-white end-2"
                          style="background-color: {{ $reel->provider->brandColor() }}">
                        {{ $reel->provider->label() }}
                    </span>

                    @unless ($reel->is_active)
                        <span class="badge absolute bottom-2 bg-ink-900/80 text-white ring-ink-900 start-2">
                            {{ __('reels.fields.active') }}: ✕
                        </span>
                    @endunless

                    @if ($reel->is_featured)
                        <span class="absolute bottom-2 text-amber-400 end-2" title="{{ __('reels.fields.featured') }}">
                            <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                        </span>
                    @endif
                </div>

                <div class="p-3">
                    <p class="line-clamp-2 text-xs text-ink-600 dark:text-ink-400">
                        {{ $reel->caption ?: $reel->external_id }}
                    </p>

                    <div class="mt-2.5 flex items-center gap-2">
                        <a href="{{ route('admin.reels.edit', $reel) }}"
                           class="text-xs font-semibold text-accent-600 hover:underline dark:text-accent-400">
                            {{ __('admin.actions.edit') }}
                        </a>

                        <form method="POST" action="{{ route('admin.reels.destroy', $reel) }}"
                              onsubmit="return confirm('{{ __('admin.actions.confirm_delete') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-semibold text-rose-600 hover:underline">
                                {{ __('admin.actions.delete') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-8">{{ $reels->links() }}</div>
@endif

@endsection
