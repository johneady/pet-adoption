<?php

namespace App\Listeners;

use App\Mail\WelcomeNewUser;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmail
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
    public function handle(Verified $event): void
    {
        Mail::to($event->user)->send(new WelcomeNewUser($event->user));
    }
}
