<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

final class ReelImportException extends RuntimeException
{
    public static function unsupportedUrl(string $url): self
    {
        return new self(__('reels.errors.unsupported_url', ['url' => $url]));
    }

    public static function unparsableUrl(string $url): self
    {
        return new self(__('reels.errors.unparsable_url', ['url' => $url]));
    }

    public static function duplicate(string $externalId): self
    {
        return new self(__('reels.errors.duplicate', ['id' => $externalId]));
    }
}
