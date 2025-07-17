<?php

namespace App\Mail;

use App\Models\Lead;
use App\Models\LeadMagnet;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeadMagnetDownload extends Mailable
{
    use Queueable, SerializesModels;

    public Lead $lead;
    public LeadMagnet $leadMagnet;
    public string $downloadUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Lead $lead, LeadMagnet $leadMagnet, string $downloadUrl)
    {
        $this->lead = $lead;
        $this->leadMagnet = $leadMagnet;
        $this->downloadUrl = $downloadUrl;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Free Download: ' . $this->leadMagnet->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.lead-magnet-download',
            with: [
                'lead' => $this->lead,
                'leadMagnet' => $this->leadMagnet,
                'downloadUrl' => $this->downloadUrl,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
