<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class ContactMessageNotification extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public readonly ContactMessage $message) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->message->subject
                ? __('contact.mail.subject_with', ['subject' => $this->message->subject])
                : __('contact.mail.subject', ['name' => $this->message->name]),
            replyTo: [$this->message->email],
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.contact.received');
    }
}
