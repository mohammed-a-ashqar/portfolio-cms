@extends('layouts.front')

@section('title', __('front.projects.title') . ' — ' . config('app.name'))
@section('meta_description', __('front.projects.subtitle'))

@section('content')

<section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">

    <header class="mb-10 max-w-2xl">
        <h1 class="font-display text-3xl font-bold tracking-tight sm:text-4xl">{{ __('front.projects.title') }}</h1>
        <p class="mt-3 text-lg text-ink-600 dark:text-ink-400">{{ __('front.projects.subtitle') }}</p>
    </header>

    {{-- Filters submit with GET so every result set has a shareable URL. --}}
    <form method="GET" action="{{ route('projects.index') }}" class="mb-8 grid gap-3 sm:grid-cols-[1fr_auto_auto]">
        <input type="search"
               name="search"
               value="{{ $filters->search }}"
               placeholder="{{ __('front.projects.search_placeholder') }}"
               class="field">

        <select name="category" class="field sm:w-48" onchange="this.form.submit()">
            <option value="">{{ __('front.projects.filter_all') }}</option>
            @foreach ($categories as $category)
                <option value="{{ $category->slug }}" @selected($filters->category === $category->slug)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>

        <select name="sort" class="field sm:w-44" onchange="this.form.submit()">
            @foreach (['manual', 'newest', 'oldest', 'popular'] as $option)
                <option value="{{ $option }}" @selected($filters->sort === $option)>
                    {{ __("front.sort.{$option}") }}
                </option>
            @endforeach
        </select>
    </form>

    @if ($technologies->isNotEmpty())
        <div class="mb-8 flex flex-wrap gap-1.5">
            @foreach ($technologies as $technology)
                @php $active = in_array($technology->slug, $filters->technologies, true); @endphp
                <a href="{{ request()->fullUrlWithQuery(['technologies' => $active ? null : [$technology->slug]]) }}"
                   @class([
                       'badge px-2.5 py-1 transition',
                       'bg-accent-600 text-white ring-accent-600' => $active,
                       'bg-white text-ink-600 ring-ink-200 hover:bg-ink-50 dark:bg-ink-900 dark:text-ink-300 dark:ring-ink-700' => ! $active,
                   ])>
                    {{ $technology->name }}
                </a>
            @endforeach
        </div>
    @endif

    @if ($projects->isEmpty())
        <x-ui.empty :title="__('front.projects.empty')" />
    @else
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($projects as $project)
                <x-project-card :project="$project" />
            @endforeach
        </div>

        <div class="mt-10">{{ $projects->links() }}</div>
    @endif

</section>

@endsection
