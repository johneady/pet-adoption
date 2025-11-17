<?php

declare(strict_types=1);

use App\Mail\InterviewRescheduled;
use App\Mail\InterviewRescheduledAdmin;
use App\Mail\InterviewScheduled;
use App\Models\AdoptionApplication;
use App\Models\Interview;
use App\Models\Pet;
use App\Models\Species;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('interview scheduled email includes ics attachment', function () {
    $admin = User::factory()->admin()->create();
    $applicant = User::factory()->create();
    $species = Species::factory()->create();
    $pet = Pet::factory()->create(['species_id' => $species->id]);

    $application = AdoptionApplication::factory()->create([
        'user_id' => $applicant->id,
        'pet_id' => $pet->id,
    ]);

    $interview = Interview::factory()->create([
        'adoption_application_id' => $application->id,
        'scheduled_at' => now()->addDays(3),
        'location' => 'Main Office',
    ]);

    $mailable = new InterviewScheduled($interview, $admin);
    $attachments = $mailable->attachments();

    expect($attachments)->toHaveCount(1)
        ->and($attachments[0]->as)->toBe('interview.ics')
        ->and($attachments[0]->mime)->toBe('text/calendar');
});

test('interview rescheduled email includes ics attachment', function () {
    $admin = User::factory()->admin()->create();
    $applicant = User::factory()->create();
    $species = Species::factory()->create();
    $pet = Pet::factory()->create(['species_id' => $species->id]);

    $application = AdoptionApplication::factory()->create([
        'user_id' => $applicant->id,
        'pet_id' => $pet->id,
    ]);

    $oldScheduledAt = now()->addDays(2);
    $interview = Interview::factory()->create([
        'adoption_application_id' => $application->id,
        'scheduled_at' => now()->addDays(5),
        'location' => 'Main Office',
    ]);

    $mailable = new InterviewRescheduled($interview, $admin, $oldScheduledAt);
    $attachments = $mailable->attachments();

    expect($attachments)->toHaveCount(1)
        ->and($attachments[0]->as)->toBe('interview.ics')
        ->and($attachments[0]->mime)->toBe('text/calendar');
});

test('interview rescheduled admin email includes ics attachment', function () {
    $admin = User::factory()->admin()->create();
    $applicant = User::factory()->create();
    $species = Species::factory()->create();
    $pet = Pet::factory()->create(['species_id' => $species->id]);

    $application = AdoptionApplication::factory()->create([
        'user_id' => $applicant->id,
        'pet_id' => $pet->id,
    ]);

    $oldScheduledAt = now()->addDays(2);
    $interview = Interview::factory()->create([
        'adoption_application_id' => $application->id,
        'scheduled_at' => now()->addDays(5),
        'location' => 'Main Office',
    ]);

    $mailable = new InterviewRescheduledAdmin($interview, $admin, $oldScheduledAt);
    $attachments = $mailable->attachments();

    expect($attachments)->toHaveCount(1)
        ->and($attachments[0]->as)->toBe('interview.ics')
        ->and($attachments[0]->mime)->toBe('text/calendar');
});

test('ics attachment contains valid calendar data', function () {
    $admin = User::factory()->admin()->create();
    $applicant = User::factory()->create(['name' => 'Test Applicant']);
    $species = Species::factory()->create(['name' => 'Dog']);
    $pet = Pet::factory()->create([
        'name' => 'Buddy',
        'species_id' => $species->id,
    ]);

    $application = AdoptionApplication::factory()->create([
        'user_id' => $applicant->id,
        'pet_id' => $pet->id,
    ]);

    $interview = Interview::factory()->create([
        'adoption_application_id' => $application->id,
        'scheduled_at' => now()->addDays(3),
        'location' => 'Main Office',
    ]);

    $service = app(\App\Services\CalendarService::class);
    $icsContent = $service->generateInterviewCalendar($interview);

    expect($icsContent)->toBeString()
        ->and($icsContent)->toContain('BEGIN:VCALENDAR')
        ->and($icsContent)->toContain('END:VCALENDAR')
        ->and($icsContent)->toContain('BEGIN:VEVENT')
        ->and($icsContent)->toContain('END:VEVENT')
        ->and($icsContent)->toContain('SUMMARY:Adoption Interview for Buddy')
        ->and($icsContent)->toContain('Test Applicant');
});

test('ics attachment includes unique identifier for calendar updates', function () {
    $admin = User::factory()->admin()->create();
    $applicant = User::factory()->create();
    $species = Species::factory()->create();
    $pet = Pet::factory()->create(['species_id' => $species->id]);

    $application = AdoptionApplication::factory()->create([
        'user_id' => $applicant->id,
        'pet_id' => $pet->id,
    ]);

    $interview = Interview::factory()->create([
        'adoption_application_id' => $application->id,
        'scheduled_at' => now()->addDays(3),
    ]);

    $service = app(\App\Services\CalendarService::class);
    $icsContent = $service->generateInterviewCalendar($interview);

    expect($icsContent)->toContain("UID:interview-{$interview->id}@");
});
