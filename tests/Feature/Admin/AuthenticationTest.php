<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guests_are_sent_to_the_admin_login(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
    }

    #[Test]
    public function an_admin_can_sign_in(): void
    {
        $user = User::factory()->admin()->create(['email' => 'admin@example.com']);

        $this->post(route('admin.login.attempt'), ['email' => 'ADMIN@example.com', 'password' => 'password'])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->last_login_at);
    }

    #[Test]
    public function a_wrong_password_is_rejected(): void
    {
        User::factory()->admin()->create(['email' => 'admin@example.com']);

        $this->post(route('admin.login.attempt'), ['email' => 'admin@example.com', 'password' => 'nope'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    #[Test]
    public function a_deactivated_account_cannot_sign_in(): void
    {
        User::factory()->admin()->inactive()->create(['email' => 'admin@example.com']);

        $this->post(route('admin.login.attempt'), ['email' => 'admin@example.com', 'password' => 'password']);

        $this->assertGuest();
    }

    #[Test]
    public function deactivation_takes_effect_on_the_next_request(): void
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user)->get(route('admin.dashboard'))->assertOk();

        $user->update(['is_active' => false]);

        $this->actingAs($user->fresh())->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
        $this->assertGuest();
    }

    #[Test]
    public function the_dashboard_renders_with_data(): void
    {
        $this->seed();

        $this->actingAs(User::where('email', 'admin@example.com')->sole())
            ->get(route('admin.dashboard'))
            ->assertOk();
    }
}
