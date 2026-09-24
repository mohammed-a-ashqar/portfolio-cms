<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\DTOs\ReelData;
use App\Http\Requests\Concerns\ValidatesTranslatableFields;
use App\Models\Reel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ImportReelRequest extends FormRequest
{
    use ValidatesTranslatableFields;

    public function authorize(): bool
    {
        return $this->user()?->can('import', Reel::class) ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $maxKb = (int) config('portfolio.uploads.max_size_kb');

        return array_merge(
            $this->translatableRules('caption', ['string', 'max:1000'], required: false),
            [
                'url' => ['required', 'url', 'max:512'],
                'project_id' => ['nullable', Rule::exists('projects', 'id')],
                'poster' => ['nullable', 'image', "max:{$maxKb}"],
                'duration_seconds' => ['nullable', 'integer', 'min:1', 'max:3600'],
                'is_featured' => ['nullable', 'boolean'],
                'is_active' => ['nullable', 'boolean'],
            ],
        );
    }

    public function toData(): ReelData
    {
        return new ReelData(
            url: $this->string('url')->trim()->value(),
            caption: $this->translatableInput('caption'),
            projectId: $this->integer('project_id') ?: null,
            isFeatured: $this->boolean('is_featured'),
            isActive: $this->boolean('is_active', true),
            poster: $this->file('poster'),
            durationSeconds: $this->integer('duration_seconds') ?: null,
        );
    }
}
