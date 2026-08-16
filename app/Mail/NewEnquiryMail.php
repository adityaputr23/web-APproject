<?php

namespace App\Mail;

use App\Models\Enquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewEnquiryMail extends Mailable
{
    use Queueable, SerializesModels;

    public Enquiry $enquiry;

    /**
     * Create a new message instance.
     */
    public function __construct(Enquiry $enquiry)
    {
        $this->enquiry = $enquiry;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '📩 Pesan Baru dari ' . $this->enquiry->name . ' — ' . ($this->enquiry->subject ?? 'APVISUALS Contact'),
            replyTo: [
                new \Illuminate\Mail\Mailables\Address($this->enquiry->email, $this->enquiry->name),
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.new-enquiry',
            with: [
                'enquiry' => $this->enquiry,
            ],
        );
    }
}
