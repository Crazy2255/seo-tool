<?php

namespace App\Mail;

use App\Models\LeadMagnet;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeadMagnetInvite extends Mailable
{
    use Queueable, SerializesModels;

    public LeadMagnet $leadMagnet;
    public string $recipientEmail;

    /**
     * Create a new message instance.
     */
    public function __construct(LeadMagnet $leadMagnet, string $recipientEmail)
    {
        $this->leadMagnet = $leadMagnet;
        $this->recipientEmail = $recipientEmail;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Get Your Free Download: {$this->leadMagnet->title}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.lead-magnet-invite',
            with: [
                'leadMagnet' => $this->leadMagnet,
                'landingUrl' => $this->leadMagnet->landing_url,
                'recipientEmail' => $this->recipientEmail,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
