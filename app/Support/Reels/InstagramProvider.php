<?php

declare(strict_types=1);

namespace App\Support\Reels;

use App\Enums\ReelProvider;
use App\Exceptions\ReelImportException;
use App\Support\Reels\Contracts\ReelProviderInterface;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Instagram reels.
 *
 * Deliberately does NOT depend on the Graph API. Meta's public oEmbed endpoint
 * now needs an app token and review, and a portfolio site that goes blank the
 * day a token expires is worse than one that never had it. Instead we embed
 * through the public `/embed/` route, which needs no credentials, and let the
 * owner attach their own poster image — which loads faster and looks better
 * than Instagram's own chrome anyway.
 *
 * If an oEmbed token IS configured, the provider enriches the record with the
 * real caption and thumbnail. Enrichment failing is logged, never fatal.
 */
final readonly class InstagramProvider implements ReelProviderInterface
{
    private const SHORTCODE_PATTERN = '#instagram\.com/(?:reels?|p|tv)/(?<code>[A-Za-z0-9_-]+)#i';

    public function __construct(
        private HttpFactory $http,
        private ?string $accessToken = null,
    ) {}

    public function provider(): ReelProvider
    {
        return ReelProvider::Instagram;
    }

    public function supports(string $url): bool
    {
        return ReelProvider::detectFromUrl($url) === ReelProvider::Instagram
            && preg_match(self::SHORTCODE_PATTERN, $url) === 1;
    }

    public function fetch(string $url): ReelMetadata
    {
        if (preg_match(self::SHORTCODE_PATTERN, $url, $matches) !== 1) {
            throw ReelImportException::unparsableUrl($url);
        }

        $shortcode = $matches['code'];

        $metadata = new ReelMetadata(
            provider: ReelProvider::Instagram,
            externalId: $shortcode,
            url: "https://www.instagram.com/reel/{$shortcode}/",
            embedUrl: "https://www.instagram.com/reel/{$shortcode}/embed/",
        );

        return $this->enrich($metadata);
    }

    /** Best-effort caption/thumbnail lookup; never throws. */
    private function enrich(ReelMetadata $metadata): ReelMetadata
    {
        if (blank($this->accessToken)) {
            return $metadata;
        }

        try {
            $response = $this->http
                ->timeout(8)
                ->retry(2, 250, throw: false)
                ->get('https://graph.facebook.com/v21.0/instagram_oembed', [
                    'url' => $metadata->url,
                    'access_token' => $this->accessToken,
                    'omitscript' => true,
                ]);

            if ($response->failed()) {
                Log::warning('Instagram oEmbed enrichment failed.', [
                    'shortcode' => $metadata->externalId,
                    'status' => $response->status(),
                ]);

                return $metadata;
            }

            $payload = $response->json();

            return $metadata->with(
                caption: $payload['title'] ?? $metadata->caption,
                thumbnailUrl: $payload['thumbnail_url'] ?? $metadata->thumbnailUrl,
                authorName: $payload['author_name'] ?? $metadata->authorName,
                raw: $payload,
            );
        } catch (Throwable $exception) {
            Log::warning('Instagram oEmbed enrichment threw.', [
                'shortcode' => $metadata->externalId,
                'message' => $exception->getMessage(),
            ]);

            return $metadata;
        }
    }
}
