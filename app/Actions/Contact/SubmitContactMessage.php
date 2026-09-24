<?php

declare(strict_types=1);

namespace App\Actions\Contact;

use App\DTOs\ContactMessageData;
use App\Events\ContactMessageReceived;
use App\Models\ContactMessage;
use App\Repositories\Contracts\ContactMessageRepositoryInterface;
use DomainException;

final readonly class SubmitContactMessage
{
    private const MAX_PER_HOUR = 3;

    public function __construct(private ContactMessageRepositoryInterface $messages) {}

    public function handle(ContactMessageData $data): ContactMessage
    {
        // Application-level throttle on top of the route middleware: the route
        // limiter keys on IP, this one on the address, which is what actually
        // stops a form-spam script rotating through proxies.
        if ($this->messages->recentCountForEmail($data->email) >= self::MAX_PER_HOUR) {
            throw new DomainException(__('contact.errors.too_many_messages'));
        }

        $message = $this->messages->create($data->toAttributes());

        ContactMessageReceived::dispatch($message);

        return $message;
    }
}
