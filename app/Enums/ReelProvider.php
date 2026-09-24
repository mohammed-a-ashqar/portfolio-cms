<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

/**
 * Every short-video source the showcase can pull from.
 *
 * The enum is the key the ReelProviderManager resolves a strategy with, so
 * adding TikTok support later means adding a case and a class — nothing else.
 */
enum ReelProvider: string
{
    use HasOptions;

    case Instagram = 'instagram';
    case YouTube = 'youtube';
    case TikTok = 'tiktok';
    case Manual = 'manual';

    public function label(): string
    {
        return __("enums.reel_provider.{$this->value}");
    }

    public function brandColor(): string
    {
        return match ($this) {
            self::Instagram => '#E1306C',
            self::YouTube => '#FF0000',
            self::TikTok => '#000000',
            self::Manual => '#64748B',
        };
    }

    /** Host fragments used to detect the provider from a pasted URL. */
    public function hosts(): array
    {
        return match ($this) {
            self::Instagram => ['instagram.com', 'instagr.am'],
            self::YouTube => ['youtube.com', 'youtu.be'],
            self::TikTok => ['tiktok.com'],
            self::Manual => [],
        };
    }

    public static function detectFromUrl(string $url): self
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));

        foreach (self::cases() as $case) {
            foreach ($case->hosts() as $needle) {
                if ($host !== '' && str_contains($host, $needle)) {
                    return $case;
                }
            }
        }

        return self::Manual;
    }
}
