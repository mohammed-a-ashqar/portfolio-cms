@extends('layouts.front')

@section('title', $project->title . ' — ' . config('app.name'))
@section('meta_description', $project->summary ?? '')
@if ($project->cover_url)
    @section('og_image', $project->cover_url)
@endif

@section('content')

<article class="mx-auto max-w-4xl px-4 py-16 sm:px-6">

    <nav class="mb-8 text-sm">
        <a href="{{ route('projects.index') }}" class="text-ink-500 hover:text-accent-600 dark:text-ink-400">
            &larr; {{ __('front.projects.all') }}
        </a>
    </nav>

    <header class="mb-8">
        @if ($project->category)
            <span class="text-sm font-semibold uppercase tracking-wide text-accent-600 dark:text-accent-400">
                {{ $project->category->name }}
            </span>
        @endif

        <h1 class="mt-2 font-display text-3xl font-bold leading-tight tracking-tight sm:text-5xl">
            {{ $project->title }}
        </h1>

        @if ($project->summary)
            <p class="mt-4 text-lg text-ink-600 dark:text-ink-400">{{ $project->summary }}</p>
        @endif

        <div class="mt-6 flex flex-wrap gap-3">
            @if ($project->project_url)
                <a href="{{ $project->project_url }}" target="_blank" rel="noopener noreferrer" class="btn-primary">
                    {{ __('front.projects.live_site') }}
                </a>
            @endif
            @if ($project->repository_url)
                <a href="{{ $project->repository_url }}" target="_blank" rel="noopener noreferrer" class="btn-ghost">
                    {{ __('front.projects.source_code') }}
                </a>
            @endif
        </div>
    </header>

    @if ($project->cover_url)
        <img src="{{ $project->cover_url }}"
             alt="{{ $project->title }}"
             class="mb-10 w-full rounded-2xl object-cover shadow-sm">
    @endif

    <dl class="mb-10 grid gap-6 rounded-2xl border border-ink-200 p-6 sm:grid-cols-3 dark:border-ink-800">
        @if ($project->client_name)
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-ink-500 dark:text-ink-400">{{ __('front.projects.client') }}</dt>
                <dd class="mt-1 font-medium">{{ $project->client_name }}</dd>
            </div>
        @endif

        @if ($project->completed_at)
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-ink-500 dark:text-ink-400">{{ __('front.projects.year') }}</dt>
                <dd class="mt-1 font-medium">{{ $project->completed_at->format('Y') }}</dd>
            </div>
        @endif

        @if ($project->technologies->isNotEmpty())
            <div class="sm:col-span-1">
                <dt class="text-xs font-semibold uppercase tracking-wide text-ink-500 dark:text-ink-400">{{ __('front.projects.built_with') }}</dt>
                <dd class="mt-1.5 flex flex-wrap gap-1.5">
                    @foreach ($project->technologies as $technology)
                        <span class="badge bg-ink-100 text-ink-600 ring-ink-200 dark:bg-ink-800 dark:text-ink-300 dark:ring-ink-700">
                            {{ $technology->name }}
                        </span>
                    @endforeach
                </dd>
            </div>
        @endif
    </dl>

    @if ($project->description)
        <div class="prose prose-ink max-w-none dark:prose-invert">
            {!! nl2br(e($project->description)) !!}
        </div>
    @endif

    @if ($project->images->isNotEmpty())
        <div class="mt-12 grid gap-4 sm:grid-cols-2">
            @foreach ($project->images as $image)
                <img src="{{ $image->url }}" alt="{{ $image->alt ?? '' }}" loading="lazy"
                     class="w-full rounded-xl object-cover shadow-sm">
            @endforeach
        </div>
    @endif

    @if ($project->reels->isNotEmpty())
        <section class="mt-14">
            <h2 class="mb-6 font-display text-xl font-bold">{{ __('reels.title') }}</h2>
            <div x-data="reelPlayer" class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                @foreach ($project->reels as $reel)
                    <x-reel-tile :reel="$reel" />
                @endforeach
                <x-reel-modal />
            </div>
        </section>
    @endif

    @if ($project->testimonials->isNotEmpty())
        <section class="mt-14 space-y-4">
            @foreach ($project->testimonials as $testimonial)
                <figure class="card p-6">
                    <blockquote class="text-ink-700 dark:text-ink-300">{{ $testimonial->content }}</blockquote>
                    <figcaption class="mt-3 text-sm font-semibold">
                        {{ $testimonial->author_name }}
                        <span class="font-normal text-ink-500 dark:text-ink-400">{{ $testimonial->author_company }}</span>
                    </figcaption>
                </figure>
            @endforeach
        </section>
    @endif

    @if ($related->isNotEmpty())
        <section class="mt-16 border-t border-ink-200 pt-12 dark:border-ink-800">
            <h2 class="mb-6 font-display text-xl font-bold">{{ __('front.projects.related') }}</h2>
            <div class="grid gap-6 sm:grid-cols-3">
                @foreach ($related as $item)
                    <x-project-card :project="$item" />
                @endforeach
            </div>
        </section>
    @endif

</article>

@endsection
