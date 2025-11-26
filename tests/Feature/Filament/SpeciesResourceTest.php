<?php

declare(strict_types=1);

use App\Filament\Resources\Species\Pages\ListSpecies;
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

test('species resource table shows all species', function () {
    $species = Species::factory()->count(5)->create();

    actingAs($this->admin);

    Livewire::test(ListSpecies::class)
        ->assertCanSeeTableRecords($species)
        ->assertCountTableRecords(5);
});

test('species resource table can search by name', function () {
    $species = Species::factory()->create(['name' => 'Dog']);
    Species::factory()->create(['name' => 'Cat']);

    actingAs($this->admin);

    Livewire::test(ListSpecies::class)
        ->searchTable('Dog')
        ->assertCanSeeTableRecords([$species])
        ->assertCountTableRecords(1);
});

test('species resource table can search by description', function () {
    $species = Species::factory()->create(['description' => 'A loyal companion']);
    Species::factory()->create(['description' => 'Independent feline']);

    actingAs($this->admin);

    Livewire::test(ListSpecies::class)
        ->searchTable('loyal')
        ->assertCanSeeTableRecords([$species])
        ->assertCountTableRecords(1);
});

test('species resource has create action', function () {
    actingAs($this->admin);

    Livewire::test(ListSpecies::class)
        ->assertActionExists('create');
});

test('species can be created directly via model', function () {
    actingAs($this->admin);

    $species = Species::create([
        'name' => 'Test Species',
        'slug' => 'test-species',
    ]);

    expect($species->name)->toBe('Test Species');
});

test('species validates unique slug', function () {
    Species::factory()->create(['slug' => 'existing-slug']);

    actingAs($this->admin);

    try {
        Species::create([
            'name' => 'New Species',
            'slug' => 'existing-slug',
        ]);
        $this->fail('Expected exception not thrown');
    } catch (\Illuminate\Database\QueryException $e) {
        expect($e->getMessage())->toContain('UNIQUE');
    }
});

test('species resource has edit action on table', function () {
    $species = Species::factory()->create();

    actingAs($this->admin);

    Livewire::test(ListSpecies::class)
        ->assertTableActionExists('edit');
});

test('species can be edited', function () {
    $species = Species::factory()->create([
        'name' => 'Original Name',
    ]);

    actingAs($this->admin);

    $species->update(['name' => 'Updated Name']);

    expect($species->fresh()->name)->toBe('Updated Name');
});

test('species resource shows correct count of records', function () {
    Species::factory()->count(3)->create();

    actingAs($this->admin);

    Livewire::test(ListSpecies::class)
        ->assertCountTableRecords(3);
});

test('species can have description', function () {
    $species = Species::factory()->create([
        'description' => 'This is a test description',
    ]);

    expect($species->description)->toBe('This is a test description');
});

test('species description is optional', function () {
    $species = Species::factory()->create([
        'description' => null,
    ]);

    expect($species->description)->toBeNull();
});

test('species has many breeds', function () {
    $species = Species::factory()->create();
    \App\Models\Breed::factory()->count(3)->create(['species_id' => $species->id]);

    expect($species->breeds()->count())->toBe(3);
});
