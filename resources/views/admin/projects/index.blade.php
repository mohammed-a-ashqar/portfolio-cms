@extends('layouts.admin')

@section('title', __('projects.title'))

@section('content')

<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <form method="GET" class="flex flex-wrap gap-2">
        <input type="search" name="search" value="{{ $filters->search }}"
               placeholder="{{ __('admin.actions.search') }}" class="field w-48">

        <select name="status" class="field w-40" onchange="this.form.submit()">
            <option value="">{{ __('admin.table.status') }}</option>
            @foreach ($statuses as $value => $label)
                <option value="{{ $value }}" @selected($filters->status === $value)>{{ $label }}</option>
            @endforeach
        </select>

        <select name="category" class="field w-40" onchange="this.form.submit()">
            <option value="">{{ __('projects.fields.category') }}</option>
            @foreach ($categories as $category)
                <option value="{{ $category->slug }}" @selected($filters->category === $category->slug)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="btn-ghost">{{ __('admin.actions.filter') }}</button>
    </form>

    <a href="{{ route('admin.projects.create') }}" class="btn-primary">{{ __('admin.actions.create') }}</a>
</div>

@if ($projects->isEmpty())
    <x-ui.empty :title="__('admin.table.empty')" />
@else
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-ink-100 bg-ink-50 text-xs uppercase tracking-wide text-ink-500 dark:border-ink-800 dark:bg-ink-800/50 dark:text-ink-400">
                    <tr>
                        <th class="px-5 py-3 text-start font-semibold">{{ __('projects.fields.title') }}</th>
                        <th class="px-5 py-3 text-start font-semibold">{{ __('projects.fields.category') }}</th>
                        <th class="px-5 py-3 text-start font-semibold">{{ __('admin.table.status') }}</th>
                        <th class="px-5 py-3 text-start font-semibold">{{ __('projects.fields.views') }}</th>
                        <th class="px-5 py-3 text-end font-semibold">{{ __('admin.table.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-50 dark:divide-ink-800/60">
                    @foreach ($projects as $project)
                        <tr class="transition hover:bg-ink-50 dark:hover:bg-ink-800/40">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    @if ($project->cover_url)
                                        <img src="{{ $project->cover_url }}" alt="" class="h-10 w-14 rounded object-cover">
                                    @else
                                        <div class="h-10 w-14 rounded bg-ink-100 dark:bg-ink-800"></div>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="truncate font-medium">{{ $project->title }}</p>
                                        @if ($project->is_featured)
                                            <span class="text-xs text-amber-600">★ {{ __('projects.fields.featured') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-ink-600 dark:text-ink-400">{{ $project->category?->name ?? '—' }}</td>
                            <td class="px-5 py-3">
                                <x-ui.badge :classes="$project->status->badgeClasses()">{{ $project->status->label() }}</x-ui.badge>
                            </td>
                            <td class="px-5 py-3 tabular-nums text-ink-600 dark:text-ink-400">{{ $project->views_count }}</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.projects.edit', $project) }}"
                                       class="font-semibold text-accent-600 hover:underline dark:text-accent-400">
                                        {{ __('admin.actions.edit') }}
                                    </a>
                                    <form method="POST" action="{{ route('admin.projects.destroy', $project) }}"
                                          onsubmit="return confirm('{{ __('admin.actions.confirm_delete') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-semibold text-rose-600 hover:underline">
                                            {{ __('admin.actions.delete') }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">{{ $projects->links() }}</div>
@endif

@endsection
