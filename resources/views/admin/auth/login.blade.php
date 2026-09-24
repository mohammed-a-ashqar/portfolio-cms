@php
    $locales = config('portfolio.locales');
    $current = $locales[app()->getLocale()] ?? reset($locales);
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $current['dir'] }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ __('admin.auth.sign_in') }} — {{ __('admin.brand') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-ink-100 px-4 font-sans dark:bg-ink-950">

<div class="w-full max-w-sm">

    <div class="mb-6 text-center">
        <h1 class="font-display text-2xl font-bold">{{ __('admin.brand') }}</h1>
        <p class="mt-1.5 text-sm text-ink-500 dark:text-ink-400">{{ __('admin.auth.sign_in_subtitle') }}</p>
    </div>

    <div class="card p-6">
        <x-ui.errors />

        <form method="POST" action="{{ route('admin.login.attempt') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="label">{{ __('admin.auth.email') }}</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}"
                       required autofocus autocomplete="username" class="field">
            </div>

            <div>
                <label for="password" class="label">{{ __('admin.auth.password') }}</label>
                <input type="password" name="password" id="password"
                       required autocomplete="current-password" class="field">
            </div>

            <label class="flex items-center gap-2 text-sm text-ink-600 dark:text-ink-400">
                <input type="checkbox" name="remember" value="1"
                       class="rounded border-ink-300 text-accent-600 focus:ring-accent-500 dark:border-ink-600 dark:bg-ink-800">
                {{ __('admin.auth.remember') }}
            </label>

            <button type="submit" class="btn-primary w-full">{{ __('admin.auth.sign_in') }}</button>
        </form>
    </div>

    <p class="mt-5 text-center">
        <a href="{{ route('home') }}" class="text-sm text-ink-500 hover:text-accent-600 dark:text-ink-400">
            &larr; {{ __('front.nav.home') }}
        </a>
    </p>
</div>

</body>
</html>
