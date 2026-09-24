<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\ContactMessageReceived;
use App\Mail\ContactMessageNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

final class NotifyOwnerOfContactMessage implements ShouldQueue
{
    public int $tries = 3;

    public array $backoff = [10, 60, 300];

    public function handle(ContactMessageReceived $event): void
    {
        $recipient = config('portfolio.notifications.contact_to') ?? config('mail.from.address');

        if (blank($recipient)) {
            return;
        }

        Mail::to($recipient)->send(new ContactMessageNotification($event->message));
    }

    public function failed(ContactMessageReceived $event, Throwable $exception): void
    {
        Log::error('Failed to notify owner about a contact message.', [
            'message_id' => $event->message->getKey(),
            'error' => $exception->getMessage(),
        ]);
    }
}
