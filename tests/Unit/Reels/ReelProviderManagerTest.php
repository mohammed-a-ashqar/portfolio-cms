<?php

declare(strict_types=1);

namespace Tests\Unit\Reels;

use App\Enums\ReelProvider;
use App\Support\Reels\InstagramProvider;
use App\Support\Reels\ManualProvider;
use App\Support\Reels\ReelProviderManager;
use App\Support\Reels\TikTokProvider;
use App\Support\Reels\YouTubeProvider;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ReelProviderManagerTest extends TestCase
{
    private ReelProviderManager $manager;

    protected function setUp(): void
    {
        parent::setUp();

        Http::preventStrayRequests();
        Http::fake(['*tiktok.com/oembed*' => Http::response(['title' => 'From TikTok'])]);

        $this->manager = $this->app->make(ReelProviderManager::class);
    }

    /** @return array<string, array{string, ReelProvider, string}> */
    public static function urls(): array
    {
        return [
            'instagram reel' => ['https://www.instagram.com/reel/C8xYz1AbCdE/', ReelProvider::Instagram, 'C8xYz1AbCdE'],
            'instagram post' => ['https://instagram.com/p/Abc_123-xyz/?igsh=foo', ReelProvider::Instagram, 'Abc_123-xyz'],
            'youtube short' => ['https://youtube.com/shorts/dQw4w9WgXcQ', ReelProvider::YouTube, 'dQw4w9WgXcQ'],
            'youtu.be link' => ['https://youtu.be/dQw4w9WgXcQ?t=3', ReelProvider::YouTube, 'dQw4w9WgXcQ'],
            'tiktok video' => ['https://www.tiktok.com/@me/video/7312345678901234567', ReelProvider::TikTok, '7312345678901234567'],
            'self hosted' => ['https://cdn.example.com/demo.mp4', ReelProvider::Manual, 'demo.mp4'],
        ];
    }

    #[Test]
    #[DataProvider('urls')]
    public function it_routes_each_url_to_the_right_strategy(string $url, ReelProvider $expected, string $externalId): void
    {
        $metadata = $this->manager->fetch($url);

        $this->assertSame($expected, $metadata->provider);
        $this->assertSame($externalId, $metadata->externalId);
    }

    #[Test]
    public function the_catch_all_must_stay_last(): void
    {
        $providers = $this->manager->supportedProviders();

        $this->assertSame(ReelProvider::Manual, end($providers));
    }

    #[Test]
    public function youtube_needs_no_network_and_gets_a_thumbnail(): void
    {
        $metadata = (new YouTubeProvider)->fetch('https://youtube.com/shorts/dQw4w9WgXcQ');

        $this->assertStringContainsString('dQw4w9WgXcQ', (string) $metadata->thumbnailUrl);
        $this->assertStringContainsString('youtube-nocookie.com', $metadata->embedUrl);
    }

    #[Test]
    public function instagram_without_a_token_makes_no_request(): void
    {
        $provider = new InstagramProvider($this->app->make(Factory::class), null);

        $metadata = $provider->fetch('https://www.instagram.com/reel/C8xYz1AbCdE/');

        $this->assertSame('https://www.instagram.com/reel/C8xYz1AbCdE/embed/', $metadata->embedUrl);
        Http::assertNothingSent();
    }

    #[Test]
    public function tiktok_enrichment_failure_is_not_fatal(): void
    {
        // A fresh client: the class-level fake already answers TikTok successfully,
        // and the first matching stub always wins.
        $http = new Factory;
        $http->fake(['*' => $http::response(null, 500)]);

        $metadata = (new TikTokProvider($http))
            ->fetch('https://www.tiktok.com/@me/video/7312345678901234567');

        $this->assertSame('7312345678901234567', $metadata->externalId);
        $this->assertNull($metadata->caption);
    }

    #[Test]
    public function manual_provider_accepts_any_valid_url(): void
    {
        $this->assertTrue((new ManualProvider)->supports('https://anything.test/v.mp4'));
        $this->assertFalse((new ManualProvider)->supports('not a url'));
    }
}
