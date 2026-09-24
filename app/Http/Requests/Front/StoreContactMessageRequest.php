<?php

declare(strict_types=1);

namespace App\Http\Requests\Front;

use App\DTOs\ContactMessageData;
use Illuminate\Foundation\Http\FormRequest;

final class StoreContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'phone' => ['nullable', 'string', 'max:32'],
            'subject' => ['nullable', 'string', 'max:190'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            // Honeypot: a real visitor never fills a hidden field.
            'website' => ['prohibited'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('contact.fields.name'),
            'email' => __('contact.fields.email'),
            'phone' => __('contact.fields.phone'),
            'subject' => __('contact.fields.subject'),
            'message' => __('contact.fields.message'),
        ];
    }

    public function toData(): ContactMessageData
    {
        return new ContactMessageData(
            name: $this->string('name')->trim()->value(),
            email: $this->string('email')->lower()->trim()->value(),
            message: $this->string('message')->trim()->value(),
            phone: $this->string('phone')->trim()->value() ?: null,
            subject: $this->string('subject')->trim()->value() ?: null,
            ipAddress: $this->ip(),
            userAgent: substr((string) $this->userAgent(), 0, 512),
        );
    }
}
