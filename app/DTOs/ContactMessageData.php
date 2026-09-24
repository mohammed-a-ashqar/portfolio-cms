<?php

declare(strict_types=1);

namespace App\DTOs;

use App\DTOs\Concerns\TransfersData;

final readonly class ContactMessageData
{
    use TransfersData;

    public function __construct(
        public string $name,
        public string $email,
        public string $message,
        public ?string $phone = null,
        public ?string $subject = null,
        public ?string $ipAddress = null,
        public ?string $userAgent = null,
    ) {}

    public function toAttributes(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'subject' => $this->subject,
            'message' => $this->message,
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
        ];
    }
}
