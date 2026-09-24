<x-mail::message>
# {{ __('quotes.mail.greeting') }}

**{{ __('quotes.reference') }}:** {{ $quote->reference }}

<x-mail::panel>
**{{ $quote->name }}** &lt;{{ $quote->email }}&gt;
@if ($quote->company)
<br>{{ $quote->company }}
@endif
@if ($quote->phone)
<br>{{ $quote->phone }}
@endif
</x-mail::panel>

@if ($quote->service)
**{{ __('front.services.title') }}:** {{ $quote->service->title }}@if ($quote->package) — {{ $quote->package->name }}@endif

@endif
**{{ __('quotes.budget') }}:** {{ $quote->budgetRange() ?? __('quotes.no_budget') }}

---

{{ $quote->message }}

<x-mail::button :url="route('admin.quotes.show', $quote)">
{{ __('quotes.mail.view_in_admin') }}
</x-mail::button>

</x-mail::message>
