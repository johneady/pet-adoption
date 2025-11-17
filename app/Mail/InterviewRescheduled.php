<?php

namespace App\Mail;

use App\Models\Interview;
use App\Models\User;
use App\Services\CalendarService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class InterviewRescheduled extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public Interview $interview, public User $rescheduledBy, public Carbon $oldScheduledAt)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Interview Rescheduled - Updated Date/Time',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.interview-rescheduled',
            with: [
                'interview' => $this->interview,
                'application' => $this->interview->adoptionApplication,
                'pet' => $this->interview->adoptionApplication->pet,
                'applicant' => $this->interview->adoptionApplication->user,
                'rescheduledBy' => $this->rescheduledBy,
                'oldScheduledAt' => $this->oldScheduledAt,
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
        $calendarService = app(CalendarService::class);

        return [
            Attachment::fromData(
                fn () => $calendarService->generateInterviewCalendar($this->interview),
                'interview.ics'
            )->withMime('text/calendar'),
        ];
    }
}
