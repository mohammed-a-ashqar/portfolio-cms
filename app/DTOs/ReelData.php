<?php

declare(strict_types=1);

namespace App\DTOs;

use App\DTOs\Concerns\TransfersData;
use Illuminate\Http\UploadedFile;

/**
 * Admin-supplied part of a reel.
 *
 * The provider-supplied part arrives separately as a ReelMetadata, and the
 * import Action merges the two — which is what keeps platform detail out of
 * the form layer.
 */
final readonly class ReelData
{
    use TransfersData;

    public function __construct(
        public string $url,
        /** @var array<string, string|null> */
        public array $caption = [],
        public ?int $projectId = null,
        public bool $isFeatured = false,
        public bool $isActive = true,
        public ?UploadedFile $poster = null,
        public ?int $durationSeconds = null,
    ) {}

    public function hasPoster(): bool
    {
        return $this->poster instanceof UploadedFile;
    }

    public function toAttributes(): array
    {
        return array_filter([
            'caption' => $this->caption ?: null,
            'project_id' => $this->projectId,
            'is_featured' => $this->isFeatured,
            'is_active' => $this->isActive,
            'duration_seconds' => $this->durationSeconds,
        ], static fn (mixed $value): bool => $value !== null);
    }
}
