@php
    $locales = config('portfolio.locales');
    $current = $locales[app()->getLocale()] ?? reset($locales);
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $current['dir'] }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">

    <title>@yield('title', __('admin.dashboard')) — {{ __('admin.brand') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-ink-100 font-sans text-ink-900 dark:bg-ink-950 dark:text-ink-100">

<div x-data="{ sidebar: false }" class="flex min-h-screen">

    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 z-40 w-64 shrink-0 border-ink-200 bg-white transition-transform md:static md:translate-x-0 start-0 border-e dark:border-ink-800 dark:bg-ink-900"
           :class="sidebar ? 'translate-x-0' : 'ltr:-translate-x-full rtl:translate-x-full md:translate-x-0'">

        <div class="flex h-16 items-center gap-2 border-b border-ink-200 px-5 dark:border-ink-800">
            <a href="{{ route('admin.dashboard') }}" class="font-display text-base font-bold">
                {{ __('admin.brand') }}
            </a>
        </div>

        <nav class="space-y-6 p-3">
            <div>
                <a href="{{ route('admin.dashboard') }}"
                   @class(['nav-link', 'nav-link-active' => request()->routeIs('admin.dashboard')])>
                    <x-admin.icon name="grid" />
                    {{ __('admin.nav.overview') }}
                </a>
            </div>

            <div>
                <p class="mb-1.5 px-3 text-xs font-semibold uppercase tracking-wide text-ink-400">
                    {{ __('admin.nav.content') }}
                </p>
                <a href="{{ route('admin.projects.index') }}"
                   @class(['nav-link', 'nav-link-active' => request()->routeIs('admin.projects.*')])>
                    <x-admin.icon name="folder" />
                    {{ __('admin.nav.projects') }}
                </a>
                <a href="{{ route('admin.reels.index') }}"
                   @class(['nav-link', 'nav-link-active' => request()->routeIs('admin.reels.*')])>
                    <x-admin.icon name="play" />
                    {{ __('admin.nav.reels') }}
                </a>
            </div>

            <div>
                <p class="mb-1.5 px-3 text-xs font-semibold uppercase tracking-wide text-ink-400">
                    {{ __('admin.nav.business') }}
                </p>
                <a href="{{ route('admin.quotes.index') }}"
                   @class(['nav-link', 'nav-link-active' => request()->routeIs('admin.quotes.*')])>
                    <x-admin.icon name="receipt" />
                    {{ __('admin.nav.quotes') }}
                </a>
                <a href="{{ route('admin.messages.index') }}"
                   @class(['nav-link', 'nav-link-active' => request()->routeIs('admin.messages.*')])>
                    <x-admin.icon name="mail" />
                    {{ __('admin.nav.messages') }}
                </a>
            </div>
        </nav>
    </aside>

    {{-- Backdrop for the mobile drawer --}}
    <div x-show="sidebar" x-cloak @click="sidebar = false"
         class="fixed inset-0 z-30 bg-black/40 md:hidden"></div>

    <div class="flex min-w-0 flex-1 flex-col">

        <header class="sticky top-0 z-20 flex h-16 items-center justify-between gap-3 border-b border-ink-200 bg-white px-4 sm:px-6 dark:border-ink-800 dark:bg-ink-900">
            <button type="button" @click="sidebar = !sidebar" class="rounded-lg p-2 md:hidden" aria-label="Menu">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/>
                </svg>
            </button>

            <h1 class="truncate font-display text-lg font-semibold">@yield('title', __('admin.dashboard'))</h1>

            <div class="flex items-center gap-2">
                <a href="{{ route('home') }}" target="_blank" rel="noopener" class="hidden text-sm text-ink-500 hover:text-accent-600 sm:inline dark:text-ink-400">
                    {{ __('front.nav.home') }} &nearr;
                </a>

                <x-ui.locale-switcher />

                <div x-data="{ open: false }" class="relative">
                    <button type="button" @click="open = !open" @click.outside="open = false"
                            class="flex h-9 w-9 items-center justify-center rounded-full bg-accent-600 text-sm font-semibold text-white">
                        {{ auth()->user()?->initials }}
                    </button>

                    <div x-show="open" x-cloak x-transition
                         class="absolute z-50 mt-2 w-48 overflow-hidden rounded-xl border border-ink-200 bg-white shadow-lg end-0 dark:border-ink-700 dark:bg-ink-900">
                        <div class="border-b border-ink-100 px-3 py-2.5 text-sm dark:border-ink-800">
                            <p class="truncate font-semibold">{{ auth()->user()?->name }}</p>
                            <p class="truncate text-xs text-ink-500 dark:text-ink-400">{{ auth()->user()?->role?->label() }}</p>
                        </div>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="w-full px-3 py-2.5 text-start text-sm text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40">
                                {{ __('admin.actions.logout') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 p-4 sm:p-6">
            @if (session('status'))
                <x-ui.alert type="success" class="mb-5">{{ session('status') }}</x-ui.alert>
            @endif

            <x-ui.errors />

            @yield('content')
        </main>
    </div>
</div>

</body>
</html>
