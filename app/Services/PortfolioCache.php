<?php

declare(strict_types=1);

namespace App\Services;

use Closure;
use Illuminate\Cache\TaggableStore;
use Illuminate\Contracts\Cache\Repository as CacheRepository;

/**
 * Cache facade for the public site that works on every driver.
 *
 * Laravel's `database`, `file` and `dynamodb` stores do not support tags, so a
 * plain `Cache::tags(...)->flush()` throws on the default configuration. This
 * uses tags when the driver has them and falls back to a tracked key index
 * otherwise — so switching CACHE_STORE never breaks invalidation.
 */
final class PortfolioCache
{
    private const TAG = 'portfolio';

    private const INDEX_KEY = 'portfolio:cache-keys';

    public function __construct(private readonly CacheRepository $cache) {}

    public function remember(string $key, int $ttlSeconds, Closure $callback): mixed
    {
        $key = $this->prefix($key);

        if ($this->supportsTags()) {
            return $this->cache->tags([self::TAG])->remember($key, $ttlSeconds, $callback);
        }

        $this->trackKey($key);

        return $this->cache->remember($key, $ttlSeconds, $callback);
    }

    public function flush(): void
    {
        if ($this->supportsTags()) {
            $this->cache->tags([self::TAG])->flush();

            return;
        }

        foreach ($this->trackedKeys() as $key) {
            $this->cache->forget($key);
        }

        $this->cache->forget(self::INDEX_KEY);
    }

    public function forget(string $key): void
    {
        $this->cache->forget($this->prefix($key));
    }

    private function supportsTags(): bool
    {
        return $this->cache->getStore() instanceof TaggableStore;
    }

    private function prefix(string $key): string
    {
        return self::TAG.':'.$key;
    }

    /** @return array<int, string> */
    private function trackedKeys(): array
    {
        return (array) $this->cache->get(self::INDEX_KEY, []);
    }

    private function trackKey(string $key): void
    {
        $keys = $this->trackedKeys();

        if (! in_array($key, $keys, true)) {
            $keys[] = $key;
            // No TTL: the index must outlive the entries it points at.
            $this->cache->forever(self::INDEX_KEY, $keys);
        }
    }
}
