<?php

declare(strict_types=1);

use App\Filament\Resources\AdoptionApplications\Pages\EditAdoptionApplication;
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

test('archive action is visible for approved applications', function () {
    $application = AdoptionApplication::factory()
        ->for($this->admin, 'user')
        ->for($this->pet)
        ->create(['status' => 'approved']);

    actingAs($this->admin);

    Livewire::test(EditAdoptionApplication::class, ['record' => $application->id])
        ->assertActionVisible('archive');
});

test('archive action is visible for rejected applications', function () {
    $application = AdoptionApplication::factory()
        ->for($this->admin, 'user')
        ->for($this->pet)
        ->create(['status' => 'rejected']);

    actingAs($this->admin);

    Livewire::test(EditAdoptionApplication::class, ['record' => $application->id])
        ->assertActionVisible('archive');
});

test('archive action is not visible for submitted applications', function () {
    $application = AdoptionApplication::factory()
        ->for($this->admin, 'user')
        ->for($this->pet)
        ->create(['status' => 'submitted']);

    actingAs($this->admin);

    Livewire::test(EditAdoptionApplication::class, ['record' => $application->id])
        ->assertActionHidden('archive');
});

test('archive action is not visible for under review applications', function () {
    $application = AdoptionApplication::factory()
        ->for($this->admin, 'user')
        ->for($this->pet)
        ->create(['status' => 'under_review']);

    actingAs($this->admin);

    Livewire::test(EditAdoptionApplication::class, ['record' => $application->id])
        ->assertActionHidden('archive');
});

test('archive action is not visible for interview scheduled applications', function () {
    $application = AdoptionApplication::factory()
        ->for($this->admin, 'user')
        ->for($this->pet)
        ->create(['status' => 'interview_scheduled']);

    actingAs($this->admin);

    Livewire::test(EditAdoptionApplication::class, ['record' => $application->id])
        ->assertActionHidden('archive');
});

test('archive action is not visible for already archived applications', function () {
    $application = AdoptionApplication::factory()
        ->for($this->admin, 'user')
        ->for($this->pet)
        ->create(['status' => 'archived']);

    actingAs($this->admin);

    Livewire::test(EditAdoptionApplication::class, ['record' => $application->id])
        ->assertActionHidden('archive');
});

test('archive action updates application status to archived', function () {
    $application = AdoptionApplication::factory()
        ->for($this->admin, 'user')
        ->for($this->pet)
        ->create(['status' => 'approved']);

    actingAs($this->admin);

    Livewire::test(EditAdoptionApplication::class, ['record' => $application->id])
        ->callAction('archive');

    expect($application->refresh()->status)->toBe('archived');
});

test('application details are read-only on edit page', function () {
    $application = AdoptionApplication::factory()
        ->for($this->admin, 'user')
        ->for($this->pet)
        ->create(['status' => 'submitted']);

    actingAs($this->admin);

    Livewire::test(EditAdoptionApplication::class, ['record' => $application->id])
        ->assertFormFieldIsDisabled('living_situation')
        ->assertFormFieldIsDisabled('employment_status')
        ->assertFormFieldIsDisabled('veterinary_reference')
        ->assertFormFieldIsDisabled('experience')
        ->assertFormFieldIsDisabled('other_pets')
        ->assertFormFieldIsDisabled('household_members')
        ->assertFormFieldIsDisabled('reason_for_adoption');
});

test('interview details are read-only on edit page', function () {
    $application = AdoptionApplication::factory()
        ->for($this->admin, 'user')
        ->for($this->pet)
        ->create(['status' => 'interview_scheduled']);

    $interview = \App\Models\Interview::factory()->create([
        'adoption_application_id' => $application->id,
        'scheduled_at' => now()->addDays(7),
        'location' => 'Test Location',
        'notes' => 'Test notes',
    ]);

    actingAs($this->admin);

    Livewire::test(EditAdoptionApplication::class, ['record' => $application->id])
        ->assertFormFieldIsDisabled('interview.scheduled_at')
        ->assertFormFieldIsDisabled('interview.location')
        ->assertFormFieldIsDisabled('interview.notes')
        ->assertFormFieldIsDisabled('interview.completed_at');
});
