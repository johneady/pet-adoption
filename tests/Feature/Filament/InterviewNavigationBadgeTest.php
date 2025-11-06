<?php

declare(strict_types=1);

use App\Filament\Resources\Interviews\InterviewResource;
use App\Models\AdoptionApplication;
use App\Models\Interview;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    Filament::setCurrentPanel('admin');
    $this->admin = User::factory()->admin()->create();
});

test('navigation badge shows count of overdue interviews', function () {
    $application1 = AdoptionApplication::factory()->create();
    $application2 = AdoptionApplication::factory()->create();
    $application3 = AdoptionApplication::factory()->create();

    // Create overdue interviews (scheduled in the past, not completed)
    Interview::create([
        'adoption_application_id' => $application1->id,
        'scheduled_at' => now()->subDays(2),
        'location' => 'Office A',
        'completed_at' => null,
    ]);

    Interview::create([
        'adoption_application_id' => $application2->id,
        'scheduled_at' => now()->subHours(5),
        'location' => 'Office B',
        'completed_at' => null,
    ]);

    // Create future interview (not overdue)
    Interview::create([
        'adoption_application_id' => $application3->id,
        'scheduled_at' => now()->addDays(2),
        'location' => 'Office C',
        'completed_at' => null,
    ]);

    expect(InterviewResource::getNavigationBadge())->toBe('2');
});

test('navigation badge returns null when no overdue interviews', function () {
    $application1 = AdoptionApplication::factory()->create();
    $application2 = AdoptionApplication::factory()->create();

    // Create future interviews (not overdue)
    Interview::create([
        'adoption_application_id' => $application1->id,
        'scheduled_at' => now()->addDays(2),
        'location' => 'Office A',
        'completed_at' => null,
    ]);

    Interview::create([
        'adoption_application_id' => $application2->id,
        'scheduled_at' => now()->addDays(5),
        'location' => 'Office B',
        'completed_at' => null,
    ]);

    expect(InterviewResource::getNavigationBadge())->toBeNull();
});

test('navigation badge does not count completed interviews', function () {
    $application1 = AdoptionApplication::factory()->create();
    $application2 = AdoptionApplication::factory()->create();

    // Create overdue but completed interview
    Interview::create([
        'adoption_application_id' => $application1->id,
        'scheduled_at' => now()->subDays(2),
        'location' => 'Office A',
        'completed_at' => now(),
    ]);

    // Create overdue non-completed interview
    Interview::create([
        'adoption_application_id' => $application2->id,
        'scheduled_at' => now()->subDays(1),
        'location' => 'Office B',
        'completed_at' => null,
    ]);

    expect(InterviewResource::getNavigationBadge())->toBe('1');
});

test('navigation badge color is danger', function () {
    expect(InterviewResource::getNavigationBadgeColor())->toBe('danger');
});
