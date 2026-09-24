@extends('layouts.admin')

@section('title', $project->exists ? __('admin.actions.edit') . ': ' . $project->title : __('admin.actions.create') . ' — ' . __('projects.singular'))

@section('content')

@php
    $locales = config('portfolio.locales');
    $selectedTech = old('technology_ids', $project->exists ? $project->technologies->pluck('id')->all() : []);
@endphp

<form method="POST"
      action="{{ $project->exists ? route('admin.projects.update', $project) : route('admin.projects.store') }}"
      enctype="multipart/form-data"
      class="grid gap-6 lg:grid-cols-3">
    @csrf
    @if ($project->exists)
        @method('PUT')
    @endif

    {{-- Main column: translatable content --}}
    <div class="space-y-6 lg:col-span-2">
        <div class="card p-6" x-data="{ tab: '{{ config('app.fallback_locale') }}' }">
            <div class="mb-5 flex gap-1 border-b border-ink-200 dark:border-ink-800">
                @foreach ($locales as $code => $meta)
                    <button type="button" @click="tab = '{{ $code }}'"
                            class="-mb-px border-b-2 px-3 py-2 text-sm font-medium transition"
                            :class="tab === '{{ $code }}'
                                ? 'border-accent-600 text-accent-700 dark:text-accent-400'
                                : 'border-transparent text-ink-500 hover:text-ink-800 dark:hover:text-ink-200'">
                        {{ $meta['flag'] }} {{ $meta['native'] }}
                        @if ($code === config('app.fallback_locale'))
                            <span class="text-rose-500">*</span>
                        @endif
                    </button>
                @endforeach
            </div>

            @foreach ($locales as $code => $meta)
                <div x-show="tab === '{{ $code }}'" x-cloak class="space-y-5" dir="{{ $meta['dir'] }}">
                    <div>
                        <label class="label" for="title_{{ $code }}">{{ __('projects.fields.title') }}</label>
                        <input type="text" name="title[{{ $code }}]" id="title_{{ $code }}"
                               value="{{ old("title.{$code}", $project->getTranslations('title')[$code] ?? '') }}"
                               class="field">
                    </div>

                    <div>
                        <label class="label" for="summary_{{ $code }}">{{ __('projects.fields.summary') }}</label>
                        <textarea name="summary[{{ $code }}]" id="summary_{{ $code }}" rows="2"
                                  class="field">{{ old("summary.{$code}", $project->getTranslations('summary')[$code] ?? '') }}</textarea>
                    </div>

                    <div>
                        <label class="label" for="description_{{ $code }}">{{ __('projects.fields.description') }}</label>
                        <textarea name="description[{{ $code }}]" id="description_{{ $code }}" rows="10"
                                  class="field">{{ old("description.{$code}", $project->getTranslations('description')[$code] ?? '') }}</textarea>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card space-y-5 p-6">
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="label" for="client_name">{{ __('projects.fields.client_name') }}</label>
                    <input type="text" name="client_name" id="client_name"
                           value="{{ old('client_name', $project->client_name) }}" class="field">
                </div>
                <div>
                    <label class="label" for="project_url">{{ __('projects.fields.project_url') }}</label>
                    <input type="url" name="project_url" id="project_url"
                           value="{{ old('project_url', $project->project_url) }}" class="field" dir="ltr">
                </div>
                <div>
                    <label class="label" for="repository_url">{{ __('projects.fields.repository_url') }}</label>
                    <input type="url" name="repository_url" id="repository_url"
                           value="{{ old('repository_url', $project->repository_url) }}" class="field" dir="ltr">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="label" for="started_at">{{ __('projects.fields.started_at') }}</label>
                        <input type="date" name="started_at" id="started_at"
                               value="{{ old('started_at', $project->started_at?->toDateString()) }}" class="field">
                    </div>
                    <div>
                        <label class="label" for="completed_at">{{ __('projects.fields.completed_at') }}</label>
                        <input type="date" name="completed_at" id="completed_at"
                               value="{{ old('completed_at', $project->completed_at?->toDateString()) }}" class="field">
                    </div>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <label class="label" for="gallery_images">{{ __('projects.fields.gallery') }}</label>
            <input type="file" name="gallery_images[]" id="gallery_images" accept="image/*" multiple
                   class="field file:me-3 file:rounded-lg file:border-0 file:bg-ink-100 file:px-3 file:py-1.5 file:text-sm dark:file:bg-ink-800 dark:file:text-ink-200">

            @if ($project->exists && $project->images->isNotEmpty())
                <div class="mt-4 grid grid-cols-4 gap-2">
                    @foreach ($project->images as $image)
                        <img src="{{ $image->url }}" alt="" class="aspect-video w-full rounded-lg object-cover">
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Side column: publishing --}}
    <div class="space-y-6">
        <div class="card space-y-5 p-6">
            <div>
                <label class="label" for="status">{{ __('projects.fields.status') }}</label>
                <select name="status" id="status" class="field">
                    @foreach ($statuses as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $project->status?->value) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <label class="flex items-center gap-2 text-sm">
                <input type="hidden" name="is_featured" value="0">
                <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $project->is_featured))
                       class="rounded border-ink-300 text-accent-600 focus:ring-accent-500 dark:border-ink-600 dark:bg-ink-800">
                {{ __('projects.fields.featured') }}
            </label>

            <div class="flex gap-2 border-t border-ink-100 pt-5 dark:border-ink-800">
                <button type="submit" class="btn-primary flex-1">{{ __('admin.actions.save') }}</button>
                <a href="{{ route('admin.projects.index') }}" class="btn-ghost">{{ __('admin.actions.cancel') }}</a>
            </div>
        </div>

        <div class="card space-y-5 p-6">
            <div>
                <label class="label" for="category_id">{{ __('projects.fields.category') }}</label>
                <select name="category_id" id="category_id" class="field">
                    <option value="">—</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->getKey() }}" @selected(old('category_id', $project->category_id) == $category->getKey())>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <p class="label">{{ __('projects.fields.technologies') }}</p>
                <div class="flex max-h-56 flex-wrap gap-1.5 overflow-y-auto">
                    @foreach ($technologies as $technology)
                        <label class="cursor-pointer">
                            <input type="checkbox" name="technology_ids[]" value="{{ $technology->getKey() }}"
                                   @checked(in_array($technology->getKey(), $selectedTech))
                                   class="peer sr-only">
                            <span class="badge bg-ink-50 px-2.5 py-1 text-ink-600 ring-ink-200 transition peer-checked:bg-accent-600 peer-checked:text-white peer-checked:ring-accent-600 dark:bg-ink-800 dark:text-ink-300 dark:ring-ink-700">
                                {{ $technology->name }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card p-6">
            <label class="label" for="cover_image">{{ __('projects.fields.cover_image') }}</label>
            @if ($project->cover_url)
                <img src="{{ $project->cover_url }}" alt="" class="mb-3 aspect-video w-full rounded-lg object-cover">
            @endif
            <input type="file" name="cover_image" id="cover_image" accept="image/*"
                   class="field file:me-3 file:rounded-lg file:border-0 file:bg-ink-100 file:px-3 file:py-1.5 file:text-sm dark:file:bg-ink-800 dark:file:text-ink-200">
        </div>
    </div>
</form>

@endsection
