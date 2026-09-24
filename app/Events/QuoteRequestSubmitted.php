<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\QuoteRequest;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class QuoteRequestSubmitted
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public readonly QuoteRequest $quote) {}
}
