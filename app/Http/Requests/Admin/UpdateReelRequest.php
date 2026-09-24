<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

final class UpdateReelRequest extends ImportReelRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('reel')) ?? false;
    }
}
