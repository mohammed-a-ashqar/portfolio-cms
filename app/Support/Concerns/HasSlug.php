<?php

declare(strict_types=1);

namespace App\Support\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Generates a unique, URL-safe slug from a source attribute.
 *
 * Arabic titles slugify to an empty string under Str::slug(), so the trait
 * falls back to a transliterated or prefixed value rather than producing
 * colliding empty slugs — the bug that bites most bilingual CMS builds.
 */
trait HasSlug
{
    public static function bootHasSlug(): void
    {
        static::saving(function (Model $model): void {
            if (blank($model->{$model->slugColumn()}) || $model->isDirty($model->slugSource())) {
                $model->{$model->slugColumn()} = $model->generateSlug();
            }
        });
    }

    public function slugSource(): string
    {
        return 'title';
    }

    public function slugColumn(): string
    {
        return 'slug';
    }

    public function generateSlug(): string
    {
        $base = $this->buildSlugBase();
        $slug = $base;
        $suffix = 1;

        while ($this->slugExists($slug)) {
            $slug = "{$base}-".(++$suffix);
        }

        return $slug;
    }

    protected function buildSlugBase(): string
    {
        $source = $this->{$this->slugSource()};

        // Translatable attributes prefer the fallback locale for stable URLs.
        if (method_exists($this, 'getTranslations')) {
            $translations = $this->getTranslations($this->slugSource());
            $source = $translations[config('app.fallback_locale')] ?? reset($translations) ?: $source;
        }

        $slug = Str::slug((string) $source);

        if ($slug !== '') {
            return $slug;
        }

        // Non-latin script: keep the readable characters, strip the rest.
        $slug = trim(preg_replace('/[^\p{L}\p{N}]+/u', '-', (string) $source) ?? '', '-');

        return $slug !== '' ? mb_strtolower($slug) : Str::lower(class_basename($this)).'-'.Str::random(6);
    }

    protected function slugExists(string $slug): bool
    {
        return static::query()
            ->where($this->slugColumn(), $slug)
            ->when($this->exists, fn (Builder $query) => $query->whereKeyNot($this->getKey()))
            ->when(
                in_array(SoftDeletes::class, class_uses_recursive($this), true),
                fn (Builder $query) => $query->withTrashed(),
            )
            ->exists();
    }

    public function getRouteKeyName(): string
    {
        return $this->slugColumn();
    }
}
