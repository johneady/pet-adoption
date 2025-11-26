<?php

declare(strict_types=1);

use App\Filament\Resources\Breeds\Pages\ListBreeds;
use App\Models\Breed;
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

test('breed resource table shows all breeds', function () {
    $breeds = Breed::factory()->count(5)->create();

    actingAs($this->admin);

    Livewire::test(ListBreeds::class)
        ->assertCanSeeTableRecords($breeds)
        ->assertCountTableRecords(5);
});

test('breed resource table can search by name', function () {
    $breed = Breed::factory()->create(['name' => 'Golden Retriever']);
    Breed::factory()->create(['name' => 'Labrador']);

    actingAs($this->admin);

    Livewire::test(ListBreeds::class)
        ->searchTable('Golden')
        ->assertCanSeeTableRecords([$breed])
        ->assertCountTableRecords(1);
});

test('breed resource table can search by description', function () {
    $breed = Breed::factory()->create(['description' => 'Friendly and energetic']);
    Breed::factory()->create(['description' => 'Calm and gentle']);

    actingAs($this->admin);

    Livewire::test(ListBreeds::class)
        ->searchTable('energetic')
        ->assertCanSeeTableRecords([$breed])
        ->assertCountTableRecords(1);
});

test('breed resource has create action', function () {
    actingAs($this->admin);

    Livewire::test(ListBreeds::class)
        ->assertActionExists('create');
});

test('breed can be created directly via model', function () {
    $species = Species::factory()->create();

    actingAs($this->admin);

    $breed = Breed::create([
        'species_id' => $species->id,
        'name' => 'Test Breed',
        'slug' => 'test-breed',
    ]);

    expect($breed->species_id)->toBe($species->id)
        ->and($breed->name)->toBe('Test Breed');
});

test('breed validates required fields', function () {
    actingAs($this->admin);

    $exceptionThrown = false;
    try {
        Breed::create([
            'name' => '',
            'slug' => '',
        ]);
    } catch (\Illuminate\Database\QueryException $e) {
        $exceptionThrown = true;
        expect($e->getMessage())->toContain('NOT NULL');
    }

    expect($exceptionThrown)->toBeTrue();
});

test('breed validates unique slug', function () {
    $species = Species::factory()->create();
    Breed::factory()->create(['slug' => 'existing-slug', 'species_id' => $species->id]);

    actingAs($this->admin);

    try {
        Breed::create([
            'species_id' => $species->id,
            'name' => 'New Breed',
            'slug' => 'existing-slug',
        ]);
        $this->fail('Expected exception not thrown');
    } catch (\Illuminate\Database\QueryException $e) {
        expect($e->getMessage())->toContain('UNIQUE');
    }
});

test('breed resource has edit action on table', function () {
    $breed = Breed::factory()->create();

    actingAs($this->admin);

    Livewire::test(ListBreeds::class)
        ->assertTableActionExists('edit');
});

test('breed can be edited', function () {
    $breed = Breed::factory()->create([
        'name' => 'Original Name',
    ]);

    actingAs($this->admin);

    $breed->update(['name' => 'Updated Name']);

    expect($breed->fresh()->name)->toBe('Updated Name');
});

test('breed belongs to species', function () {
    $species = Species::factory()->create(['name' => 'Dog']);
    $breed = Breed::factory()->create(['species_id' => $species->id]);

    expect($breed->species->name)->toBe('Dog');
});

test('breed resource shows correct count of records', function () {
    $species = Species::factory()->create();
    Breed::factory()->count(3)->create(['species_id' => $species->id]);

    actingAs($this->admin);

    Livewire::test(ListBreeds::class)
        ->assertCountTableRecords(3);
});

test('breed can have description', function () {
    $breed = Breed::factory()->create([
        'description' => 'This is a test description',
    ]);

    expect($breed->description)->toBe('This is a test description');
});

test('breed description is optional', function () {
    $breed = Breed::factory()->create([
        'description' => null,
    ]);

    expect($breed->description)->toBeNull();
});
