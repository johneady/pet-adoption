<?php

namespace App\Mail;

use App\Models\Draw;
use App\Models\DrawTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DrawResultsSummary extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Draw $draw,
        public DrawTicket $winningTicket
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '50/50 Draw Results - '.$this->draw->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.draw-results-summary',
            with: [
                'draw' => $this->draw,
                'winningTicket' => $this->winningTicket,
                'totalTickets' => $this->draw->totalTicketsSold(),
                'totalAmount' => $this->draw->totalAmountCollected(),
                'prizeAmount' => $this->draw->prizeAmount(),
                'durationDays' => $this->draw->durationInDays(),
                'uniqueParticipants' => $this->draw->tickets()->distinct('user_id')->count('user_id'),
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
