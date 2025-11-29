<?php

declare(strict_types=1);

use App\Filament\Resources\AdoptionApplications\Pages\EditAdoptionApplication;
use App\Models\AdoptionApplication;
use App\Models\ApplicationStatusHistory;
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

test('view status history action exists', function () {
    $application = AdoptionApplication::factory()
        ->for($this->admin, 'user')
        ->for($this->pet)
        ->create();

    actingAs($this->admin);

    Livewire::test(EditAdoptionApplication::class, ['record' => $application->id])
        ->assertActionExists('view_status_history');
});

test('view status history action displays modal with status history', function () {
    $user = User::factory()->create();
    $application = AdoptionApplication::factory()
        ->for($user)
        ->for($this->pet)
        ->create();

    ApplicationStatusHistory::create([
        'adoption_application_id' => $application->id,
        'from_status' => null,
        'to_status' => 'pending',
        'notes' => 'Application submitted',
        'changed_by' => $user->id,
    ]);

    ApplicationStatusHistory::create([
        'adoption_application_id' => $application->id,
        'from_status' => 'pending',
        'to_status' => 'approved',
        'notes' => 'Approved by admin',
        'changed_by' => $this->admin->id,
    ]);

    actingAs($this->admin);

    Livewire::test(EditAdoptionApplication::class, ['record' => $application->id])
        ->mountAction('view_status_history')
        ->assertMountedActionModalSee('Status History')
        ->assertMountedActionModalSee('Pending')
        ->assertMountedActionModalSee('Approved')
        ->assertMountedActionModalSee('Application submitted')
        ->assertMountedActionModalSee('Approved by admin');
});
