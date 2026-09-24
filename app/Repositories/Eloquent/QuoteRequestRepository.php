<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Enums\QuoteStatus;
use App\Models\QuoteRequest;
use App\Repositories\Contracts\QuoteRequestRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * @extends BaseRepository<QuoteRequest>
 */
final class QuoteRequestRepository extends BaseRepository implements QuoteRequestRepositoryInterface
{
    public function __construct(QuoteRequest $model)
    {
        parent::__construct($model);
    }

    public function listing(?QuoteStatus $status = null, ?string $search = null, int $perPage = 20): LengthAwarePaginator
    {
        return $this->query()
            ->with(['service', 'package'])
            ->withStatus($status)
            ->when($search, function (Builder $query, string $term): void {
                $query->where(function (Builder $q) use ($term): void {
                    $q->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%")
                        ->orWhere('company', 'like', "%{$term}%")
                        ->orWhere('reference', 'like', "%{$term}%");
                });
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findByReference(string $reference): ?QuoteRequest
    {
        return $this->query()->where('reference', $reference)->first();
    }

    public function countsByStatus(): array
    {
        $counts = $this->query()
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status')
            ->all();

        return collect(QuoteStatus::cases())
            ->mapWithKeys(fn (QuoteStatus $status): array => [
                $status->value => (int) ($counts[$status->value] ?? 0),
            ])
            ->all();
    }

    public function openCount(): int
    {
        return $this->query()->open()->count();
    }

    public function monthlyTotals(int $months = 6): array
    {
        $since = Carbon::now()->startOfMonth()->subMonths($months - 1);

        $rows = $this->query()
            ->where('created_at', '>=', $since)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as bucket, COUNT(*) as aggregate")
            ->groupBy('bucket')
            ->pluck('aggregate', 'bucket')
            ->all();

        // Fill the gaps so the chart has a point for every month.
        $totals = [];

        for ($i = 0; $i < $months; $i++) {
            $key = $since->copy()->addMonths($i)->format('Y-m');
            $totals[$key] = (int) ($rows[$key] ?? 0);
        }

        return $totals;
    }
}
