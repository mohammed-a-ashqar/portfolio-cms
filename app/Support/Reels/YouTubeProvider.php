<?php

declare(strict_types=1);

namespace App\Support\Reels;

use App\Enums\ReelProvider;
use App\Exceptions\ReelImportException;
use App\Support\Reels\Contracts\ReelProviderInterface;

/**
 * YouTube Shorts and regular videos.
 *
 * YouTube exposes deterministic thumbnail URLs, so this strategy needs no
 * network call at all — import stays instant and works offline in tests.
 */
final readonly class YouTubeProvider implements ReelProviderInterface
{
    private const ID_PATTERN = '#(?:youtube\.com/(?:shorts/|watch\?v=|embed/)|youtu\.be/)(?<id>[A-Za-z0-9_-]{11})#i';

    public function provider(): ReelProvider
    {
        return ReelProvider::YouTube;
    }

    public function supports(string $url): bool
    {
        return ReelProvider::detectFromUrl($url) === ReelProvider::YouTube
            && preg_match(self::ID_PATTERN, $url) === 1;
    }

    public function fetch(string $url): ReelMetadata
    {
        if (preg_match(self::ID_PATTERN, $url, $matches) !== 1) {
            throw ReelImportException::unparsableUrl($url);
        }

        $id = $matches['id'];

        return new ReelMetadata(
            provider: ReelProvider::YouTube,
            externalId: $id,
            url: "https://www.youtube.com/shorts/{$id}",
            // `youtube-nocookie` keeps the portfolio clear of tracking cookies
            // until the visitor actually presses play.
            embedUrl: "https://www.youtube-nocookie.com/embed/{$id}?rel=0&modestbranding=1",
            thumbnailUrl: "https://i.ytimg.com/vi/{$id}/maxresdefault.jpg",
        );
    }
}
