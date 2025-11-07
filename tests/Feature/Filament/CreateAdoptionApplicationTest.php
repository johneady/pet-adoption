<?php

declare(strict_types=1);

use App\Filament\Resources\AdoptionApplications\Pages\CreateAdoptionApplication;
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
    $this->user = User::factory()->create();
    $this->pet = Pet::factory()->create(['status' => 'available']);
});

test('creates adoption application with default submitted status when status not provided', function () {
    actingAs($this->admin);

    $application = AdoptionApplication::create([
        'user_id' => $this->user->id,
        'pet_id' => $this->pet->id,
        'living_situation' => 'House with yard',
        'reason_for_adoption' => 'I love pets and have a great home for them.',
    ]);

    expect($application->status)->toBe('submitted');
});

test('status field is not visible when creating new application', function () {
    actingAs($this->admin);

    Livewire::test(CreateAdoptionApplication::class)
        ->assertFormFieldIsHidden('status');
});

test('status field is visible when editing existing application', function () {
    actingAs($this->admin);

    $application = AdoptionApplication::factory()
        ->for($this->user)
        ->for($this->pet)
        ->create(['status' => 'submitted']);

    Livewire::test(EditAdoptionApplication::class, ['record' => $application->id])
        ->assertFormFieldIsVisible('status');
});

test('creates status history entry when application is created via form', function () {
    actingAs($this->admin);

    Livewire::test(CreateAdoptionApplication::class)
        ->set('data.user_id', $this->user->id)
        ->set('data.pet_id', $this->pet->id)
        ->set('data.living_situation', 'House with yard')
        ->set('data.reason_for_adoption', 'I love pets and have a great home for them.')
        ->call('create')
        ->assertHasNoFormErrors();

    $application = AdoptionApplication::latest()->first();

    expect($application)->not->toBeNull()
        ->and($application->status)->toBe('submitted');

    // Verify status history entry was created
    $history = ApplicationStatusHistory::where('adoption_application_id', $application->id)->first();

    expect($history)->not->toBeNull()
        ->and($history->from_status)->toBeNull()
        ->and($history->to_status)->toBe('submitted')
        ->and($history->notes)->toBe('Application submitted')
        ->and($history->changed_by)->toBe($this->admin->id);
});
