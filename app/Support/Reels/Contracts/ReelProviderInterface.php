<?php

declare(strict_types=1);

namespace App\Support\Reels\Contracts;

use App\Enums\ReelProvider;
use App\Exceptions\ReelImportException;
use App\Support\Reels\ReelMetadata;

/**
 * One strategy per short-video platform.
 *
 * Adding TikTok means writing a class against this interface and registering
 * it in the manager. No controller, action or view changes.
 */
interface ReelProviderInterface
{
    public function provider(): ReelProvider;

    /** Can this strategy handle the pasted URL? */
    public function supports(string $url): bool;

    /**
     * Turn a public URL into normalised metadata.
     *
     * @throws ReelImportException when the URL cannot be parsed.
     */
    public function fetch(string $url): ReelMetadata;
}
