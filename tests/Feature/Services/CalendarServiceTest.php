<?php

declare(strict_types=1);

use App\Models\AdoptionApplication;
use App\Models\Interview;
use App\Models\Pet;
use App\Models\Species;
use App\Models\User;
use App\Services\CalendarService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('generates valid icalendar content', function () {
    $service = new CalendarService;

    $user = User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    $species = Species::factory()->create(['name' => 'Dog']);
    $pet = Pet::factory()->create([
        'name' => 'Buddy',
        'species_id' => $species->id,
    ]);

    $application = AdoptionApplication::factory()->create([
        'user_id' => $user->id,
        'pet_id' => $pet->id,
    ]);

    $interview = Interview::factory()->create([
        'adoption_application_id' => $application->id,
        'scheduled_at' => now()->addDays(3),
        'location' => '123 Main St',
    ]);

    $icsContent = $service->generateInterviewCalendar($interview);

    expect($icsContent)->toBeString()
        ->and($icsContent)->toContain('BEGIN:VCALENDAR')
        ->and($icsContent)->toContain('END:VCALENDAR')
        ->and($icsContent)->toContain('BEGIN:VEVENT')
        ->and($icsContent)->toContain('END:VEVENT');
});

test('includes event title with pet name', function () {
    $service = new CalendarService;

    $user = User::factory()->create();
    $species = Species::factory()->create(['name' => 'Cat']);
    $pet = Pet::factory()->create([
        'name' => 'Fluffy',
        'species_id' => $species->id,
    ]);

    $application = AdoptionApplication::factory()->create([
        'user_id' => $user->id,
        'pet_id' => $pet->id,
    ]);

    $interview = Interview::factory()->create([
        'adoption_application_id' => $application->id,
        'scheduled_at' => now()->addDays(5),
    ]);

    $icsContent = $service->generateInterviewCalendar($interview);

    expect($icsContent)->toContain('SUMMARY:Adoption Interview for Fluffy');
});

test('includes event description with applicant details', function () {
    $service = new CalendarService;

    $user = User::factory()->create([
        'name' => 'Jane Smith',
        'email' => 'jane@example.com',
        'phone' => '555-1234',
    ]);

    $species = Species::factory()->create(['name' => 'Dog']);
    $pet = Pet::factory()->create([
        'name' => 'Max',
        'species_id' => $species->id,
    ]);

    $application = AdoptionApplication::factory()->create([
        'user_id' => $user->id,
        'pet_id' => $pet->id,
    ]);

    $interview = Interview::factory()->create([
        'adoption_application_id' => $application->id,
        'scheduled_at' => now()->addDays(2),
        'notes' => 'Bring vaccination records',
    ]);

    $icsContent = $service->generateInterviewCalendar($interview);

    expect($icsContent)->toContain('DESCRIPTION')
        ->and($icsContent)->toContain('Jane Smith')
        ->and($icsContent)->toContain('jane@example.com')
        ->and($icsContent)->toContain('555-1234');
});

test('generates calendar with location when provided', function () {
    $service = new CalendarService;

    $user = User::factory()->create();
    $species = Species::factory()->create();
    $pet = Pet::factory()->create(['species_id' => $species->id]);

    $application = AdoptionApplication::factory()->create([
        'user_id' => $user->id,
        'pet_id' => $pet->id,
    ]);

    $interview = Interview::factory()->create([
        'adoption_application_id' => $application->id,
        'scheduled_at' => now()->addDays(1),
        'location' => '456 Elm Street, Suite 10',
    ]);

    $icsContent = $service->generateInterviewCalendar($interview);

    expect($icsContent)->toBeString()
        ->and($icsContent)->toContain('BEGIN:VEVENT')
        ->and($icsContent)->toContain('END:VEVENT');
});

test('handles missing location gracefully', function () {
    $service = new CalendarService;

    $user = User::factory()->create();
    $species = Species::factory()->create();
    $pet = Pet::factory()->create(['species_id' => $species->id]);

    $application = AdoptionApplication::factory()->create([
        'user_id' => $user->id,
        'pet_id' => $pet->id,
    ]);

    $interview = Interview::factory()->create([
        'adoption_application_id' => $application->id,
        'scheduled_at' => now()->addDays(1),
        'location' => null,
    ]);

    $icsContent = $service->generateInterviewCalendar($interview);

    expect($icsContent)->toBeString()
        ->and($icsContent)->toContain('BEGIN:VEVENT');
});

test('includes unique identifier with interview id', function () {
    $service = new CalendarService;

    $user = User::factory()->create();
    $species = Species::factory()->create();
    $pet = Pet::factory()->create(['species_id' => $species->id]);

    $application = AdoptionApplication::factory()->create([
        'user_id' => $user->id,
        'pet_id' => $pet->id,
    ]);

    $interview = Interview::factory()->create([
        'adoption_application_id' => $application->id,
        'scheduled_at' => now()->addDays(1),
    ]);

    $icsContent = $service->generateInterviewCalendar($interview);

    expect($icsContent)->toContain("UID:interview-{$interview->id}@");
});

test('includes organizer and attendee information', function () {
    $service = new CalendarService;

    $user = User::factory()->create([
        'name' => 'Test User',
        'email' => 'testuser@example.com',
    ]);

    $species = Species::factory()->create();
    $pet = Pet::factory()->create(['species_id' => $species->id]);

    $application = AdoptionApplication::factory()->create([
        'user_id' => $user->id,
        'pet_id' => $pet->id,
    ]);

    $interview = Interview::factory()->create([
        'adoption_application_id' => $application->id,
        'scheduled_at' => now()->addDays(1),
    ]);

    $icsContent = $service->generateInterviewCalendar($interview);

    expect($icsContent)->toContain('ORGANIZER')
        ->and($icsContent)->toContain('ATTENDEE')
        ->and($icsContent)->toContain('testuser@example.com');
});

test('sets event duration to one hour', function () {
    $service = new CalendarService;

    $user = User::factory()->create();
    $species = Species::factory()->create();
    $pet = Pet::factory()->create(['species_id' => $species->id]);

    $application = AdoptionApplication::factory()->create([
        'user_id' => $user->id,
        'pet_id' => $pet->id,
    ]);

    $scheduledAt = now()->addDays(1)->setTime(14, 0, 0);
    $interview = Interview::factory()->create([
        'adoption_application_id' => $application->id,
        'scheduled_at' => $scheduledAt,
    ]);

    $icsContent = $service->generateInterviewCalendar($interview);

    expect($icsContent)->toContain('DTSTART')
        ->and($icsContent)->toContain('DTEND');
});
