<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Enums\ProjectStatus;
use App\Events\ProjectPublished;
use App\Models\Project;
use App\Models\Technology;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ProjectManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    #[Test]
    public function an_editor_creates_a_bilingual_project_with_a_cover(): void
    {
        Event::fake([ProjectPublished::class]);
        $tech = Technology::factory()->count(2)->create();

        $this->actingAs(User::factory()->editor()->create())
            ->post(route('admin.projects.store'), [
                'title' => ['en' => 'Hospital Portal', 'ar' => 'بوابة المستشفى'],
                'summary' => ['en' => 'Patient portal', 'ar' => ''],
                'status' => ProjectStatus::Published->value,
                'technology_ids' => $tech->pluck('id')->all(),
                'cover_image' => UploadedFile::fake()->image('cover.jpg', 1600, 1000),
            ])
            ->assertSessionHasNoErrors()->assertRedirect();

        $project = Project::sole();

        $this->assertSame('hospital-portal', $project->slug);
        $this->assertSame('بوابة المستشفى', $project->translate('title', 'ar'));
        $this->assertArrayNotHasKey('ar', $project->getTranslations('summary'), 'blank locales are not stored');
        $this->assertCount(2, $project->technologies);
        Storage::disk('public')->assertExists($project->cover_image);
        Event::assertDispatched(ProjectPublished::class);
    }

    #[Test]
    public function the_default_locale_title_is_required(): void
    {
        $this->actingAs(User::factory()->editor()->create())
            ->post(route('admin.projects.store'), [
                'title' => ['ar' => 'عنوان فقط'],
                'status' => ProjectStatus::Draft->value,
            ])
            ->assertSessionHasErrors('title.en');
    }

    #[Test]
    public function duplicate_titles_get_unique_slugs(): void
    {
        $first = Project::factory()->create(['title' => ['en' => 'Same name']]);
        $second = Project::factory()->create(['title' => ['en' => 'Same name']]);

        $this->assertSame('same-name', $first->slug);
        $this->assertSame('same-name-2', $second->slug);
    }

    #[Test]
    public function an_arabic_only_title_still_produces_a_slug(): void
    {
        $project = Project::factory()->create(['title' => ['ar' => 'متجر إلكتروني']]);

        $this->assertNotEmpty($project->slug);
    }

    #[Test]
    public function a_viewer_can_read_but_not_write(): void
    {
        $viewer = User::factory()->create();
        $project = Project::factory()->create();

        $this->actingAs($viewer)->get(route('admin.projects.index'))->assertOk();
        $this->actingAs($viewer)->get(route('admin.projects.create'))->assertForbidden();
        $this->actingAs($viewer)->delete(route('admin.projects.destroy', $project))->assertForbidden();
    }

    #[Test]
    public function deleting_is_soft_so_mistakes_are_recoverable(): void
    {
        $project = Project::factory()->create();

        $this->actingAs(User::factory()->editor()->create())
            ->delete(route('admin.projects.destroy', $project))
            ->assertRedirect(route('admin.projects.index'));

        $this->assertSoftDeleted($project);
    }
}
