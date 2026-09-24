@extends('layouts.front')

@section('title', __('front.services.title') . ' — ' . config('app.name'))
@section('meta_description', __('front.services.subtitle'))

@section('content')

<section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">

    <header class="mb-10 max-w-2xl">
        <h1 class="font-display text-3xl font-bold tracking-tight sm:text-4xl">{{ __('front.services.title') }}</h1>
        <p class="mt-3 text-lg text-ink-600 dark:text-ink-400">{{ __('front.services.subtitle') }}</p>
    </header>

    @if ($services->isEmpty())
        <x-ui.empty :title="__('front.services.empty')" />
    @else
        <div class="space-y-16">
            @foreach ($services as $service)
                <div>
                    <div class="mb-6">
                        <h2 class="font-display text-2xl font-bold">
                            <a href="{{ route('services.show', $service->slug) }}" class="hover:text-accent-600 dark:hover:text-accent-400">
                                {{ $service->title }}
                            </a>
                        </h2>
                        @if ($service->excerpt)
                            <p class="mt-2 max-w-2xl text-ink-600 dark:text-ink-400">{{ $service->excerpt }}</p>
                        @endif
                    </div>

                    @if ($service->packages->isNotEmpty())
                        <div class="grid gap-5 md:grid-cols-3">
                            @foreach ($service->packages as $package)
                                <x-package-card :package="$package" />
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

</section>

@endsection
