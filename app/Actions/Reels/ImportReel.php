<?php

declare(strict_types=1);

namespace App\Actions\Reels;

use App\DTOs\ReelData;
use App\Models\Reel;
use App\Repositories\Contracts\ReelRepositoryInterface;
use App\Services\MediaService;
use App\Support\Reels\ReelProviderManager;
use Illuminate\Support\Carbon;

/**
 * Turns a pasted URL into a stored reel.
 *
 * The whole platform-specific part is one line — `$this->providers->fetch()` —
 * because the Strategy pattern already picked the right implementation. This
 * Action stays identical whether the link is Instagram, YouTube or TikTok.
 */
final readonly class ImportReel
{
    public function __construct(
        private ReelRepositoryInterface $reels,
        private ReelProviderManager $providers,
        private MediaService $media,
    ) {}

    public function handle(ReelData $data): Reel
    {
        $metadata = $this->providers->fetch($data->url);

        $attributes = array_merge(
            $metadata->toArray(),
            $data->toAttributes(),
            ['synced_at' => Carbon::now()],
        );

        // An uploaded poster always beats the platform's thumbnail.
        if ($data->hasPoster()) {
            $attributes['thumbnail_path'] = $this->media->storePath($data->poster, 'reels/posters');
        }

        // Re-importing the same link updates it instead of erroring.
        return $this->reels->upsertFromMetadata($attributes);
    }
}
