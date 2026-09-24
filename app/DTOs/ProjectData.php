<?php

declare(strict_types=1);

namespace App\DTOs;

use App\DTOs\Concerns\TransfersData;
use App\Enums\ProjectStatus;
use Illuminate\Http\UploadedFile;

/**
 * Everything needed to create or update a project.
 *
 * Translatable fields arrive as ['en' => '...', 'ar' => '...'] and are handed
 * straight to HasTranslations, so the Action never touches locale logic.
 */
final readonly class ProjectData
{
    use TransfersData;

    public function __construct(
        /** @var array<string, string|null> */
        public array $title,
        /** @var array<string, string|null> */
        public array $summary = [],
        /** @var array<string, string|null> */
        public array $description = [],
        public ?int $categoryId = null,
        public ?string $clientName = null,
        public ?string $projectUrl = null,
        public ?string $repositoryUrl = null,
        public ?string $startedAt = null,
        public ?string $completedAt = null,
        public ProjectStatus $status = ProjectStatus::Draft,
        public bool $isFeatured = false,
        /** @var array<int, int> */
        public array $technologyIds = [],
        public ?UploadedFile $coverImage = null,
        /** @var array<int, UploadedFile> */
        public array $galleryImages = [],
        /** @var array<string, mixed>|null */
        public ?array $seo = null,
    ) {}

    /** Column-name map for the repository; files are handled separately. */
    public function toAttributes(): array
    {
        return array_filter([
            'category_id' => $this->categoryId,
            'title' => $this->title,
            'summary' => $this->summary ?: null,
            'description' => $this->description ?: null,
            'client_name' => $this->clientName,
            'project_url' => $this->projectUrl,
            'repository_url' => $this->repositoryUrl,
            'started_at' => $this->startedAt,
            'completed_at' => $this->completedAt,
            'status' => $this->status,
            'is_featured' => $this->isFeatured,
            'seo' => $this->seo,
        ], static fn (mixed $value): bool => $value !== null);
    }

    public function hasCoverImage(): bool
    {
        return $this->coverImage instanceof UploadedFile;
    }
}
