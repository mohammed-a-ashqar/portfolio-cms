@extends('layouts.front')

@section('title', config('app.name'))
@section('meta_description', __('front.projects.subtitle'))

@section('content')

{{-- Hero --}}
<section class="relative overflow-hidden">
    <div class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(60%_50%_at_50%_0%,theme(colors.accent.100),transparent)] dark:bg-[radial-gradient(60%_50%_at_50%_0%,theme(colors.accent.900/.35),transparent)]"></div>

    <div class="mx-auto max-w-6xl px-4 py-20 sm:px-6 sm:py-28">
        <p class="mb-4 text-sm font-semibold uppercase tracking-widest text-accent-600 dark:text-accent-400">
            {{ __('front.hero.eyebrow') }}
        </p>

        <h1 class="max-w-3xl font-display text-4xl font-bold leading-tight tracking-tight sm:text-6xl">
            {{ __('front.projects.title') }}
        </h1>

        <p class="mt-5 max-w-2xl text-lg text-ink-600 dark:text-ink-400">
            {{ __('front.projects.subtitle') }}
        </p>

        <div class="mt-8 flex flex-wrap gap-3">
            <a href="{{ route('projects.index') }}" class="btn-primary">{{ __('front.hero.cta_work') }}</a>
            <a href="{{ route('quotes.create') }}" class="btn-ghost">{{ __('front.hero.cta_contact') }}</a>
        </div>

        <dl class="mt-14 grid max-w-lg grid-cols-3 gap-6">
            <div>
                <dt class="text-sm text-ink-500 dark:text-ink-400">{{ __('front.nav.work') }}</dt>
                <dd class="font-display text-3xl font-bold">{{ $stats['projects'] ?? 0 }}</dd>
            </div>
            <div>
                <dt class="text-sm text-ink-500 dark:text-ink-400">{{ __('front.nav.reels') }}</dt>
                <dd class="font-display text-3xl font-bold">{{ $stats['reels'] ?? 0 }}</dd>
            </div>
            <div>
                <dt class="text-sm text-ink-500 dark:text-ink-400">{{ __('front.nav.services') }}</dt>
                <dd class="font-display text-3xl font-bold">{{ $stats['services'] ?? 0 }}</dd>
            </div>
        </dl>
    </div>
</section>

{{-- Featured projects --}}
@if ($featuredProjects->isNotEmpty())
    <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
        <div class="mb-8 flex items-end justify-between gap-4">
            <h2 class="font-display text-2xl font-bold sm:text-3xl">{{ __('front.projects.title') }}</h2>
            <a href="{{ route('projects.index') }}" class="shrink-0 text-sm font-semibold text-accent-600 hover:underline dark:text-accent-400">
                {{ __('front.projects.view_all') }} &rarr;
            </a>
        </div>

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($featuredProjects as $project)
                <x-project-card :project="$project" />
            @endforeach
        </div>
    </section>
@endif

{{-- Reels strip --}}
@if ($reels->isNotEmpty())
    <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
        <div class="mb-8 flex items-end justify-between gap-4">
            <div>
                <h2 class="font-display text-2xl font-bold sm:text-3xl">{{ __('reels.showcase_title') }}</h2>
                <p class="mt-2 text-ink-600 dark:text-ink-400">{{ __('reels.showcase_subtitle') }}</p>
            </div>
            <a href="{{ route('reels.index') }}" class="shrink-0 text-sm font-semibold text-accent-600 hover:underline dark:text-accent-400">
                {{ __('reels.title') }} &rarr;
            </a>
        </div>

        <div x-data="reelPlayer" class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
            @foreach ($reels as $reel)
                <x-reel-tile :reel="$reel" />
            @endforeach

            <x-reel-modal />
        </div>
    </section>
@endif

{{-- Skills --}}
@if ($skills->isNotEmpty())
    <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
        <h2 class="mb-8 font-display text-2xl font-bold sm:text-3xl">{{ __('front.skills.title') }}</h2>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($skills as $group => $items)
                <div class="card p-5">
                    <h3 class="mb-4 text-sm font-semibold uppercase tracking-wide text-ink-500 dark:text-ink-400">
                        {{ $group ?: __('front.skills.title') }}
                    </h3>
                    <ul class="space-y-3">
                        @foreach ($items as $skill)
                            <li>
                                <div class="mb-1 flex items-center justify-between text-sm">
                                    <span class="font-medium">{{ $skill->name }}</span>
                                    <span class="text-ink-400">{{ $skill->proficiency }}%</span>
                                </div>
                                <div class="h-1.5 overflow-hidden rounded-full bg-ink-100 dark:bg-ink-800">
                                    <div class="h-full rounded-full bg-accent-500" style="width: {{ $skill->proficiency }}%"></div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </section>
@endif

{{-- Services --}}
@if ($services->isNotEmpty())
    <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
        <h2 class="mb-2 font-display text-2xl font-bold sm:text-3xl">{{ __('front.services.title') }}</h2>
        <p class="mb-8 text-ink-600 dark:text-ink-400">{{ __('front.services.subtitle') }}</p>

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($services as $service)
                <a href="{{ route('services.show', $service->slug) }}" class="card group p-6 transition hover:-translate-y-0.5 hover:shadow-md">
                    <h3 class="font-display text-lg font-semibold group-hover:text-accent-600 dark:group-hover:text-accent-400">
                        {{ $service->title }}
                    </h3>
                    <p class="mt-2 line-clamp-3 text-sm text-ink-600 dark:text-ink-400">{{ $service->excerpt }}</p>
                    <span class="mt-4 inline-block text-sm font-semibold text-accent-600 dark:text-accent-400">
                        {{ __('front.services.view') }} &rarr;
                    </span>
                </a>
            @endforeach
        </div>
    </section>
@endif

{{-- Testimonials --}}
@if ($testimonials->isNotEmpty())
    <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
        <h2 class="mb-8 font-display text-2xl font-bold sm:text-3xl">{{ __('front.testimonials.title') }}</h2>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($testimonials as $testimonial)
                <figure class="card p-6">
                    <div class="mb-3 flex gap-0.5 text-amber-400" aria-label="{{ $testimonial->rating }}/5">
                        @for ($i = 0; $i < $testimonial->rating; $i++)
                            <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                        @endfor
                    </div>
                    <blockquote class="text-sm leading-relaxed text-ink-700 dark:text-ink-300">
                        {{ $testimonial->content }}
                    </blockquote>
                    <figcaption class="mt-4 flex items-center gap-3 border-t border-ink-100 pt-4 dark:border-ink-800">
                        @if ($testimonial->avatar_url)
                            <img src="{{ $testimonial->avatar_url }}" alt="" class="h-9 w-9 rounded-full object-cover" loading="lazy">
                        @endif
                        <div class="text-sm">
                            <p class="font-semibold">{{ $testimonial->author_name }}</p>
                            <p class="text-ink-500 dark:text-ink-400">{{ $testimonial->author_title }}{{ $testimonial->author_company ? ' · ' . $testimonial->author_company : '' }}</p>
                        </div>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </section>
@endif

@endsection
