<?php

declare(strict_types=1);

namespace App\Support\Reels;

use App\Enums\ReelProvider;
use App\Support\Reels\Contracts\ReelProviderInterface;
use Illuminate\Support\Str;

/**
 * Fallback strategy: a self-hosted MP4 or any URL the other providers reject.
 *
 * Having a catch-all means the manager's `for()` never returns null, so callers
 * have no null branch to forget.
 */
final readonly class ManualProvider implements ReelProviderInterface
{
    public function provider(): ReelProvider
    {
        return ReelProvider::Manual;
    }

    public function supports(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    public function fetch(string $url): ReelMetadata
    {
        return new ReelMetadata(
            provider: ReelProvider::Manual,
            externalId: Str::of($url)->afterLast('/')->before('?')->value() ?: Str::random(12),
            url: $url,
            embedUrl: $url,
        );
    }
}
