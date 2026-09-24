<?php

declare(strict_types=1);

namespace Tests\Feature\Front;

use App\Models\Project;
use App\Models\Reel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function every_public_page_renders(): void
    {
        foreach (['home', 'projects.index', 'reels.index', 'services.index', 'contact.index', 'quotes.create'] as $route) {
            $this->get(route($route))->assertOk();
        }
    }

    #[Test]
    public function published_projects_are_visible_and_count_a_view(): void
    {
        $project = Project::factory()->create(['title' => ['en' => 'Clinic system']]);

        $this->get(route('projects.show', $project->slug))
            ->assertOk()
            ->assertSee('Clinic system');

        $this->assertSame(1, $project->fresh()->views_count);
    }

    #[Test]
    public function drafts_are_not_leaked(): void
    {
        $draft = Project::factory()->draft()->create();

        $this->get(route('projects.show', $draft->slug))->assertNotFound();
        $this->get(route('projects.index'))->assertDontSee($draft->title);
    }

    #[Test]
    public function the_locale_switcher_changes_language_and_direction(): void
    {
        Project::factory()->create(['title' => ['en' => 'English title', 'ar' => 'عنوان عربي']]);

        $this->get(route('projects.index', ['lang' => 'ar']))
            ->assertOk()
            ->assertSee('dir="rtl"', false)
            ->assertSee('عنوان عربي');

        // The choice sticks for the rest of the session.
        $this->get(route('projects.index'))->assertSee('dir="rtl"', false);
    }

    #[Test]
    public function an_unknown_locale_is_ignored(): void
    {
        $this->get(route('home', ['lang' => '../../etc']))
            ->assertOk()
            ->assertSee('lang="en"', false);
    }

    #[Test]
    public function missing_translations_fall_back_instead_of_rendering_blank(): void
    {
        $project = Project::factory()->create(['title' => ['en' => 'Only in English']]);

        $this->get(route('projects.show', ['project' => $project->slug, 'lang' => 'ar']))
            ->assertOk()
            ->assertSee('Only in English');
    }

    #[Test]
    public function the_reel_grid_lazy_loads_embeds_and_hides_inactive_reels(): void
    {
        $visible = Reel::factory()->create(['caption' => ['en' => 'Visible reel']]);
        Reel::factory()->hidden()->create(['caption' => ['en' => 'Hidden reel']]);

        $this->get(route('reels.index'))
            ->assertOk()
            ->assertSee('Visible reel')
            ->assertDontSee('Hidden reel')
            // Tiles hand the embed URL to the modal; no iframe is rendered up front.
            ->assertSee($visible->embed_url, false)
            ->assertDontSee('<iframe src=', false);
    }
}
