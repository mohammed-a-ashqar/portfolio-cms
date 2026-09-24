@props(['reel'])

{{--
    One reel in the grid.

    The iframe is NOT rendered here. A grid of twelve embedded players would
    pull in twelve third-party frames — slow, and every one of them a tracking
    surface. Instead each tile is a poster image plus a play button, and the
    iframe is mounted only when the visitor opens the modal.
--}}
<button type="button"
        @click="play('{{ $reel->embed_url }}', @js($reel->caption))"
        class="group relative block w-full overflow-hidden rounded-2xl bg-ink-200 text-start ring-1 ring-ink-200 transition hover:-translate-y-0.5 hover:shadow-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-accent-500 dark:bg-ink-800 dark:ring-ink-800 reel-frame"
        aria-label="{{ __('reels.watch') }}">

    @if ($reel->poster_url)
        <img src="{{ $reel->poster_url }}"
             alt="{{ $reel->caption ?? '' }}"
             loading="lazy"
             decoding="async"
             class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105">
    @else
        {{-- No poster yet: a branded placeholder beats a broken image icon. --}}
        <div class="absolute inset-0 flex items-center justify-center"
             style="background: linear-gradient(140deg, {{ $reel->provider->brandColor() }}22, {{ $reel->provider->brandColor() }}55)">
            <span class="font-display text-xs font-semibold uppercase tracking-widest text-ink-600 dark:text-ink-300">
                {{ $reel->provider->label() }}
            </span>
        </div>
    @endif

    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></div>

    {{-- Play affordance --}}
    <span class="absolute inset-0 flex items-center justify-center">
        <span class="flex h-12 w-12 items-center justify-center rounded-full bg-white/90 shadow-lg backdrop-blur transition group-hover:scale-110">
            <svg class="ms-0.5 h-5 w-5 text-ink-900" viewBox="0 0 24 24" fill="currentColor">
                <path d="M8 5v14l11-7z"/>
            </svg>
        </span>
    </span>

    {{-- Provider chip --}}
    <span class="absolute top-2 flex items-center gap-1 rounded-full px-2 py-1 text-[10px] font-semibold text-white end-2"
          style="background-color: {{ $reel->provider->brandColor() }}">
        {{ $reel->provider->label() }}
    </span>

    @if ($reel->duration_for_humans)
        <span class="absolute top-2 rounded bg-black/70 px-1.5 py-0.5 text-[10px] font-medium text-white start-2">
            {{ $reel->duration_for_humans }}
        </span>
    @endif

    @if ($reel->caption)
        <span class="absolute inset-x-0 bottom-0 line-clamp-2 p-3 text-xs font-medium leading-snug text-white">
            {{ $reel->caption }}
        </span>
    @endif
</button>
