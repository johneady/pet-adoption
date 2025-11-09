<?php

declare(strict_types=1);

use App\Filament\Resources\Pets\Pages\ListPets;
use App\Models\Breed;
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
    $this->species = Species::factory()->create();
    $this->breed = Breed::factory()->for($this->species)->create();
});

test('list pets page excludes adopted pets by default', function () {
    $availablePet = Pet::factory()->for($this->species)->for($this->breed)->create(['status' => 'available']);
    $pendingPet = Pet::factory()->for($this->species)->for($this->breed)->create(['status' => 'pending']);
    $comingSoonPet = Pet::factory()->for($this->species)->for($this->breed)->create(['status' => 'coming_soon']);
    $adoptedPet = Pet::factory()->for($this->species)->for($this->breed)->create(['status' => 'adopted']);

    actingAs($this->admin);

    Livewire::test(ListPets::class)
        ->assertCanSeeTableRecords([$availablePet, $pendingPet, $comingSoonPet])
        ->assertCanNotSeeTableRecords([$adoptedPet])
        ->assertCountTableRecords(3);
});

test('list pets page shows adopted pets when status filter is set to adopted', function () {
    $availablePet = Pet::factory()->for($this->species)->for($this->breed)->create(['status' => 'available']);
    $adoptedPet = Pet::factory()->for($this->species)->for($this->breed)->create(['status' => 'adopted']);

    actingAs($this->admin);

    Livewire::test(ListPets::class)
        ->filterTable('status', 'adopted')
        ->assertCanSeeTableRecords([$adoptedPet])
        ->assertCanNotSeeTableRecords([$availablePet])
        ->assertCountTableRecords(1);
});

test('list pets page shows only available pets when status filter is set to available', function () {
    $availablePet = Pet::factory()->for($this->species)->for($this->breed)->create(['status' => 'available']);
    $pendingPet = Pet::factory()->for($this->species)->for($this->breed)->create(['status' => 'pending']);
    $adoptedPet = Pet::factory()->for($this->species)->for($this->breed)->create(['status' => 'adopted']);

    actingAs($this->admin);

    Livewire::test(ListPets::class)
        ->filterTable('status', 'available')
        ->assertCanSeeTableRecords([$availablePet])
        ->assertCanNotSeeTableRecords([$pendingPet, $adoptedPet])
        ->assertCountTableRecords(1);
});

test('list pets page shows all pets including adopted when status filter is cleared', function () {
    $availablePet = Pet::factory()->for($this->species)->for($this->breed)->create(['status' => 'available']);
    $adoptedPet = Pet::factory()->for($this->species)->for($this->breed)->create(['status' => 'adopted']);

    actingAs($this->admin);

    Livewire::test(ListPets::class)
        ->filterTable('status', 'adopted')
        ->assertCanSeeTableRecords([$adoptedPet])
        ->assertCanNotSeeTableRecords([$availablePet])
        ->filterTable('status', null)
        ->assertCanSeeTableRecords([$availablePet])
        ->assertCanNotSeeTableRecords([$adoptedPet])
        ->assertCountTableRecords(1);
});
