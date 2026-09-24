@props(['project'])

<a href="{{ route('projects.show', $project->slug) }}"
   class="card group flex flex-col overflow-hidden transition hover:-translate-y-0.5 hover:shadow-md">

    <div class="aspect-[16/10] overflow-hidden bg-ink-100 dark:bg-ink-800">
        @if ($project->cover_url)
            <img src="{{ $project->cover_url }}"
                 alt="{{ $project->title }}"
                 loading="lazy"
                 decoding="async"
                 class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
        @else
            <div class="flex h-full items-center justify-center">
                <span class="font-display text-2xl font-bold text-ink-300 dark:text-ink-600">
                    {{ mb_substr($project->title ?? '?', 0, 1) }}
                </span>
            </div>
        @endif
    </div>

    <div class="flex flex-1 flex-col p-5">
        @if ($project->category)
            <span class="mb-2 text-xs font-semibold uppercase tracking-wide text-accent-600 dark:text-accent-400">
                {{ $project->category->name }}
            </span>
        @endif

        <h3 class="font-display text-lg font-semibold leading-snug group-hover:text-accent-600 dark:group-hover:text-accent-400">
            {{ $project->title }}
        </h3>

        @if ($project->summary)
            <p class="mt-2 line-clamp-2 text-sm text-ink-600 dark:text-ink-400">{{ $project->summary }}</p>
        @endif

        @if ($project->relationLoaded('technologies') && $project->technologies->isNotEmpty())
            <div class="mt-4 flex flex-wrap gap-1.5">
                @foreach ($project->technologies->take(4) as $technology)
                    <span class="badge bg-ink-100 text-ink-600 ring-ink-200 dark:bg-ink-800 dark:text-ink-300 dark:ring-ink-700">
                        {{ $technology->name }}
                    </span>
                @endforeach
                @if ($project->technologies->count() > 4)
                    <span class="badge bg-ink-100 text-ink-500 ring-ink-200 dark:bg-ink-800 dark:text-ink-400 dark:ring-ink-700">
                        +{{ $project->technologies->count() - 4 }}
                    </span>
                @endif
            </div>
        @endif
    </div>
</a>
