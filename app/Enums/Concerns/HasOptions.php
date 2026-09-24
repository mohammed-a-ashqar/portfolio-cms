<?php

declare(strict_types=1);

namespace App\Enums\Concerns;

/**
 * Shared helpers for backed enums that are rendered in the UI.
 *
 * Keeping this in a trait means every enum exposes the same contract to
 * Blade selects, validation rules and API resources without repetition.
 */
trait HasOptions
{
    /** @return array<int, string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /** @return array<string, string> value => translated label */
    public static function options(): array
    {
        return array_reduce(
            self::cases(),
            static fn (array $carry, self $case): array => $carry + [$case->value => $case->label()],
            [],
        );
    }

    public static function fromOrDefault(?string $value, self $default): self
    {
        return $value !== null ? (self::tryFrom($value) ?? $default) : $default;
    }

    public function is(self ...$cases): bool
    {
        return in_array($this, $cases, true);
    }

    public function isNot(self ...$cases): bool
    {
        return ! $this->is(...$cases);
    }
}
