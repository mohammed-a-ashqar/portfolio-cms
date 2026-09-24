<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\PortfolioCache;
use App\Support\Filters\FilterPipeline;
use App\Support\Images\ImageProcessor;
use App\Support\Pricing\FixedPricing;
use App\Support\Pricing\HourlyPricing;
use App\Support\Pricing\PricingCalculator;
use App\Support\Pricing\RecurringPricing;
use App\Support\Reels\InstagramProvider;
use App\Support\Reels\ManualProvider;
use App\Support\Reels\ReelProviderManager;
use App\Support\Reels\TikTokProvider;
use App\Support\Reels\YouTubeProvider;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Pagination\Paginator;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

/**
 * Wires the domain services: which strategies exist, and in what order.
 *
 * The ordering in ReelProviderManager matters — ManualProvider accepts any
 * valid URL, so it must stay last or it would swallow every Instagram link.
 */
final class PortfolioServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ImageProcessor::class, fn (): ImageProcessor => new ImageProcessor);

        $this->app->singleton(
            FilterPipeline::class,
            fn ($app): FilterPipeline => new FilterPipeline(new Pipeline($app)),
        );

        $this->app->singleton(
            PortfolioCache::class,
            fn ($app): PortfolioCache => new PortfolioCache($app->make(CacheRepository::class)),
        );

        $this->app->singleton(ReelProviderManager::class, function ($app): ReelProviderManager {
            $http = $app->make(HttpFactory::class);

            return new ReelProviderManager(
                new InstagramProvider($http, config('portfolio.reels.instagram_token')),
                new YouTubeProvider,
                new TikTokProvider($http),
                new ManualProvider, // catch-all: must be last
            );
        });

        $this->app->singleton(PricingCalculator::class, fn (): PricingCalculator => new PricingCalculator(
            new FixedPricing,
            new HourlyPricing,
            new RecurringPricing,
        ));
    }

    public function boot(): void
    {
        // Fail loudly in development when a relation was not eager loaded,
        // instead of shipping an N+1 to production.
        Model::preventLazyLoading(! $this->app->isProduction());
        Model::preventSilentlyDiscardingAttributes(! $this->app->isProduction());

        Paginator::useTailwind();

        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }
    }
}
