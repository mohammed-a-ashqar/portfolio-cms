<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Enums\ReelProvider;
use App\Models\Reel;
use App\Repositories\Contracts\ReelRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * @extends BaseRepository<Reel>
 */
final class ReelRepository extends BaseRepository implements ReelRepositoryInterface
{
    public function __construct(Reel $model)
    {
        parent::__construct($model);
    }

    public function showcase(int $limit = 12, ?ReelProvider $provider = null): Collection
    {
        return $this->query()
            ->active()
            ->fromProvider($provider)
            ->ordered()
            ->limit($limit)
            ->get();
    }

    public function paginatedShowcase(int $perPage = 12, ?ReelProvider $provider = null): LengthAwarePaginator
    {
        return $this->query()
            ->active()
            ->fromProvider($provider)
            ->ordered()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findByExternalId(ReelProvider $provider, string $externalId): ?Reel
    {
        return $this->query()
            ->where('provider', $provider->value)
            ->where('external_id', $externalId)
            ->first();
    }

    public function upsertFromMetadata(array $attributes): Reel
    {
        // The (provider, external_id) unique index makes this safe to repeat.
        return $this->query()->updateOrCreate(
            [
                'provider' => $attributes['provider'],
                'external_id' => $attributes['external_id'],
            ],
            $attributes,
        );
    }

    public function countsByProvider(): array
    {
        $counts = $this->query()
            ->selectRaw('provider, COUNT(*) as aggregate')
            ->groupBy('provider')
            ->pluck('aggregate', 'provider')
            ->all();

        return collect(ReelProvider::cases())
            ->mapWithKeys(fn (ReelProvider $provider): array => [
                $provider->value => (int) ($counts[$provider->value] ?? 0),
            ])
            ->all();
    }
}
