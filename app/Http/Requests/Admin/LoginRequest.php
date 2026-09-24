<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

final class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:190'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    /** @return array{email: string, password: string} */
    public function credentials(): array
    {
        return [
            'email' => $this->string('email')->lower()->trim()->value(),
            'password' => $this->string('password')->value(),
            // Deactivated accounts fail at the guard, not after login.
            'is_active' => true,
        ];
    }

    public function remember(): bool
    {
        return $this->boolean('remember');
    }
}
