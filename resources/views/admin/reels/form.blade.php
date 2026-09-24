@extends('layouts.admin')

@section('title', $reel->exists ? __('admin.actions.edit') . ' — ' . __('reels.title') : __('reels.actions.import'))

@section('content')

<form method="POST"
      action="{{ $reel->exists ? route('admin.reels.update', $reel) : route('admin.reels.store') }}"
      enctype="multipart/form-data"
      class="mx-auto max-w-3xl space-y-6">
    @csrf
    @if ($reel->exists)
        @method('PUT')
    @endif

    <div class="card space-y-5 p-6">

        <div>
            <label for="url" class="label">{{ __('reels.fields.url') }}</label>
            <input type="url" name="url" id="url"
                   value="{{ old('url', $reel->url) }}"
                   placeholder="{{ __('reels.actions.paste_url') }}"
                   required class="field">
            <p class="mt-1.5 text-xs text-ink-500 dark:text-ink-400">
                Instagram · YouTube Shorts · TikTok · direct MP4
            </p>
        </div>

        {{-- One tab per configured locale. Adding a locale to the config adds
             a tab here automatically. --}}
        <div x-data="{ tab: '{{ config('app.fallback_locale') }}' }">
            <div class="mb-2 flex gap-1 border-b border-ink-200 dark:border-ink-800">
                @foreach (config('portfolio.locales') as $code => $meta)
                    <button type="button" @click="tab = '{{ $code }}'"
                            class="-mb-px border-b-2 px-3 py-2 text-sm font-medium transition"
                            :class="tab === '{{ $code }}'
                                ? 'border-accent-600 text-accent-700 dark:text-accent-400'
                                : 'border-transparent text-ink-500 hover:text-ink-800 dark:hover:text-ink-200'">
                        {{ $meta['flag'] }} {{ $meta['native'] }}
                    </button>
                @endforeach
            </div>

            @foreach (config('portfolio.locales') as $code => $meta)
                <div x-show="tab === '{{ $code }}'" x-cloak>
                    <label for="caption_{{ $code }}" class="label">{{ __('reels.fields.caption') }}</label>
                    <textarea name="caption[{{ $code }}]" id="caption_{{ $code }}" rows="3"
                              dir="{{ $meta['dir'] }}"
                              class="field">{{ old("caption.{$code}", $reel->getTranslations('caption')[$code] ?? '') }}</textarea>
                </div>
            @endforeach
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="project_id" class="label">{{ __('reels.fields.project') }}</label>
                <select name="project_id" id="project_id" class="field">
                    <option value="">—</option>
                    @foreach ($projects as $project)
                        <option value="{{ $project->getKey() }}" @selected(old('project_id', $reel->project_id) == $project->getKey())>
                            {{ $project->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="duration_seconds" class="label">Duration (s)</label>
                <input type="number" name="duration_seconds" id="duration_seconds" min="1" max="3600"
                       value="{{ old('duration_seconds', $reel->duration_seconds) }}" class="field">
            </div>
        </div>

        <div>
            <label for="poster" class="label">{{ __('reels.fields.poster') }}</label>
            <input type="file" name="poster" id="poster" accept="image/*"
                   class="field file:me-3 file:rounded-lg file:border-0 file:bg-ink-100 file:px-3 file:py-1.5 file:text-sm dark:file:bg-ink-800 dark:file:text-ink-200">
            <p class="mt-1.5 text-xs text-ink-500 dark:text-ink-400">{{ __('reels.fields.poster_hint') }}</p>

            @if ($reel->poster_url)
                <img src="{{ $reel->poster_url }}" alt="" class="mt-3 h-32 w-auto rounded-lg object-cover">
            @endif
        </div>

        <div class="flex flex-wrap gap-6">
            <label class="flex items-center gap-2 text-sm">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $reel->is_active ?? true))
                       class="rounded border-ink-300 text-accent-600 focus:ring-accent-500 dark:border-ink-600 dark:bg-ink-800">
                {{ __('reels.fields.active') }}
            </label>

            <label class="flex items-center gap-2 text-sm">
                <input type="hidden" name="is_featured" value="0">
                <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $reel->is_featured))
                       class="rounded border-ink-300 text-accent-600 focus:ring-accent-500 dark:border-ink-600 dark:bg-ink-800">
                {{ __('reels.fields.featured') }}
            </label>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">{{ __('admin.actions.save') }}</button>
        <a href="{{ route('admin.reels.index') }}" class="btn-ghost">{{ __('admin.actions.cancel') }}</a>
    </div>
</form>

@endsection
