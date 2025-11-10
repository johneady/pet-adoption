<?php

declare(strict_types=1);

use App\Livewire\Pets\Show;
use App\Models\Pet;
use App\Models\PetPhoto;
use App\Models\Species;
use Livewire\Livewire;

use function Pest\Laravel\get;

test('can visit pet detail page', function () {
    $species = Species::factory()->create(['name' => 'Dog']);
    $pet = Pet::factory()->create([
        'species_id' => $species->id,
        'slug' => 'buddy-the-dog',
        'name' => 'Buddy',
    ]);

    $response = get(route('pets.show', $pet->slug));

    $response->assertSuccessful();
    $response->assertSee('Buddy');
    $response->assertSee('Ready to Adopt');
});

test('displays pet information', function () {
    $species = Species::factory()->create(['name' => 'Dog']);
    $pet = Pet::factory()->create([
        'species_id' => $species->id,
        'name' => 'Buddy',
        'age' => 3,
        'gender' => 'male',
        'size' => 'large',
        'color' => 'Brown',
        'description' => 'A friendly dog',
        'slug' => 'buddy-the-dog',
    ]);

    Livewire::test(Show::class, ['slug' => $pet->slug])
        ->assertSee('Buddy')
        ->assertSee('3 years')
        ->assertSee('Male')
        ->assertSee('Large')
        ->assertSee('Brown')
        ->assertSee('A friendly dog');
});

test('displays vaccination status', function () {
    $species = Species::factory()->create(['name' => 'Dog']);
    $pet = Pet::factory()->create([
        'species_id' => $species->id,
        'vaccination_status' => true,
        'slug' => 'buddy-the-dog',
    ]);

    Livewire::test(Show::class, ['slug' => $pet->slug])
        ->assertSee('Up to date');
});

test('displays special needs status', function () {
    $species = Species::factory()->create(['name' => 'Dog']);
    $petWithNeeds = Pet::factory()->create([
        'species_id' => $species->id,
        'special_needs' => true,
        'slug' => 'special-pet',
    ]);

    Livewire::test(Show::class, ['slug' => $petWithNeeds->slug])
        ->assertSee('Yes');
});

test('displays medical notes when present', function () {
    $species = Species::factory()->create(['name' => 'Dog']);
    $pet = Pet::factory()->create([
        'species_id' => $species->id,
        'medical_notes' => 'Requires daily medication',
        'slug' => 'buddy-the-dog',
    ]);

    Livewire::test(Show::class, ['slug' => $pet->slug])
        ->assertSee('Medical Notes')
        ->assertSee('Requires daily medication');
});

test('can navigate through photo gallery', function () {
    $species = Species::factory()->create(['name' => 'Dog']);
    $pet = Pet::factory()->create([
        'species_id' => $species->id,
        'slug' => 'buddy-the-dog',
    ]);

    PetPhoto::factory()->count(3)->create(['pet_id' => $pet->id]);

    Livewire::test(Show::class, ['slug' => $pet->slug])
        ->assertSet('selectedPhotoIndex', 0)
        ->call('nextPhoto')
        ->assertSet('selectedPhotoIndex', 1)
        ->call('nextPhoto')
        ->assertSet('selectedPhotoIndex', 2)
        ->call('nextPhoto')
        ->assertSet('selectedPhotoIndex', 0);
});

test('can navigate backwards through photo gallery', function () {
    $species = Species::factory()->create(['name' => 'Dog']);
    $pet = Pet::factory()->create([
        'species_id' => $species->id,
        'slug' => 'buddy-the-dog',
    ]);

    PetPhoto::factory()->count(3)->create(['pet_id' => $pet->id]);

    Livewire::test(Show::class, ['slug' => $pet->slug])
        ->assertSet('selectedPhotoIndex', 0)
        ->call('previousPhoto')
        ->assertSet('selectedPhotoIndex', 2)
        ->call('previousPhoto')
        ->assertSet('selectedPhotoIndex', 1);
});

test('can select specific photo', function () {
    $species = Species::factory()->create(['name' => 'Dog']);
    $pet = Pet::factory()->create([
        'species_id' => $species->id,
        'slug' => 'buddy-the-dog',
    ]);

    PetPhoto::factory()->count(3)->create(['pet_id' => $pet->id]);

    Livewire::test(Show::class, ['slug' => $pet->slug])
        ->call('selectPhoto', 2)
        ->assertSet('selectedPhotoIndex', 2);
});

test('shows apply button for authenticated users', function () {
    $species = Species::factory()->create(['name' => 'Dog']);
    $pet = Pet::factory()->create([
        'species_id' => $species->id,
        'slug' => 'buddy-the-dog',
    ]);

    $user = \App\Models\User::factory()->create();

    Livewire::actingAs($user)->test(Show::class, ['slug' => $pet->slug])
        ->assertSee('Apply to Adopt');
});

test('shows sign in buttons for guest users', function () {
    $species = Species::factory()->create(['name' => 'Dog']);
    $pet = Pet::factory()->create([
        'species_id' => $species->id,
        'slug' => 'buddy-the-dog',
    ]);

    Livewire::test(Show::class, ['slug' => $pet->slug])
        ->assertSee('Sign In to Apply')
        ->assertSee('Create Account');
});

test('displays breadcrumb navigation', function () {
    $species = Species::factory()->create(['name' => 'Dog']);
    $pet = Pet::factory()->create([
        'species_id' => $species->id,
        'name' => 'Buddy',
        'slug' => 'buddy-the-dog',
    ]);

    Livewire::test(Show::class, ['slug' => $pet->slug])
        ->assertSee('Home')
        ->assertSee('Pets')
        ->assertSee('Buddy');
});

test('returns 404 for non-existent pet', function () {
    get(route('pets.show', 'non-existent-slug'))
        ->assertNotFound();
});
