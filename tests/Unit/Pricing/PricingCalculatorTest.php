<?php

declare(strict_types=1);

namespace Tests\Unit\Pricing;

use App\Enums\BillingPeriod;
use App\Support\Pricing\FixedPricing;
use App\Support\Pricing\HourlyPricing;
use App\Support\Pricing\Money;
use App\Support\Pricing\PricingCalculator;
use App\Support\Pricing\PricingContext;
use App\Support\Pricing\RecurringPricing;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PricingCalculatorTest extends TestCase
{
    private PricingCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->calculator = new PricingCalculator(new FixedPricing, new HourlyPricing, new RecurringPricing);
    }

    #[Test]
    public function fixed_price_applies_the_discount(): void
    {
        $total = $this->calculator->calculate(
            new PricingContext(Money::fromMajorUnits(2500), BillingPeriod::OneTime, discountPercent: 10),
        );

        $this->assertSame(225000, $total->amountInMinorUnits);
    }

    #[Test]
    public function hourly_price_picks_the_highest_volume_tier_reached(): void
    {
        // 120h crosses the 100h tier (10%) but not the 200h tier (15%).
        $total = $this->calculator->calculate(
            new PricingContext(Money::fromMajorUnits(50), BillingPeriod::Hourly, quantity: 120),
        );

        $this->assertSame(540000, $total->amountInMinorUnits);
    }

    #[Test]
    public function hourly_price_below_every_tier_is_undiscounted(): void
    {
        $total = $this->calculator->calculate(
            new PricingContext(Money::fromMajorUnits(50), BillingPeriod::Hourly, quantity: 10),
        );

        $this->assertSame(50000, $total->amountInMinorUnits);
    }

    #[Test]
    public function monthly_retainer_is_quoted_as_an_annual_commitment(): void
    {
        $total = $this->calculator->calculate(
            new PricingContext(Money::fromMajorUnits(800), BillingPeriod::Monthly),
        );

        $this->assertSame(960000, $total->amountInMinorUnits);
    }

    #[Test]
    public function yearly_prepayment_gets_the_default_discount(): void
    {
        $total = $this->calculator->calculate(
            new PricingContext(Money::fromMajorUnits(9000), BillingPeriod::Yearly),
        );

        $this->assertSame(810000, $total->amountInMinorUnits);
    }

    #[Test]
    public function money_avoids_floating_point_drift(): void
    {
        $sum = Money::fromMajorUnits(0.1)->add(Money::fromMajorUnits(0.2));

        $this->assertSame(30, $sum->amountInMinorUnits);
    }

    #[Test]
    public function money_refuses_to_mix_currencies(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Money::fromMajorUnits(1, 'USD')->add(Money::fromMajorUnits(1, 'EUR'));
    }
}
