@extends('layouts.front')

@section('title', __('quotes.form.title') . ' — ' . config('app.name'))
@section('meta_description', __('quotes.form.subtitle'))

@section('content')

<section class="mx-auto max-w-2xl px-4 py-16 sm:px-6">

    <header class="mb-8">
        <h1 class="font-display text-3xl font-bold tracking-tight sm:text-4xl">{{ __('quotes.form.title') }}</h1>
        <p class="mt-3 text-ink-600 dark:text-ink-400">{{ __('quotes.form.subtitle') }}</p>
    </header>

    <x-ui.errors />

    <form method="POST" action="{{ route('quotes.store') }}"
          x-data="{ service: '{{ old('service_id', request('service')) }}' }"
          class="card space-y-5 p-6">
        @csrf

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
                <label for="company" class="label">{{ __('contact.fields.company') }}</label>
                <input type="text" name="company" id="company" value="{{ old('company') }}" class="field">
            </div>
        </div>

        @if ($services->isNotEmpty())
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="service_id" class="label">{{ __('front.services.title') }}</label>
                    <select name="service_id" id="service_id" x-model="service" class="field">
                        <option value="">—</option>
                        @foreach ($services as $service)
                            <option value="{{ $service->getKey() }}">{{ $service->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="package_id" class="label">{{ __('front.services.packages') }}</label>
                    <select name="package_id" id="package_id" class="field">
                        <option value="">—</option>
                        @foreach ($services as $service)
                            @foreach ($service->packages as $package)
                                {{-- Packages stay in the DOM but only those belonging
                                     to the chosen service remain selectable. --}}
                                <option value="{{ $package->getKey() }}"
                                        x-show="!service || service === '{{ $service->getKey() }}'"
                                        @selected(old('package_id', request('package')) == $package->getKey())>
                                    {{ $service->title }} — {{ $package->name }}
                                </option>
                            @endforeach
                        @endforeach
                    </select>
                </div>
            </div>
        @endif

        <div class="grid gap-5 sm:grid-cols-3">
            <div>
                <label for="budget_min" class="label">{{ __('quotes.budget') }} ({{ __('pricing.from') }})</label>
                <input type="number" name="budget_min" id="budget_min" min="0" step="any" value="{{ old('budget_min') }}" class="field">
            </div>
            <div>
                <label for="budget_max" class="label">{{ __('quotes.budget') }} (max)</label>
                <input type="number" name="budget_max" id="budget_max" min="0" step="any" value="{{ old('budget_max') }}" class="field">
            </div>
            <div>
                <label for="currency" class="label">Currency</label>
                <select name="currency" id="currency" class="field">
                    @foreach (['USD', 'EUR', 'SAR', 'AED', 'EGP'] as $code)
                        <option value="{{ $code }}" @selected(old('currency') === $code)>{{ $code }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label for="message" class="label">{{ __('contact.fields.message') }}</label>
            <textarea name="message" id="message" rows="6" required minlength="20" class="field">{{ old('message') }}</textarea>
        </div>

        <button type="submit" class="btn-primary w-full">{{ __('quotes.form.submit') }}</button>
    </form>

</section>

@endsection
