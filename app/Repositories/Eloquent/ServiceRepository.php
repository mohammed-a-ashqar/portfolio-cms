<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Service;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/** @extends BaseRepository<Service> */
final class ServiceRepository extends BaseRepository implements ServiceRepositoryInterface
{
    public function __construct(Service $model)
    {
        parent::__construct($model);
    }

    public function activeWithPackages(): Collection
    {
        return $this->query()
            ->active()
            ->with(['packages' => fn ($q) => $q->where('is_active', true)->orderBy('order_column')])
            ->ordered()
            ->get();
    }

    public function featured(int $limit = 6): Collection
    {
        return $this->query()->active()->featured()->ordered()->limit($limit)->get();
    }

    public function findActiveBySlug(string $slug): ?Service
    {
        return $this->query()
            ->active()
            ->with(['packages' => fn ($q) => $q->where('is_active', true)->orderBy('order_column')])
            ->where('slug', $slug)
            ->first();
    }
}
