<?php

declare(strict_types=1);

namespace App\DTOs;

use App\DTOs\Concerns\TransfersData;
use App\Support\Pricing\Money;

final readonly class QuoteRequestData
{
    use TransfersData;

    public function __construct(
        public string $name,
        public string $email,
        public string $message,
        public ?string $phone = null,
        public ?string $company = null,
        public ?int $serviceId = null,
        public ?int $packageId = null,
        public ?float $budgetMin = null,
        public ?float $budgetMax = null,
        public string $currency = 'USD',
        public ?string $ipAddress = null,
        public ?string $userAgent = null,
    ) {}

    public function toAttributes(): array
    {
        return [
            'service_id' => $this->serviceId,
            'package_id' => $this->packageId,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'company' => $this->company,
            'budget_min_minor' => $this->budgetMin !== null
                ? Money::fromMajorUnits($this->budgetMin, $this->currency)->amountInMinorUnits
                : null,
            'budget_max_minor' => $this->budgetMax !== null
                ? Money::fromMajorUnits($this->budgetMax, $this->currency)->amountInMinorUnits
                : null,
            'currency' => $this->currency,
            'message' => $this->message,
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
        ];
    }
}
