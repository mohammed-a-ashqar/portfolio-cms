<?php

declare(strict_types=1);

namespace App\Support\Pricing;

use App\Enums\BillingPeriod;
use App\Support\Pricing\Contracts\PricingStrategyInterface;

/**
 * Monthly or yearly retainers.
 *
 * Quotes the annual commitment so clients compare like with like, and applies
 * the customary yearly prepayment discount.
 */
final readonly class RecurringPricing implements PricingStrategyInterface
{
    public function __construct(private float $yearlyPrepaymentDiscount = 10.0) {}

    public function handles(BillingPeriod $period): bool
    {
        return $period->is(BillingPeriod::Monthly, BillingPeriod::Yearly);
    }

    public function calculate(PricingContext $context): Money
    {
        $perCycle = $context->basePrice->multiply($context->quantity);
        $annual = $perCycle->multiply($context->period->occurrencesPerYear());

        $discount = $context->period === BillingPeriod::Yearly
            ? max($context->discountPercent, $this->yearlyPrepaymentDiscount)
            : $context->discountPercent;

        return $annual->multiply(1 - ($discount / 100));
    }

    public function explain(PricingContext $context): string
    {
        return __('pricing.explain.recurring', [
            'base' => $context->basePrice->format(),
            'period' => $context->period->label(),
            'cycles' => $context->period->occurrencesPerYear(),
        ]);
    }
}
