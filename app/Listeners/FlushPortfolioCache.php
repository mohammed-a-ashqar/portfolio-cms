<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\ProjectPublished;
use App\Services\PortfolioCache;

/**
 * Publishing a project invalidates the cached public listings.
 *
 * Runs synchronously on purpose: stale content on the site the moment after
 * publishing is exactly the bug a queued flush would introduce.
 */
final readonly class FlushPortfolioCache
{
    public function __construct(private PortfolioCache $cache) {}

    public function handle(ProjectPublished $event): void
    {
        $this->cache->flush();
    }
}
