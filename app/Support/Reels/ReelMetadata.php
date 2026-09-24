<?php

declare(strict_types=1);

namespace App\Support\Reels;

use App\Enums\ReelProvider;
use Carbon\CarbonImmutable;

/**
 * Immutable description of a short video, whatever platform it came from.
 *
 * Providers return this shape and nothing else, so the Action that persists a
 * reel never learns which network it is talking to.
 */
final readonly class ReelMetadata
{
    public function __construct(
        public ReelProvider $provider,
        public string $externalId,
        public string $url,
        public string $embedUrl,
        public ?string $caption = null,
        public ?string $thumbnailUrl = null,
        public ?string $authorName = null,
        public ?CarbonImmutable $postedAt = null,
        public ?int $durationSeconds = null,
        /** @var array<string, mixed> Raw payload, kept for debugging and future fields. */
        public array $raw = [],
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'provider' => $this->provider->value,
            'external_id' => $this->externalId,
            'url' => $this->url,
            'embed_url' => $this->embedUrl,
            'caption' => $this->caption,
            'thumbnail_url' => $this->thumbnailUrl,
            'author_name' => $this->authorName,
            'posted_at' => $this->postedAt?->toDateTimeString(),
            'duration_seconds' => $this->durationSeconds,
            'metadata' => $this->raw,
        ];
    }

    public function with(mixed ...$overrides): self
    {
        return new self(
            provider: $overrides['provider'] ?? $this->provider,
            externalId: $overrides['externalId'] ?? $this->externalId,
            url: $overrides['url'] ?? $this->url,
            embedUrl: $overrides['embedUrl'] ?? $this->embedUrl,
            caption: $overrides['caption'] ?? $this->caption,
            thumbnailUrl: $overrides['thumbnailUrl'] ?? $this->thumbnailUrl,
            authorName: $overrides['authorName'] ?? $this->authorName,
            postedAt: $overrides['postedAt'] ?? $this->postedAt,
            durationSeconds: $overrides['durationSeconds'] ?? $this->durationSeconds,
            raw: $overrides['raw'] ?? $this->raw,
        );
    }
}
