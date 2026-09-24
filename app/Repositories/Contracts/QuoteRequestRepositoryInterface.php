<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Enums\QuoteStatus;
use App\Models\QuoteRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * @extends RepositoryInterface<QuoteRequest>
 */
interface QuoteRequestRepositoryInterface extends RepositoryInterface
{
    /** @return LengthAwarePaginator<QuoteRequest> */
    public function listing(?QuoteStatus $status = null, ?string $search = null, int $perPage = 20): LengthAwarePaginator;

    public function findByReference(string $reference): ?QuoteRequest;

    /** @return array<string, int> status => count */
    public function countsByStatus(): array;

    public function openCount(): int;

    /** @return array<string, int> Y-m => count, for the dashboard trend chart. */
    public function monthlyTotals(int $months = 6): array;
}
