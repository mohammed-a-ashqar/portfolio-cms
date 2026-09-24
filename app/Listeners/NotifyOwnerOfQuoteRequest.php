<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\QuoteRequestSubmitted;
use App\Mail\QuoteRequestNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Queued so a slow SMTP server never makes a visitor wait on the form.
 */
final class NotifyOwnerOfQuoteRequest implements ShouldQueue
{
    public int $tries = 3;

    public array $backoff = [10, 60, 300];

    public function handle(QuoteRequestSubmitted $event): void
    {
        $recipient = config('portfolio.notifications.quotes_to') ?? config('mail.from.address');

        if (blank($recipient)) {
            return;
        }

        Mail::to($recipient)->send(new QuoteRequestNotification($event->quote));
    }

    /** Runs after the final retry: the quote is safe in the DB, so only log. */
    public function failed(QuoteRequestSubmitted $event, Throwable $exception): void
    {
        Log::error('Failed to notify owner about a quote request.', [
            'reference' => $event->quote->reference,
            'message' => $exception->getMessage(),
        ]);
    }
}
