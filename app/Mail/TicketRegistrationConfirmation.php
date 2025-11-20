<?php

namespace App\Mail;

use App\Models\Draw;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketRegistrationConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Draw $draw,
        public Collection $tickets,
        public float $totalAmount
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Ticket Registration Confirmation - '.$this->draw->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.ticket-registration-confirmation',
            with: [
                'draw' => $this->draw,
                'tickets' => $this->tickets,
                'totalAmount' => $this->totalAmount,
                'prizeAmount' => $this->draw->prizeAmount(),
                'totalTicketsSold' => $this->draw->totalTicketsSold(),
            ],
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
