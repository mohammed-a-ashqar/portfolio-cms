<?php

declare(strict_types=1);

namespace App\Support\Pricing;

use App\Enums\BillingPeriod;

/** Everything a pricing strategy is allowed to look at. */
final readonly class PricingContext
{
    public function __construct(
        public Money $basePrice,
        public BillingPeriod $period,
        public int $quantity = 1,
        public float $discountPercent = 0.0,
        /** @var array<string, mixed> */
        public array $extras = [],
    ) {}

    public function withDiscount(float $percent): self
    {
        return new self($this->basePrice, $this->period, $this->quantity, $percent, $this->extras);
    }
}
