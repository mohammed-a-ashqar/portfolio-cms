<?php

declare(strict_types=1);

namespace Tests\Feature\Front;

use App\Enums\QuoteStatus;
use App\Events\ContactMessageReceived;
use App\Events\QuoteRequestSubmitted;
use App\Mail\QuoteRequestNotification;
use App\Models\ContactMessage;
use App\Models\QuoteRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ContactAndQuoteFormsTest extends TestCase
{
    use RefreshDatabase;

    private function contactPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Layla',
            'email' => 'Layla@Example.com',
            'subject' => 'Hello',
            'message' => 'I would like to talk about a project.',
        ], $overrides);
    }

    #[Test]
    public function a_contact_message_is_stored_and_announced(): void
    {
        Event::fake([ContactMessageReceived::class]);

        $this->from(route('contact.index'))
            ->post(route('contact.store'), $this->contactPayload())
            ->assertRedirect(route('contact.index'))
            ->assertSessionHas('status');

        $message = ContactMessage::sole();
        $this->assertSame('layla@example.com', $message->email, 'email is normalised');
        Event::assertDispatched(ContactMessageReceived::class);
    }

    #[Test]
    public function the_honeypot_rejects_bots(): void
    {
        $this->post(route('contact.store'), $this->contactPayload(['website' => 'http://spam.test']))
            ->assertSessionHasErrors('website');

        $this->assertDatabaseCount('contact_messages', 0);
    }

    #[Test]
    public function one_address_cannot_flood_the_inbox(): void
    {
        ContactMessage::factory()->count(3)->create(['email' => 'layla@example.com']);

        $this->post(route('contact.store'), $this->contactPayload())
            ->assertSessionHasErrors('message');

        $this->assertDatabaseCount('contact_messages', 3);
    }

    #[Test]
    public function a_quote_request_gets_a_reference_and_money_in_minor_units(): void
    {
        $this->post(route('quotes.store'), [
            'name' => 'Omar',
            'email' => 'omar@example.com',
            'budget_min' => 1500.50,
            'budget_max' => 3000,
            'currency' => 'usd',
            'message' => 'We need an admin dashboard for our logistics company.',
        ])->assertRedirect(route('quotes.create'));

        $quote = QuoteRequest::sole();

        $this->assertMatchesRegularExpression('/^QR-\d{4}-0001$/', $quote->reference);
        $this->assertSame(150050, $quote->budget_min_minor);
        $this->assertSame('USD', $quote->currency);
        $this->assertSame(QuoteStatus::New, $quote->status);
    }

    #[Test]
    public function the_owner_is_emailed_about_a_new_quote(): void
    {
        Mail::fake();
        config(['portfolio.notifications.quotes_to' => 'me@example.com']);

        $this->post(route('quotes.store'), [
            'name' => 'Omar',
            'email' => 'omar@example.com',
            'message' => 'We need an admin dashboard for our logistics company.',
        ]);

        Mail::assertSent(QuoteRequestNotification::class, fn ($mail) => $mail->hasTo('me@example.com')
            && $mail->hasReplyTo('omar@example.com'));
    }

    #[Test]
    public function a_budget_range_must_be_ordered(): void
    {
        Event::fake([QuoteRequestSubmitted::class]);

        $this->post(route('quotes.store'), [
            'name' => 'Omar',
            'email' => 'omar@example.com',
            'budget_min' => 5000,
            'budget_max' => 1000,
            'message' => 'We need an admin dashboard for our logistics company.',
        ])->assertSessionHasErrors('budget_max');

        Event::assertNotDispatched(QuoteRequestSubmitted::class);
    }
}
