<?php

declare(strict_types=1);

namespace App\Http\Requests\Concerns;

/**
 * Builds validation rules for a translatable field across every configured
 * locale, requiring only the fallback one.
 *
 * Without this, adding a locale to config/portfolio.php would mean editing
 * every FormRequest by hand — and forgetting one is how half-validated
 * translations reach the database.
 */
trait ValidatesTranslatableFields
{
    /**
     * @param  array<int, mixed>  $rules  applied to each locale's value
     * @return array<string, mixed>
     */
    protected function translatableRules(string $field, array $rules = ['string', 'max:255'], bool $required = true): array
    {
        $fallback = config('app.fallback_locale');
        $built = [$field => [$required ? 'required' : 'nullable', 'array']];

        foreach (array_keys(config('portfolio.locales', [])) as $locale) {
            $built["{$field}.{$locale}"] = array_merge(
                [$required && $locale === $fallback ? 'required' : 'nullable'],
                $rules,
            );
        }

        return $built;
    }

    /**
     * Strips empty locales so a blank Arabic box does not store "ar": "".
     *
     * @return array<string, string>
     */
    protected function translatableInput(string $field): array
    {
        return array_filter(
            (array) $this->input($field, []),
            static fn (mixed $value): bool => is_string($value) && trim($value) !== '',
        );
    }
}
