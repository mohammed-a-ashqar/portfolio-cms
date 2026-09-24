<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\Contracts;
use App\Repositories\Eloquent;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

/**
 * Binds every repository contract to its Eloquent implementation.
 *
 * This single map is what lets the rest of the app type-hint interfaces. It is
 * deferred: nothing here is needed until something actually resolves a
 * repository, so it stays out of the boot path of console commands.
 */
final class RepositoryServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /** @var array<class-string, class-string> */
    private const BINDINGS = [
        Contracts\ProjectRepositoryInterface::class => Eloquent\ProjectRepository::class,
        Contracts\ServiceRepositoryInterface::class => Eloquent\ServiceRepository::class,
        Contracts\ReelRepositoryInterface::class => Eloquent\ReelRepository::class,
        Contracts\QuoteRequestRepositoryInterface::class => Eloquent\QuoteRequestRepository::class,
        Contracts\ContactMessageRepositoryInterface::class => Eloquent\ContactMessageRepository::class,
    ];

    public function register(): void
    {
        foreach (self::BINDINGS as $contract => $implementation) {
            $this->app->bind($contract, $implementation);
        }
    }

    /** @return array<int, class-string> */
    public function provides(): array
    {
        return array_keys(self::BINDINGS);
    }
}
