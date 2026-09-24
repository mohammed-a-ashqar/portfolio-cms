<?php

declare(strict_types=1);

namespace App\Actions\Quotes;

use App\DTOs\QuoteRequestData;
use App\Events\QuoteRequestSubmitted;
use App\Models\QuoteRequest;
use App\Repositories\Contracts\QuoteRequestRepositoryInterface;
use Illuminate\Support\Facades\DB;

final readonly class SubmitQuoteRequest
{
    public function __construct(private QuoteRequestRepositoryInterface $quotes) {}

    public function handle(QuoteRequestData $data): QuoteRequest
    {
        $quote = DB::transaction(function () use ($data): QuoteRequest {
            return $this->quotes->create(array_merge(
                $data->toAttributes(),
                // Generated inside the transaction so two concurrent submits
                // cannot end up with the same reference.
                ['reference' => QuoteRequest::nextReference()],
            ));
        });

        QuoteRequestSubmitted::dispatch($quote);

        return $quote;
    }
}
