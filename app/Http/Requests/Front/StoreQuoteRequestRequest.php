<?php

declare(strict_types=1);

namespace App\Http\Requests\Front;

use App\DTOs\QuoteRequestData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreQuoteRequestRequest extends FormRequest
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
            'company' => ['nullable', 'string', 'max:190'],
            'service_id' => ['nullable', Rule::exists('services', 'id')->where('is_active', true)],
            'package_id' => ['nullable', Rule::exists('packages', 'id')->where('is_active', true)],
            'budget_min' => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'budget_max' => ['nullable', 'numeric', 'min:0', 'max:100000000', 'gte:budget_min'],
            'currency' => ['nullable', 'string', 'size:3'],
            'message' => ['required', 'string', 'min:20', 'max:5000'],
            'website' => ['prohibited'], // honeypot
        ];
    }

    public function toData(): QuoteRequestData
    {
        return new QuoteRequestData(
            name: $this->string('name')->trim()->value(),
            email: $this->string('email')->lower()->trim()->value(),
            message: $this->string('message')->trim()->value(),
            phone: $this->string('phone')->trim()->value() ?: null,
            company: $this->string('company')->trim()->value() ?: null,
            serviceId: $this->integer('service_id') ?: null,
            packageId: $this->integer('package_id') ?: null,
            budgetMin: $this->has('budget_min') ? $this->float('budget_min') : null,
            budgetMax: $this->has('budget_max') ? $this->float('budget_max') : null,
            currency: strtoupper($this->string('currency')->value() ?: 'USD'),
            ipAddress: $this->ip(),
            userAgent: substr((string) $this->userAgent(), 0, 512),
        );
    }
}
