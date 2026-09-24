<?php

declare(strict_types=1);

namespace App\Actions\Quotes;

use App\Enums\QuoteStatus;
use App\Models\QuoteRequest;
use App\Repositories\Contracts\QuoteRequestRepositoryInterface;
use App\Support\Pricing\Money;
use DomainException;
use Illuminate\Support\Carbon;

/**
 * Moves a quote through its workflow, refusing illegal jumps.
 *
 * The rules live on the enum, so this Action only enforces them — and a new
 * status never means hunting for scattered `if ($status === ...)` checks.
 */
final readonly class TransitionQuoteStatus
{
    public function __construct(private QuoteRequestRepositoryInterface $quotes) {}

    public function handle(
        QuoteRequest $quote,
        QuoteStatus $target,
        ?Money $quotedAmount = null,
        ?string $notes = null,
    ): QuoteRequest {
        if (! $quote->status->canTransitionTo($target)) {
            throw new DomainException(__('quotes.errors.illegal_transition', [
                'from' => $quote->status->label(),
                'to' => $target->label(),
            ]));
        }

        return $this->quotes->update($quote, array_filter([
            'status' => $target,
            'quoted_amount_minor' => $quotedAmount?->amountInMinorUnits,
            'admin_notes' => $notes,
            'responded_at' => $target === QuoteStatus::Quoted ? Carbon::now() : $quote->responded_at,
        ], static fn (mixed $value): bool => $value !== null));
    }
}
