@extends('layouts.front')

@section('title', __('contact.form.title') . ' — ' . config('app.name'))
@section('meta_description', __('contact.form.subtitle'))

@section('content')

<section class="mx-auto max-w-xl px-4 py-16 sm:px-6">

    <header class="mb-8">
        <h1 class="font-display text-3xl font-bold tracking-tight sm:text-4xl">{{ __('contact.form.title') }}</h1>
        <p class="mt-3 text-ink-600 dark:text-ink-400">{{ __('contact.form.subtitle') }}</p>
    </header>

    <x-ui.errors />

    <form method="POST" action="{{ route('contact.store') }}" class="card space-y-5 p-6">
        @csrf

        {{-- Honeypot. Hidden from people, irresistible to naive bots; the
             FormRequest rejects any submission that fills it. --}}
        <div class="hidden" aria-hidden="true">
            <label for="website">Website</label>
            <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="name" class="label">{{ __('contact.fields.name') }}</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required class="field">
            </div>
            <div>
                <label for="email" class="label">{{ __('contact.fields.email') }}</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required class="field">
            </div>
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="phone" class="label">{{ __('contact.fields.phone') }}</label>
                <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" class="field">
            </div>
            <div>
                <label for="subject" class="label">{{ __('contact.fields.subject') }}</label>
                <input type="text" name="subject" id="subject" value="{{ old('subject') }}" class="field">
            </div>
        </div>

        <div>
            <label for="message" class="label">{{ __('contact.fields.message') }}</label>
            <textarea name="message" id="message" rows="6" required class="field">{{ old('message') }}</textarea>
        </div>

        <button type="submit" class="btn-primary w-full">{{ __('contact.form.submit') }}</button>
    </form>

</section>

@endsection
