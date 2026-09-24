@extends('layouts.front')

@section('title', $service->title . ' — ' . config('app.name'))
@section('meta_description', $service->excerpt ?? '')

@section('content')

<section class="mx-auto max-w-5xl px-4 py-16 sm:px-6">

    <nav class="mb-8 text-sm">
        <a href="{{ route('services.index') }}" class="text-ink-500 hover:text-accent-600 dark:text-ink-400">
            &larr; {{ __('front.services.title') }}
        </a>
    </nav>

    <header class="mb-10 max-w-3xl">
        <h1 class="font-display text-3xl font-bold tracking-tight sm:text-5xl">{{ $service->title }}</h1>
        @if ($service->excerpt)
            <p class="mt-4 text-lg text-ink-600 dark:text-ink-400">{{ $service->excerpt }}</p>
        @endif
    </header>

    @if ($service->description)
        <div class="prose prose-ink mb-12 max-w-none dark:prose-invert">
            {!! nl2br(e($service->description)) !!}
        </div>
    @endif

    @if ($service->packages->isNotEmpty())
        <h2 class="mb-6 font-display text-2xl font-bold">{{ __('front.services.packages') }}</h2>
        <div class="grid gap-5 md:grid-cols-3">
            @foreach ($service->packages as $package)
                <x-package-card :package="$package" />
            @endforeach
        </div>
    @endif

</section>

@endsection
