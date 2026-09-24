<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Enums\ReelProvider;
use App\Models\Reel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ReelManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Http::preventStrayRequests();
        Storage::fake('public');
    }

    #[Test]
    public function an_editor_imports_an_instagram_reel_from_its_url(): void
    {
        $this->actingAs(User::factory()->editor()->create())
            ->post(route('admin.reels.store'), [
                'url' => 'https://www.instagram.com/reel/C8xYz1AbCdE/?igsh=abc',
                'caption' => ['en' => 'Dashboard build', 'ar' => 'بناء لوحة تحكم'],
                'is_active' => '1',
            ])
            ->assertRedirect();

        $reel = Reel::sole();

        $this->assertSame(ReelProvider::Instagram, $reel->provider);
        $this->assertSame('C8xYz1AbCdE', $reel->external_id);
        $this->assertSame('https://www.instagram.com/reel/C8xYz1AbCdE/embed/', $reel->embed_url);
        $this->assertSame('بناء لوحة تحكم', $reel->translate('caption', 'ar'));
    }

    #[Test]
    public function importing_the_same_link_twice_updates_instead_of_duplicating(): void
    {
        $editor = User::factory()->editor()->create();
        $url = 'https://youtube.com/shorts/dQw4w9WgXcQ';

        $this->actingAs($editor)->post(route('admin.reels.store'), ['url' => $url, 'caption' => ['en' => 'First']]);
        $this->actingAs($editor)->post(route('admin.reels.store'), ['url' => $url, 'caption' => ['en' => 'Second']]);

        $this->assertSame(1, Reel::count());
        $this->assertSame('Second', Reel::sole()->caption);
    }

    #[Test]
    public function an_uploaded_poster_wins_over_the_platform_thumbnail(): void
    {
        $this->actingAs(User::factory()->editor()->create())
            ->post(route('admin.reels.store'), [
                'url' => 'https://youtube.com/shorts/dQw4w9WgXcQ',
                'caption' => ['en' => 'x'],
                'poster' => UploadedFile::fake()->image('poster.jpg', 540, 960),
            ]);

        $reel = Reel::sole();

        Storage::disk('public')->assertExists($reel->thumbnail_path);
        $this->assertStringContainsString($reel->thumbnail_path, (string) $reel->poster_url);
    }

    #[Test]
    public function a_viewer_cannot_import(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('admin.reels.store'), ['url' => 'https://youtube.com/shorts/dQw4w9WgXcQ', 'caption' => ['en' => 'x']])
            ->assertForbidden();

        $this->assertDatabaseCount('reels', 0);
    }

    #[Test]
    public function reels_can_be_reordered(): void
    {
        [$a, $b, $c] = Reel::factory()->count(3)->create()->all();

        $this->actingAs(User::factory()->editor()->create())
            ->postJson(route('admin.reels.reorder'), ['ids' => [$c->id, $a->id, $b->id]])
            ->assertOk();

        $this->assertSame([$c->id, $a->id, $b->id], Reel::query()->ordered()->pluck('id')->all());
    }
}
