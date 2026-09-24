<?php

declare(strict_types=1);

namespace App\Support\Pricing;

use App\Support\Pricing\Contracts\PricingStrategyInterface;
use RuntimeException;

/** Picks the strategy that handles the context's billing period. */
final class PricingCalculator
{
    /** @var array<int, PricingStrategyInterface> */
    private array $strategies;

    public function __construct(PricingStrategyInterface ...$strategies)
    {
        $this->strategies = $strategies;
    }

    public function calculate(PricingContext $context): Money
    {
        return $this->strategyFor($context)->calculate($context);
    }

    public function explain(PricingContext $context): string
    {
        return $this->strategyFor($context)->explain($context);
    }

    private function strategyFor(PricingContext $context): PricingStrategyInterface
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->handles($context->period)) {
                return $strategy;
            }
        }

        throw new RuntimeException("No pricing strategy handles [{$context->period->value}].");
    }
}
