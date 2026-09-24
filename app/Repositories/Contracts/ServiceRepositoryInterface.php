<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Service;
use Illuminate\Database\Eloquent\Collection;

/** @extends RepositoryInterface<Service> */
interface ServiceRepositoryInterface extends RepositoryInterface
{
    /** @return Collection<int, Service> */
    public function activeWithPackages(): Collection;

    /** @return Collection<int, Service> */
    public function featured(int $limit = 6): Collection;

    public function findActiveBySlug(string $slug): ?Service;
}
