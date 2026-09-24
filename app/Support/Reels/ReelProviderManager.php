<?php

declare(strict_types=1);

namespace App\Support\Reels;

use App\Enums\ReelProvider;
use App\Exceptions\ReelImportException;
use App\Support\Reels\Contracts\ReelProviderInterface;

/**
 * Resolves the right strategy for a URL.
 *
 * Strategies are ordered: the specific platforms get first refusal and the
 * manual provider is the guaranteed tail, so `for()` always returns something.
 */
final class ReelProviderManager
{
    /** @var array<int, ReelProviderInterface> */
    private array $providers;

    public function __construct(ReelProviderInterface ...$providers)
    {
        $this->providers = $providers;
    }

    public function for(string $url): ReelProviderInterface
    {
        foreach ($this->providers as $provider) {
            if ($provider->supports($url)) {
                return $provider;
            }
        }

        throw ReelImportException::unsupportedUrl($url);
    }

    public function byEnum(ReelProvider $provider): ReelProviderInterface
    {
        foreach ($this->providers as $candidate) {
            if ($candidate->provider() === $provider) {
                return $candidate;
            }
        }

        throw ReelImportException::unsupportedUrl($provider->value);
    }

    public function fetch(string $url): ReelMetadata
    {
        return $this->for($url)->fetch($url);
    }

    /** @return array<int, ReelProvider> */
    public function supportedProviders(): array
    {
        return array_map(
            static fn (ReelProviderInterface $provider): ReelProvider => $provider->provider(),
            $this->providers,
        );
    }
}
