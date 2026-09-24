<?php

declare(strict_types=1);

namespace App\DTOs\Concerns;

use ReflectionClass;
use ReflectionNamedType;

/**
 * Minimal DTO plumbing.
 *
 * Rolled by hand rather than pulled from a package: it is forty lines, it
 * keeps the dependency list honest, and the reflection only runs on write
 * paths (form submits), never in a loop.
 */
trait TransfersData
{
    /** Drops nulls so partial updates do not blank out existing columns. */
    public function toArray(): array
    {
        return array_filter(
            get_object_vars($this),
            static fn (mixed $value): bool => $value !== null,
        );
    }

    /** Includes nulls — use when a null genuinely means "clear this field". */
    public function toArrayWithNulls(): array
    {
        return get_object_vars($this);
    }

    /** @param array<string, mixed> $attributes */
    public static function fromArray(array $attributes): static
    {
        $constructor = (new ReflectionClass(static::class))->getConstructor();

        if ($constructor === null) {
            return new static;
        }

        $arguments = [];

        foreach ($constructor->getParameters() as $parameter) {
            $name = $parameter->getName();

            if (array_key_exists($name, $attributes)) {
                $arguments[$name] = self::castValue($attributes[$name], $parameter->getType());

                continue;
            }

            $arguments[$name] = $parameter->isDefaultValueAvailable()
                ? $parameter->getDefaultValue()
                : null;
        }

        return new static(...$arguments);
    }

    /** Hydrates backed enums from their scalar value; leaves everything else alone. */
    private static function castValue(mixed $value, ?\ReflectionType $type): mixed
    {
        if (! $type instanceof ReflectionNamedType || $value === null) {
            return $value;
        }

        $name = $type->getName();

        if (enum_exists($name) && ! $value instanceof $name) {
            return $name::tryFrom($value);
        }

        return $value;
    }
}
