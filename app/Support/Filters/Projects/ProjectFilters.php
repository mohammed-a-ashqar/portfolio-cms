<?php

declare(strict_types=1);

namespace App\Support\Filters\Projects;

use App\Support\Filters\Filter;
use Illuminate\Http\Request;

/**
 * Translates request input into an ordered list of filters.
 *
 * Keeping the mapping here means the controller passes a DTO-ish object around
 * instead of the whole Request, and the same set is reusable from a console
 * command, a queued export, or a test.
 */
final readonly class ProjectFilters
{
    public function __construct(
        public ?string $search = null,
        public ?string $category = null,
        public array $technologies = [],
        public ?string $status = null,
        public ?string $featured = null,
        public string $sort = 'manual',
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            search: $request->string('search')->trim()->value() ?: null,
            category: $request->string('category')->value() ?: null,
            technologies: array_filter((array) $request->input('technologies', [])),
            status: $request->string('status')->value() ?: null,
            featured: $request->has('featured') ? (string) $request->input('featured') : null,
            sort: $request->string('sort')->value() ?: 'manual',
        );
    }

    /** @return array<int, Filter> */
    public function toPipes(): array
    {
        return [
            new SearchFilter($this->search),
            new CategoryFilter($this->category),
            new TechnologyFilter($this->technologies),
            new StatusFilter($this->status),
            new FeaturedFilter($this->featured),
            new SortFilter($this->sort),
        ];
    }

    /** Used as part of the cache key for the public listing. */
    public function fingerprint(): string
    {
        return md5(json_encode([
            $this->search, $this->category, $this->technologies,
            $this->status, $this->featured, $this->sort, app()->getLocale(),
        ], JSON_THROW_ON_ERROR));
    }

    public function isEmpty(): bool
    {
        return blank($this->search) && blank($this->category) && $this->technologies === []
            && blank($this->status) && blank($this->featured);
    }
}
