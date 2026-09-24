<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\DTOs\ProjectData;
use App\Enums\ProjectStatus;
use App\Http\Requests\Concerns\ValidatesTranslatableFields;
use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StoreProjectRequest extends FormRequest
{
    use ValidatesTranslatableFields;

    public function authorize(): bool
    {
        return $this->user()?->can('create', Project::class) ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $maxKb = (int) config('portfolio.uploads.max_size_kb');
        $mimes = implode(',', config('portfolio.uploads.image_mimes'));

        return array_merge(
            $this->translatableRules('title', ['string', 'max:190']),
            $this->translatableRules('summary', ['string', 'max:500'], required: false),
            $this->translatableRules('description', ['string', 'max:20000'], required: false),
            [
                'category_id' => ['nullable', Rule::exists('categories', 'id')],
                'client_name' => ['nullable', 'string', 'max:190'],
                'project_url' => ['nullable', 'url', 'max:512'],
                'repository_url' => ['nullable', 'url', 'max:512'],
                'started_at' => ['nullable', 'date'],
                'completed_at' => ['nullable', 'date', 'after_or_equal:started_at'],
                'status' => ['required', new Enum(ProjectStatus::class)],
                'is_featured' => ['nullable', 'boolean'],
                'technology_ids' => ['nullable', 'array'],
                'technology_ids.*' => [Rule::exists('technologies', 'id')],
                'cover_image' => ['nullable', 'image', "mimes:{$mimes}", "max:{$maxKb}"],
                'gallery_images' => ['nullable', 'array', 'max:20'],
                'gallery_images.*' => ['image', "mimes:{$mimes}", "max:{$maxKb}"],
                'seo' => ['nullable', 'array'],
            ],
        );
    }

    public function toData(): ProjectData
    {
        return new ProjectData(
            title: $this->translatableInput('title'),
            summary: $this->translatableInput('summary'),
            description: $this->translatableInput('description'),
            categoryId: $this->integer('category_id') ?: null,
            clientName: $this->string('client_name')->trim()->value() ?: null,
            projectUrl: $this->string('project_url')->trim()->value() ?: null,
            repositoryUrl: $this->string('repository_url')->trim()->value() ?: null,
            startedAt: $this->date('started_at')?->toDateString(),
            completedAt: $this->date('completed_at')?->toDateString(),
            status: ProjectStatus::from($this->string('status')->value()),
            isFeatured: $this->boolean('is_featured'),
            technologyIds: array_map('intval', (array) $this->input('technology_ids', [])),
            coverImage: $this->file('cover_image'),
            galleryImages: (array) $this->file('gallery_images', []),
            seo: $this->input('seo'),
        );
    }
}
