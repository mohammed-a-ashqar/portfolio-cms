<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Enums\ReelProvider;
use App\Models\Reel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * @extends RepositoryInterface<Reel>
 */
interface ReelRepositoryInterface extends RepositoryInterface
{
    /** @return Collection<int, Reel> */
    public function showcase(int $limit = 12, ?ReelProvider $provider = null): Collection;

    /** @return LengthAwarePaginator<Reel> */
    public function paginatedShowcase(int $perPage = 12, ?ReelProvider $provider = null): LengthAwarePaginator;

    public function findByExternalId(ReelProvider $provider, string $externalId): ?Reel;

    /** Insert or refresh in one call, so re-importing a URL is idempotent. */
    public function upsertFromMetadata(array $attributes): Reel;

    /** @return array<string, int> provider => count */
    public function countsByProvider(): array;
}
