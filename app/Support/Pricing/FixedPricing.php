<?php

declare(strict_types=1);

namespace App\Support\Pricing;

use App\Enums\BillingPeriod;
use App\Support\Pricing\Contracts\PricingStrategyInterface;

/** One-off project fee. */
final readonly class FixedPricing implements PricingStrategyInterface
{
    public function handles(BillingPeriod $period): bool
    {
        return $period === BillingPeriod::OneTime;
    }

    public function calculate(PricingContext $context): Money
    {
        return $context->basePrice
            ->multiply($context->quantity)
            ->multiply(1 - ($context->discountPercent / 100));
    }

    public function explain(PricingContext $context): string
    {
        return __('pricing.explain.fixed', [
            'base' => $context->basePrice->format(),
            'quantity' => $context->quantity,
            'discount' => $context->discountPercent,
        ]);
    }
}
