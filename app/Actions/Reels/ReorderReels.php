<?php

declare(strict_types=1);

namespace App\Actions\Reels;

use App\Models\Reel;
use Illuminate\Support\Facades\DB;

/** Persists the drag-and-drop order sent by the admin grid. */
final readonly class ReorderReels
{
    /** @param array<int, int|string> $orderedIds */
    public function handle(array $orderedIds): void
    {
        DB::transaction(static fn () => Reel::applyOrder($orderedIds));
    }
}
