<?php

declare(strict_types=1);

namespace App\Support\Pricing;

use InvalidArgumentException;

/**
 * Money stored as integer minor units.
 *
 * Prices are never floats here: 0.1 + 0.2 !== 0.3 in binary floating point,
 * and a quotation system that rounds wrong loses trust fast.
 */
final readonly class Money
{
    public function __construct(
        public int $amountInMinorUnits,
        public string $currency = 'USD',
    ) {
        if ($amountInMinorUnits < 0) {
            throw new InvalidArgumentException('Money cannot be negative.');
        }
    }

    public static function fromMajorUnits(float|int|string $amount, string $currency = 'USD'): self
    {
        return new self((int) round(((float) $amount) * 100), $currency);
    }

    public static function zero(string $currency = 'USD'): self
    {
        return new self(0, $currency);
    }

    public function toMajorUnits(): float
    {
        return $this->amountInMinorUnits / 100;
    }

    public function add(self $other): self
    {
        $this->assertSameCurrency($other);

        return new self($this->amountInMinorUnits + $other->amountInMinorUnits, $this->currency);
    }

    public function multiply(float $factor): self
    {
        return new self((int) round($this->amountInMinorUnits * $factor), $this->currency);
    }

    public function percentage(float $percent): self
    {
        return $this->multiply($percent / 100);
    }

    public function format(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        if (class_exists(\NumberFormatter::class)) {
            $formatter = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);

            return (string) $formatter->formatCurrency($this->toMajorUnits(), $this->currency);
        }

        return sprintf('%s %s', number_format($this->toMajorUnits(), 2), $this->currency);
    }

    private function assertSameCurrency(self $other): void
    {
        if ($other->currency !== $this->currency) {
            throw new InvalidArgumentException("Currency mismatch: {$this->currency} vs {$other->currency}.");
        }
    }
}
