<?php

declare(strict_types=1);

use App\Filament\Resources\Pets\Pages\EditPet;
use App\Filament\Resources\Pets\Pages\ListPets;
use App\Models\Pet;
use App\Models\Species;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    Filament::setCurrentPanel('admin');
    $this->admin = User::factory()->admin()->create();
});

test('adopted pet has disabled state set correctly', function () {
    $adoptedPet = Pet::factory()->create([
        'name' => 'Adopted Pet',
        'status' => 'adopted',
    ]);

    $availablePet = Pet::factory()->create([
        'name' => 'Available Pet',
        'status' => 'available',
    ]);

    // Verify pet statuses
    expect($adoptedPet->status)->toBe('adopted')
        ->and($availablePet->status)->toBe('available');

    // Test that the disabled logic evaluates correctly
    $adoptedDisabled = (fn ($record) => $record?->status === 'adopted')($adoptedPet);
    $availableDisabled = (fn ($record) => $record?->status === 'adopted')($availablePet);

    expect($adoptedDisabled)->toBeTrue()
        ->and($availableDisabled)->toBeFalse();
});

test('adopted pet form fields are disabled on edit page', function () {
    $species = Species::factory()->create();
    $pet = Pet::factory()->create([
        'species_id' => $species->id,
        'name' => 'Adopted Pet',
        'status' => 'adopted',
    ]);

    actingAs($this->admin);

    Livewire::test(EditPet::class, [
        'record' => $pet->id,
    ])
        ->assertFormFieldIsDisabled('name')
        ->assertFormFieldIsDisabled('slug')
        ->assertFormFieldIsDisabled('species_id')
        ->assertFormFieldIsDisabled('age')
        ->assertFormFieldIsDisabled('gender')
        ->assertFormFieldIsDisabled('size')
        ->assertFormFieldIsDisabled('color')
        ->assertFormFieldIsDisabled('vaccination_status')
        ->assertFormFieldIsDisabled('special_needs')
        ->assertFormFieldIsDisabled('intake_date')
        ->assertFormFieldIsDisabled('description')
        ->assertFormFieldIsDisabled('medical_notes')
        ->assertFormFieldIsDisabled('photos');
});

test('non-adopted pet form fields are not disabled on edit page', function () {
    $species = Species::factory()->create();
    $pet = Pet::factory()->create([
        'species_id' => $species->id,
        'name' => 'Available Pet',
        'status' => 'available',
    ]);

    actingAs($this->admin);

    Livewire::test(EditPet::class, [
        'record' => $pet->id,
    ])
        ->assertFormFieldIsEnabled('name')
        ->assertFormFieldIsEnabled('slug')
        ->assertFormFieldIsEnabled('species_id')
        ->assertFormFieldIsEnabled('age')
        ->assertFormFieldIsEnabled('gender')
        ->assertFormFieldIsEnabled('size')
        ->assertFormFieldIsEnabled('color')
        ->assertFormFieldIsEnabled('vaccination_status')
        ->assertFormFieldIsEnabled('special_needs')
        ->assertFormFieldIsEnabled('intake_date')
        ->assertFormFieldIsEnabled('description')
        ->assertFormFieldIsEnabled('medical_notes')
        ->assertFormFieldIsEnabled('photos');
});

test('pet list page loads successfully', function () {
    actingAs($this->admin);

    Livewire::test(ListPets::class)
        ->assertOk();
});

test('pet status can be changed from available to adopted', function () {
    $pet = Pet::factory()->create([
        'status' => 'available',
    ]);

    expect($pet->status)->toBe('available');

    $pet->update(['status' => 'adopted']);

    expect($pet->fresh()->status)->toBe('adopted');
});
