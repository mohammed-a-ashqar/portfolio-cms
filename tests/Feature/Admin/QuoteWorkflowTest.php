<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Enums\QuoteStatus;
use App\Models\QuoteRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class QuoteWorkflowTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_quote_moves_forward_and_records_the_amount(): void
    {
        $quote = QuoteRequest::factory()->withStatus(QuoteStatus::InReview)->create();

        $this->actingAs(User::factory()->editor()->create())
            ->patch(route('admin.quotes.transition', $quote), [
                'status' => QuoteStatus::Quoted->value,
                'quoted_amount' => 4200,
            ])
            ->assertSessionHasNoErrors();

        $quote->refresh();

        $this->assertSame(QuoteStatus::Quoted, $quote->status);
        $this->assertSame(420000, $quote->quoted_amount_minor);
        $this->assertNotNull($quote->responded_at);
    }

    #[Test]
    public function the_state_machine_blocks_illegal_jumps(): void
    {
        $quote = QuoteRequest::factory()->create(); // New

        $this->actingAs(User::factory()->editor()->create())
            ->patch(route('admin.quotes.transition', $quote), ['status' => QuoteStatus::Accepted->value])
            ->assertSessionHasErrors('status');

        $this->assertSame(QuoteStatus::New, $quote->fresh()->status);
    }

    #[Test]
    public function the_detail_page_only_offers_legal_moves(): void
    {
        $quote = QuoteRequest::factory()->create();

        $this->actingAs(User::factory()->editor()->create())
            ->get(route('admin.quotes.show', $quote))
            ->assertOk()
            ->assertSee('value="in_review"', false)
            ->assertDontSee('value="accepted"', false);
    }

    #[Test]
    public function only_admins_can_delete_quotes(): void
    {
        $quote = QuoteRequest::factory()->create();

        $this->actingAs(User::factory()->editor()->create())
            ->delete(route('admin.quotes.destroy', $quote))
            ->assertForbidden();

        $this->actingAs(User::factory()->admin()->create())
            ->delete(route('admin.quotes.destroy', $quote))
            ->assertRedirect(route('admin.quotes.index'));

        $this->assertSoftDeleted($quote);
    }
}
