<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Skill;
use App\Models\Testimonial;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Repositories\Contracts\ReelRepositoryInterface;
use App\Repositories\Contracts\ServiceRepositoryInterface;

/**
 * Assembles the public pages.
 *
 * Controllers call one method and get a ready view model; the caching policy
 * lives here rather than being repeated (and forgotten) in each controller.
 */
final readonly class PortfolioService
{
    public function __construct(
        private ProjectRepositoryInterface $projects,
        private ServiceRepositoryInterface $services,
        private ReelRepositoryInterface $reels,
        private PortfolioCache $cache,
    ) {}

    /** @return array<string, mixed> */
    public function homePage(): array
    {
        return $this->cache->remember(
            'home:'.app()->getLocale(),
            (int) config('portfolio.cache_ttl'),
            fn (): array => [
                'featuredProjects' => $this->projects->featured(6),
                'services' => $this->services->featured(3),
                'reels' => $this->reels->showcase(6),
                'skills' => Skill::query()->active()->ordered()->get()->groupBy('group'),
                'testimonials' => Testimonial::query()
                    ->active()
                    ->featured()
                    ->ordered()
                    ->limit(6)
                    ->get(),
            ],
        );
    }

    /** Cheap counters shown in the hero, refreshed on the same schedule. */
    public function headlineStats(): array
    {
        return $this->cache->remember(
            'stats',
            (int) config('portfolio.cache_ttl'),
            fn (): array => [
                'projects' => $this->projects->countsByStatus()['published'] ?? 0,
                'reels' => $this->reels->count(),
                'services' => $this->services->activeWithPackages()->count(),
            ],
        );
    }
}
