<?php

declare(strict_types=1);

namespace App\Support\Pricing\Contracts;

use App\Enums\BillingPeriod;
use App\Support\Pricing\Money;
use App\Support\Pricing\PricingContext;

/**
 * How a package turns into a number.
 *
 * Each billing model is its own class, so adding "retainer with included
 * hours" later does not mean another branch in a growing match statement.
 */
interface PricingStrategyInterface
{
    public function handles(BillingPeriod $period): bool;

    public function calculate(PricingContext $context): Money;

    /** Human-readable breakdown shown on the quotation. */
    public function explain(PricingContext $context): string;
}
