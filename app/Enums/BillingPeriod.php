<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum BillingPeriod: string
{
    use HasOptions;

    case OneTime = 'one_time';
    case Hourly = 'hourly';
    case Monthly = 'monthly';
    case Yearly = 'yearly';

    public function label(): string
    {
        return __("enums.billing_period.{$this->value}");
    }

    /** Suffix rendered after a price, e.g. "$50 / hour". */
    public function suffix(): ?string
    {
        return match ($this) {
            self::OneTime => null,
            self::Hourly => __('enums.billing_period_suffix.hourly'),
            self::Monthly => __('enums.billing_period_suffix.monthly'),
            self::Yearly => __('enums.billing_period_suffix.yearly'),
        };
    }

    /** How many times a year this period bills — used by the pricing strategies. */
    public function occurrencesPerYear(): int
    {
        return match ($this) {
            self::OneTime => 1,
            self::Hourly => 0, // usage based, not recurring
            self::Monthly => 12,
            self::Yearly => 1,
        };
    }
}
