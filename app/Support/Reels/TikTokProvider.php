<?php

declare(strict_types=1);

namespace App\Support\Reels;

use App\Enums\ReelProvider;
use App\Exceptions\ReelImportException;
use App\Support\Reels\Contracts\ReelProviderInterface;
use Illuminate\Http\Client\Factory as HttpFactory;
use Throwable;

/**
 * TikTok, via the public oEmbed endpoint (no credentials required).
 */
final readonly class TikTokProvider implements ReelProviderInterface
{
    private const ID_PATTERN = '#tiktok\.com/@[\w.-]+/video/(?<id>\d+)#i';

    public function __construct(private HttpFactory $http) {}

    public function provider(): ReelProvider
    {
        return ReelProvider::TikTok;
    }

    public function supports(string $url): bool
    {
        return ReelProvider::detectFromUrl($url) === ReelProvider::TikTok
            && preg_match(self::ID_PATTERN, $url) === 1;
    }

    public function fetch(string $url): ReelMetadata
    {
        if (preg_match(self::ID_PATTERN, $url, $matches) !== 1) {
            throw ReelImportException::unparsableUrl($url);
        }

        $id = $matches['id'];

        $metadata = new ReelMetadata(
            provider: ReelProvider::TikTok,
            externalId: $id,
            url: $url,
            embedUrl: "https://www.tiktok.com/embed/v2/{$id}",
        );

        try {
            $response = $this->http->timeout(8)->get('https://www.tiktok.com/oembed', ['url' => $url]);

            if ($response->successful()) {
                $payload = $response->json();

                return $metadata->with(
                    caption: $payload['title'] ?? null,
                    thumbnailUrl: $payload['thumbnail_url'] ?? null,
                    authorName: $payload['author_name'] ?? null,
                    raw: $payload,
                );
            }
        } catch (Throwable) {
            // Enrichment is optional; the embed works without it.
        }

        return $metadata;
    }
}
