<?php

declare(strict_types=1);

use App\Filament\Resources\AdoptionApplications\Pages\ListAdoptionApplications;
use App\Models\AdoptionApplication;
use App\Models\Pet;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    Filament::setCurrentPanel('admin');
    $this->admin = User::factory()->admin()->create();
    $this->pet = Pet::factory()->create();
});

test('adoption applications list excludes archived by default', function () {
    // Create applications with different statuses
    $submitted = AdoptionApplication::factory()->for($this->admin, 'user')->for($this->pet)->create(['status' => 'submitted']);
    $underReview = AdoptionApplication::factory()->for($this->admin, 'user')->for($this->pet)->create(['status' => 'under_review']);
    $interviewScheduled = AdoptionApplication::factory()->for($this->admin, 'user')->for($this->pet)->create(['status' => 'interview_scheduled']);
    $approved = AdoptionApplication::factory()->for($this->admin, 'user')->for($this->pet)->create(['status' => 'approved']);
    $rejected = AdoptionApplication::factory()->for($this->admin, 'user')->for($this->pet)->create(['status' => 'rejected']);
    $archived = AdoptionApplication::factory()->for($this->admin, 'user')->for($this->pet)->create(['status' => 'archived']);

    actingAs($this->admin);

    Livewire::test(ListAdoptionApplications::class)
        ->filterTable('status', ['submitted', 'under_review', 'interview_scheduled', 'approved', 'rejected'])
        ->assertCanSeeTableRecords([$submitted, $underReview, $interviewScheduled, $approved, $rejected])
        ->assertCanNotSeeTableRecords([$archived])
        ->assertCountTableRecords(5);
});

test('adoption applications list can show archived when filter is changed', function () {
    $submitted = AdoptionApplication::factory()->for($this->pet)->create(['status' => 'submitted']);
    $archived = AdoptionApplication::factory()->for($this->pet)->create(['status' => 'archived']);

    actingAs($this->admin);

    Livewire::test(ListAdoptionApplications::class)
        ->filterTable('status', ['archived'])
        ->assertCanSeeTableRecords([$archived])
        ->assertCanNotSeeTableRecords([$submitted])
        ->assertCountTableRecords(1);
});

test('adoption applications list can show all statuses including archived', function () {
    $submitted = AdoptionApplication::factory()->for($this->pet)->create(['status' => 'submitted']);
    $archived = AdoptionApplication::factory()->for($this->pet)->create(['status' => 'archived']);

    actingAs($this->admin);

    Livewire::test(ListAdoptionApplications::class)
        ->filterTable('status', ['submitted', 'archived'])
        ->assertCanSeeTableRecords([$submitted, $archived])
        ->assertCountTableRecords(2);
});

test('can create adoption application for available pets', function () {
    $species = \App\Models\Species::factory()->create(['name' => 'Test Species']);
    $breed = \App\Models\Breed::factory()->create(['species_id' => $species->id]);
    $user = User::factory()->create();

    $availablePet = Pet::factory()->create([
        'species_id' => $species->id,
        'breed_id' => $breed->id,
        'status' => 'available',
        'name' => 'Available Pet',
    ]);
    Pet::factory()->create([
        'species_id' => $species->id,
        'breed_id' => $breed->id,
        'status' => 'pending',
        'name' => 'Pending Pet',
    ]);
    Pet::factory()->create([
        'species_id' => $species->id,
        'breed_id' => $breed->id,
        'status' => 'adopted',
        'name' => 'Adopted Pet',
    ]);
    Pet::factory()->create([
        'species_id' => $species->id,
        'breed_id' => $breed->id,
        'status' => 'coming_soon',
        'name' => 'Coming Soon Pet',
    ]);

    actingAs($this->admin);

    $application = AdoptionApplication::create([
        'user_id' => $user->id,
        'pet_id' => $availablePet->id,
        'living_situation' => 'House with yard',
        'reason_for_adoption' => 'I love pets',
    ]);

    expect($application)->not->toBeNull()
        ->and($application->pet_id)->toBe($availablePet->id)
        ->and($application->user_id)->toBe($user->id)
        ->and($application->status)->toBe('submitted');
});
