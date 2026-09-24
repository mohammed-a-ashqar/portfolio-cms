@php
    $locales = config('portfolio.locales');
    $current = $locales[app()->getLocale()] ?? reset($locales);
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $current['dir'] }}" class="scroll-pt-24">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name'))</title>
    <meta name="description" content="@yield('meta_description', '')">

    {{-- Open Graph: what a shared link looks like in chat apps and on LinkedIn. --}}
    <meta property="og:title" content="@yield('title', config('app.name'))">
    <meta property="og:description" content="@yield('meta_description', '')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    @hasSection('og_image')
        <meta property="og:image" content="@yield('og_image')">
        <meta name="twitter:card" content="summary_large_image">
    @endif

    @foreach ($locales as $code => $meta)
        <link rel="alternate" hreflang="{{ $code }}" href="{{ request()->fullUrlWithQuery(['lang' => $code]) }}">
    @endforeach

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-ink-50 font-sans text-ink-900 dark:bg-ink-950 dark:text-ink-100">

<a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:m-3 focus:rounded-lg focus:bg-accent-600 focus:px-4 focus:py-2 focus:text-white">
    {{ __('front.nav.home') }}
</a>

<header x-data="{ open: false }" class="sticky top-0 z-40 border-b border-ink-200/70 bg-ink-50/80 backdrop-blur dark:border-ink-800/70 dark:bg-ink-950/80">
    <nav class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-4 sm:px-6">
        <a href="{{ route('home') }}" class="font-display text-lg font-bold tracking-tight">
            {{ config('app.name') }}
        </a>

        <div class="hidden items-center gap-1 md:flex">
            @foreach ([
                'home' => route('home'),
                'work' => route('projects.index'),
                'reels' => route('reels.index'),
                'services' => route('services.index'),
                'contact' => route('contact.index'),
            ] as $key => $url)
                <a href="{{ $url }}"
                   @class([
                       'rounded-lg px-3 py-2 text-sm font-medium transition',
                       'text-accent-700 dark:text-accent-300' => request()->url() === $url,
                       'text-ink-600 hover:text-ink-900 dark:text-ink-400 dark:hover:text-white' => request()->url() !== $url,
                   ])>
                    {{ __("front.nav.{$key}") }}
                </a>
            @endforeach
        </div>

        <div class="flex items-center gap-2">
            <x-ui.locale-switcher />

            <a href="{{ route('quotes.create') }}" class="btn-primary hidden sm:inline-flex">
                {{ __('front.hero.cta_contact') }}
            </a>

            <button type="button" @click="open = !open"
                    class="rounded-lg p-2 text-ink-600 md:hidden dark:text-ink-300"
                    :aria-expanded="open" aria-label="Menu">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" x-show="!open" d="M4 7h16M4 12h16M4 17h16"/>
                    <path stroke-linecap="round" x-show="open" x-cloak d="M6 6l12 12M18 6L6 18"/>
                </svg>
            </button>
        </div>
    </nav>

    <div x-show="open" x-cloak x-collapse class="border-t border-ink-200 px-4 py-3 md:hidden dark:border-ink-800">
        @foreach (['home' => route('home'), 'work' => route('projects.index'), 'reels' => route('reels.index'), 'services' => route('services.index'), 'contact' => route('contact.index')] as $key => $url)
            <a href="{{ $url }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-ink-700 dark:text-ink-300">
                {{ __("front.nav.{$key}") }}
            </a>
        @endforeach
    </div>
</header>

<main id="main">
    @if (session('status'))
        <div class="mx-auto mt-4 max-w-6xl px-4 sm:px-6">
            <x-ui.alert type="success">{{ session('status') }}</x-ui.alert>
        </div>
    @endif

    @yield('content')
</main>

<footer class="mt-24 border-t border-ink-200 py-10 dark:border-ink-800">
    <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-4 px-4 text-sm text-ink-500 sm:flex-row sm:px-6 dark:text-ink-400">
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('front.footer.rights') }}</p>
        <p>{{ __('front.footer.built_with') }}</p>
    </div>
</footer>

</body>
</html>
