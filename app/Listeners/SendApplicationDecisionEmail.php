<?php

namespace App\Listeners;

use App\Events\ApplicationDecisionMade;
use App\Mail\AdoptionApplicationApproved;
use App\Mail\AdoptionApplicationRejected;
use Illuminate\Support\Facades\Mail;

class SendApplicationDecisionEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ApplicationDecisionMade $event): void
    {
        $mailable = match ($event->decision) {
            'approved' => new AdoptionApplicationApproved($event->application),
            'rejected' => new AdoptionApplicationRejected($event->application),
        };

        Mail::to($event->application->user)->send($mailable);
    }
}
