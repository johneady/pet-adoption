<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Interview;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;

class CalendarService
{
    /**
     * Generate an iCalendar file for an interview.
     */
    public function generateInterviewCalendar(Interview $interview): string
    {
        $application = $interview->adoptionApplication;
        $applicant = $application->user;
        $pet = $application->pet;

        $event = Event::create()
            ->name($this->getEventTitle($pet->name))
            ->description($this->getEventDescription($interview, $applicant, $pet))
            ->uniqueIdentifier($this->generateUniqueIdentifier($interview))
            ->startsAt($interview->scheduled_at)
            ->endsAt($interview->scheduled_at->copy()->addHour())
            ->organizer(config('mail.from.address'), config('app.name'))
            ->attendee($applicant->email, $applicant->name);

        if ($interview->location) {
            $event->addressName($interview->location);
        }

        return Calendar::create()
            ->event($event)
            ->get();
    }

    /**
     * Get the event title for the interview.
     */
    protected function getEventTitle(string $petName): string
    {
        return "Adoption Interview for {$petName}";
    }

    /**
     * Get the event description with interview details.
     */
    protected function getEventDescription(Interview $interview, $applicant, $pet): string
    {
        $species = $pet->species->name ?? 'Pet';

        $description = "Interview for the adoption of {$pet->name} ({$species}).\n\n";
        $description .= "Applicant: {$applicant->name}\n";
        $description .= "Email: {$applicant->email}\n";

        if ($applicant->phone) {
            $description .= "Phone: {$applicant->phone}\n";
        }

        if ($interview->notes) {
            $description .= "\nNotes:\n{$interview->notes}";
        }

        return $description;
    }

    /**
     * Generate a unique identifier for the interview event.
     */
    protected function generateUniqueIdentifier(Interview $interview): string
    {
        return "interview-{$interview->id}@".parse_url(config('app.url'), PHP_URL_HOST);
    }
}
