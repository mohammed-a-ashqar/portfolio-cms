{{--
    The shared reel player.

    Rendered once per page inside an x-data="reelPlayer" scope. `x-if` means the
    iframe element is created on open and destroyed on close, which is what
    actually stops playback — hiding it with CSS would leave the video running
    and the audio playing behind the overlay.
--}}
<template x-teleport="body">
    <div x-show="open"
         x-cloak
         @keydown.escape.window="close()"
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         role="dialog"
         aria-modal="true">

        <div x-show="open"
             x-transition.opacity
             @click="close()"
             class="absolute inset-0 bg-black/80 backdrop-blur-sm"></div>

        <div x-show="open"
             x-transition
             class="relative z-10 w-full max-w-sm">

            <button type="button" @click="close()"
                    class="absolute -top-11 flex h-9 w-9 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-white/20 end-0"
                    aria-label="Close">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/>
                </svg>
            </button>

            <div class="overflow-hidden rounded-2xl bg-black shadow-2xl reel-frame">
                <template x-if="open && src">
                    <iframe :src="src"
                            class="h-full w-full"
                            frameborder="0"
                            scrolling="no"
                            allow="autoplay; encrypted-media; picture-in-picture"
                            allowfullscreen
                            referrerpolicy="strict-origin-when-cross-origin"></iframe>
                </template>
            </div>

            <p x-show="caption" x-text="caption"
               class="mt-3 text-center text-sm text-white/80"></p>
        </div>
    </div>
</template>
