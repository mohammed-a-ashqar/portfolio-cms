<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\QuoteRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class QuoteRequestNotification extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public readonly QuoteRequest $quote) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[{$this->quote->reference}] ".__('quotes.mail.subject', ['name' => $this->quote->name]),
            // Replying in the mail client goes straight to the prospect.
            replyTo: [$this->quote->email],
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.quotes.submitted');
    }
}
