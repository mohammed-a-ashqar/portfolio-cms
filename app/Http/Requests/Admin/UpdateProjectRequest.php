<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

/**
 * Same shape as creation — the difference is the authorisation check, which
 * needs the specific model instance.
 */
final class UpdateProjectRequest extends StoreProjectRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('project')) ?? false;
    }
}
