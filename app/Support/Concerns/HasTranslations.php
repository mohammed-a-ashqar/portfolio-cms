<?php

declare(strict_types=1);

namespace App\Support\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * Stores translatable attributes as a JSON map: {"en": "...", "ar": "..."}.
 *
 * A JSON column beats a side table here: the portfolio only ever renders one
 * locale at a time, so this removes a join from every read path while still
 * letting MySQL index and search inside the document.
 *
 * Usage:
 *   protected array $translatable = ['title', 'description'];
 */
trait HasTranslations
{
    /** @return array<int, string> */
    public function getTranslatableAttributes(): array
    {
        return $this->translatable ?? [];
    }

    public function isTranslatable(string $key): bool
    {
        return in_array($key, $this->getTranslatableAttributes(), true);
    }

    /**
     * Resolve one locale, falling back to the app fallback and then to the
     * first non-empty value so a half-translated record never renders blank.
     */
    public function translate(string $key, ?string $locale = null): ?string
    {
        $translations = $this->getTranslations($key);

        if ($translations === []) {
            return null;
        }

        $locale ??= app()->getLocale();

        foreach ([$locale, config('app.fallback_locale')] as $candidate) {
            $value = $translations[$candidate] ?? null;

            if (filled($value)) {
                return $value;
            }
        }

        return collect($translations)->first(fn ($value) => filled($value));
    }

    /** @return array<string, string> */
    public function getTranslations(string $key): array
    {
        $raw = $this->getAttributeFromArray($key);

        if (blank($raw)) {
            return [];
        }

        $decoded = is_array($raw) ? $raw : json_decode((string) $raw, true);

        return is_array($decoded) ? array_filter($decoded, 'is_string') : [];
    }

    public function setTranslation(string $key, string $locale, ?string $value): static
    {
        $translations = $this->getTranslations($key);

        if ($value === null || $value === '') {
            unset($translations[$locale]);
        } else {
            $translations[$locale] = $value;
        }

        $this->attributes[$key] = json_encode($translations, JSON_UNESCAPED_UNICODE);

        return $this;
    }

    /** @param array<string, string|null> $values */
    public function setTranslations(string $key, array $values): static
    {
        $this->attributes[$key] = null;

        foreach ($values as $locale => $value) {
            $this->setTranslation($key, (string) $locale, $value);
        }

        return $this;
    }

    /** Search inside the JSON document for the active locale. */
    public function scopeWhereTranslationLike(Builder $query, string $key, string $term, ?string $locale = null): Builder
    {
        $locale ??= app()->getLocale();

        return $query->where("{$key}->{$locale}", 'like', "%{$term}%");
    }

    public function scopeOrderByTranslation(Builder $query, string $key, string $direction = 'asc', ?string $locale = null): Builder
    {
        $locale ??= app()->getLocale();

        return $query->orderBy("{$key}->{$locale}", $direction);
    }

    /** Transparently return the active locale's string for `$project->title`. */
    public function getAttributeValue($key)
    {
        if ($this->isTranslatable($key)) {
            return $this->translate($key);
        }

        return parent::getAttributeValue($key);
    }

    public function setAttribute($key, $value)
    {
        if ($this->isTranslatable($key) && is_array($value)) {
            return $this->setTranslations($key, $value);
        }

        if ($this->isTranslatable($key) && is_string($value)) {
            return $this->setTranslation($key, app()->getLocale(), $value);
        }

        return parent::setAttribute($key, $value);
    }
}
