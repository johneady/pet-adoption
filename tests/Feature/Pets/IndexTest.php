<?php

declare(strict_types=1);

use App\Livewire\Pets\Index;
use App\Models\Breed;
use App\Models\Pet;
use App\Models\Species;
use Livewire\Livewire;

use function Pest\Laravel\get;

test('can visit pets index page', function () {
    $response = get(route('pets.index'));

    $response->assertSuccessful();
    $response->assertSee('Find Your Perfect Companion');
});

test('displays available pets', function () {
    $species = Species::factory()->create(['name' => 'Dog']);
    $pets = Pet::factory()->count(3)->create([
        'species_id' => $species->id,
        'status' => 'available',
    ]);

    Livewire::test(Index::class)
        ->assertSee($pets[0]->name)
        ->assertSee($pets[1]->name)
        ->assertSee($pets[2]->name);
});

test('does not display non-available pets', function () {
    $species = Species::factory()->create(['name' => 'Dog']);
    $availablePet = Pet::factory()->create([
        'species_id' => $species->id,
        'status' => 'available',
        'name' => 'Available Pet',
    ]);
    $adoptedPet = Pet::factory()->create([
        'species_id' => $species->id,
        'status' => 'adopted',
        'name' => 'Adopted Pet',
    ]);

    Livewire::test(Index::class)
        ->assertSee('Available Pet')
        ->assertDontSee('Adopted Pet');
});

test('can search pets by name', function () {
    $species = Species::factory()->create(['name' => 'Dog']);
    $pet1 = Pet::factory()->create([
        'species_id' => $species->id,
        'name' => 'BuddySearchTest',
        'status' => 'available',
    ]);
    $pet2 = Pet::factory()->create([
        'species_id' => $species->id,
        'name' => 'MaxSearchTest',
        'status' => 'available',
    ]);

    Livewire::test(Index::class)
        ->set('search', 'BuddySearchTest')
        ->assertSee('BuddySearchTest')
        ->assertDontSee('MaxSearchTest');
});

test('can filter pets by species', function () {
    $dogSpecies = Species::factory()->create(['name' => 'Dog']);
    $catSpecies = Species::factory()->create(['name' => 'Cat']);

    $dog = Pet::factory()->create([
        'species_id' => $dogSpecies->id,
        'name' => 'Buddy',
        'status' => 'available',
    ]);
    $cat = Pet::factory()->create([
        'species_id' => $catSpecies->id,
        'name' => 'Whiskers',
        'status' => 'available',
    ]);

    Livewire::test(Index::class)
        ->set('speciesId', $dogSpecies->id)
        ->assertSee('Buddy')
        ->assertDontSee('Whiskers');
});

test('can filter pets by breed', function () {
    $species = Species::factory()->create(['name' => 'Dog']);
    $breed1 = Breed::factory()->create(['species_id' => $species->id, 'name' => 'Labrador']);
    $breed2 = Breed::factory()->create(['species_id' => $species->id, 'name' => 'Poodle']);

    $labrador = Pet::factory()->create([
        'species_id' => $species->id,
        'breed_id' => $breed1->id,
        'name' => 'BuddyBreedTest',
        'status' => 'available',
    ]);
    $poodle = Pet::factory()->create([
        'species_id' => $species->id,
        'breed_id' => $breed2->id,
        'name' => 'MaxBreedTest',
        'status' => 'available',
    ]);

    Livewire::test(Index::class)
        ->set('speciesId', $species->id)
        ->set('breedId', $breed1->id)
        ->assertSee('BuddyBreedTest')
        ->assertDontSee('MaxBreedTest');
});

test('can filter pets by gender', function () {
    $species = Species::factory()->create(['name' => 'Dog']);
    $malePet = Pet::factory()->create([
        'species_id' => $species->id,
        'gender' => 'male',
        'name' => 'Buddy',
        'status' => 'available',
    ]);
    $femalePet = Pet::factory()->create([
        'species_id' => $species->id,
        'gender' => 'female',
        'name' => 'Bella',
        'status' => 'available',
    ]);

    Livewire::test(Index::class)
        ->set('gender', 'male')
        ->assertSee('Buddy')
        ->assertDontSee('Bella');
});

test('can filter pets by size', function () {
    $species = Species::factory()->create(['name' => 'Dog']);
    $smallPet = Pet::factory()->create([
        'species_id' => $species->id,
        'size' => 'small',
        'name' => 'Tiny',
        'status' => 'available',
    ]);
    $largePet = Pet::factory()->create([
        'species_id' => $species->id,
        'size' => 'large',
        'name' => 'Big Boy',
        'status' => 'available',
    ]);

    Livewire::test(Index::class)
        ->set('size', 'small')
        ->assertSee('Tiny')
        ->assertDontSee('Big Boy');
});

test('can filter pets by age range', function () {
    $species = Species::factory()->create(['name' => 'Dog']);
    $youngPet = Pet::factory()->create([
        'species_id' => $species->id,
        'age' => 2,
        'name' => 'Puppy',
        'status' => 'available',
    ]);
    $oldPet = Pet::factory()->create([
        'species_id' => $species->id,
        'age' => 10,
        'name' => 'Old Boy',
        'status' => 'available',
    ]);

    Livewire::test(Index::class)
        ->set('minAge', 1)
        ->set('maxAge', 5)
        ->assertSee('Puppy')
        ->assertDontSee('Old Boy');
});

test('can clear all filters', function () {
    $species = Species::factory()->create(['name' => 'Dog']);
    Pet::factory()->create([
        'species_id' => $species->id,
        'name' => 'Buddy',
        'status' => 'available',
    ]);

    Livewire::test(Index::class)
        ->set('search', 'test')
        ->set('speciesId', $species->id)
        ->set('gender', 'male')
        ->call('clearFilters')
        ->assertSet('search', '')
        ->assertSet('speciesId', null)
        ->assertSet('gender', null);
});

test('breed filter resets when species changes', function () {
    $species1 = Species::factory()->create(['name' => 'Dog']);
    $species2 = Species::factory()->create(['name' => 'Cat']);
    $breed = Breed::factory()->create(['species_id' => $species1->id]);

    Livewire::test(Index::class)
        ->set('speciesId', $species1->id)
        ->set('breedId', $breed->id)
        ->set('speciesId', $species2->id)
        ->assertSet('breedId', null);
});

test('resets page when search changes', function () {
    $species = Species::factory()->create(['name' => 'Dog']);
    $pet = Pet::factory()->create([
        'species_id' => $species->id,
        'name' => 'SearchablePet',
        'status' => 'available',
    ]);

    Livewire::test(Index::class)
        ->set('search', 'SearchablePet')
        ->assertSee('SearchablePet');
});
