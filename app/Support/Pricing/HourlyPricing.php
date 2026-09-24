<?php

declare(strict_types=1);

namespace App\Support\Pricing;

use App\Enums\BillingPeriod;
use App\Support\Pricing\Contracts\PricingStrategyInterface;

/**
 * Time and materials. `quantity` is hours.
 *
 * Applies an optional volume discount taken from `extras.volume_tiers`, which
 * lets the admin change the tiers from settings without a deploy.
 */
final readonly class HourlyPricing implements PricingStrategyInterface
{
    public function handles(BillingPeriod $period): bool
    {
        return $period === BillingPeriod::Hourly;
    }

    public function calculate(PricingContext $context): Money
    {
        $gross = $context->basePrice->multiply($context->quantity);
        $discount = max($context->discountPercent, $this->volumeDiscount($context));

        return $gross->multiply(1 - ($discount / 100));
    }

    public function explain(PricingContext $context): string
    {
        return __('pricing.explain.hourly', [
            'rate' => $context->basePrice->format(),
            'hours' => $context->quantity,
            'discount' => max($context->discountPercent, $this->volumeDiscount($context)),
        ]);
    }

    private function volumeDiscount(PricingContext $context): float
    {
        /** @var array<int, float> $tiers hours => percent */
        $tiers = $context->extras['volume_tiers'] ?? [40 => 5.0, 100 => 10.0, 200 => 15.0];

        krsort($tiers);

        foreach ($tiers as $hours => $percent) {
            if ($context->quantity >= $hours) {
                return (float) $percent;
            }
        }

        return 0.0;
    }
}
