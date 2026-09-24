<x-mail::message>
# {{ __('contact.mail.greeting') }}

<x-mail::panel>
**{{ $message->name }}** &lt;{{ $message->email }}&gt;
@if ($message->phone)
<br>{{ $message->phone }}
@endif
</x-mail::panel>

@if ($message->subject)
## {{ $message->subject }}
@endif

{{ $message->message }}

<x-mail::button :url="route('admin.messages.show', $message)">
{{ __('quotes.mail.view_in_admin') }}
</x-mail::button>

</x-mail::message>
