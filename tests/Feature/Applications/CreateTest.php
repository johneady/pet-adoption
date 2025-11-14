<?php

declare(strict_types=1);

use App\Livewire\Applications\Create;
use App\Models\AdoptionApplication;
use App\Models\Pet;
use App\Models\Species;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

test('guests are redirected to login page', function () {
    $pet = Pet::factory()->create(['status' => 'available']);

    $this->get(route('applications.create', ['petId' => $pet->id]))->assertRedirect('/login');
});

test('authenticated users can visit application form', function () {
    $user = User::factory()->create();
    $pet = Pet::factory()->create(['status' => 'available']);

    actingAs($user)
        ->get(route('applications.create', ['petId' => $pet->id]))
        ->assertSuccessful()
        ->assertSee('Adoption Application');
});

test('can submit application with valid data', function () {
    $user = User::factory()->create();
    $species = Species::factory()->create();
    $pet = Pet::factory()->create([
        'species_id' => $species->id,
        'status' => 'available',
    ]);

    actingAs($user);

    Livewire::test(Create::class, ['petId' => $pet->id])
        ->set('living_situation', 'House with fenced yard')
        ->set('experience', 'Had dogs for 10 years')
        ->set('other_pets', 'One cat, spayed')
        ->set('veterinary_reference', 'Dr. Smith at Happy Paws')
        ->set('household_members', '2 adults, 1 child')
        ->set('employment_status', 'Work from home')
        ->set('reason_for_adoption', 'Looking for a companion for our family')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    assertDatabaseHas(AdoptionApplication::class, [
        'user_id' => $user->id,
        'pet_id' => $pet->id,
        'status' => 'submitted',
        'living_situation' => 'House with fenced yard',
        'reason_for_adoption' => 'Looking for a companion for our family',
    ]);
});

test('pet_id is required', function () {
    $user = User::factory()->create();
    $pet = Pet::factory()->create(['status' => 'available']);

    actingAs($user);

    Livewire::test(Create::class, ['petId' => $pet->id])
        ->set('pet_id', null)
        ->set('living_situation', 'House with fenced yard')
        ->set('reason_for_adoption', 'Looking for a companion')
        ->call('submit')
        ->assertHasErrors(['pet_id' => 'required']);
});

test('living_situation is required', function () {
    $user = User::factory()->create();
    $species = Species::factory()->create();
    $pet = Pet::factory()->create(['species_id' => $species->id, 'status' => 'available']);

    actingAs($user);

    Livewire::test(Create::class, ['petId' => $pet->id])
        ->set('reason_for_adoption', 'Looking for a companion')
        ->call('submit')
        ->assertHasErrors(['living_situation' => 'required']);
});

test('reason_for_adoption is required', function () {
    $user = User::factory()->create();
    $species = Species::factory()->create();
    $pet = Pet::factory()->create(['species_id' => $species->id, 'status' => 'available']);

    actingAs($user);

    Livewire::test(Create::class, ['petId' => $pet->id])
        ->set('living_situation', 'House with fenced yard')
        ->call('submit')
        ->assertHasErrors(['reason_for_adoption' => 'required']);
});

test('pet_id must exist in database', function () {
    $user = User::factory()->create();
    $pet = Pet::factory()->create(['status' => 'available']);

    actingAs($user);

    Livewire::test(Create::class, ['petId' => $pet->id])
        ->set('pet_id', 999999)
        ->set('living_situation', 'House with fenced yard')
        ->set('reason_for_adoption', 'Looking for a companion')
        ->call('submit')
        ->assertHasErrors(['pet_id' => 'exists']);
});

test('living_situation cannot exceed 255 characters', function () {
    $user = User::factory()->create();
    $species = Species::factory()->create();
    $pet = Pet::factory()->create(['species_id' => $species->id, 'status' => 'available']);

    actingAs($user);

    Livewire::test(Create::class, ['petId' => $pet->id])
        ->set('living_situation', str_repeat('a', 256))
        ->set('reason_for_adoption', 'Looking for a companion')
        ->call('submit')
        ->assertHasErrors(['living_situation' => 'max']);
});

test('reason_for_adoption cannot exceed 2000 characters', function () {
    $user = User::factory()->create();
    $species = Species::factory()->create();
    $pet = Pet::factory()->create(['species_id' => $species->id, 'status' => 'available']);

    actingAs($user);

    Livewire::test(Create::class, ['petId' => $pet->id])
        ->set('living_situation', 'House')
        ->set('reason_for_adoption', str_repeat('a', 2001))
        ->call('submit')
        ->assertHasErrors(['reason_for_adoption' => 'max']);
});

test('optional fields can be null', function () {
    $user = User::factory()->create();
    $species = Species::factory()->create();
    $pet = Pet::factory()->create([
        'species_id' => $species->id,
        'status' => 'available',
    ]);

    actingAs($user);

    Livewire::test(Create::class, ['petId' => $pet->id])
        ->set('living_situation', 'House with fenced yard')
        ->set('reason_for_adoption', 'Looking for a companion')
        ->call('submit')
        ->assertHasNoErrors();

    assertDatabaseHas(AdoptionApplication::class, [
        'user_id' => $user->id,
        'pet_id' => $pet->id,
        'experience' => '',
        'other_pets' => '',
        'veterinary_reference' => '',
        'household_members' => '',
        'employment_status' => '',
    ]);
});

test('throws 404 when pet does not exist', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('applications.create', ['petId' => 999999]))
        ->assertNotFound();
});

test('pet is automatically loaded from route parameter', function () {
    $user = User::factory()->create();
    $species = Species::factory()->create();
    $pet = Pet::factory()->create([
        'species_id' => $species->id,
        'status' => 'available',
        'name' => 'Test Pet',
    ]);

    actingAs($user);

    Livewire::test(Create::class, ['petId' => $pet->id])
        ->assertSet('pet_id', $pet->id)
        ->assertSet('selectedPet.name', 'Test Pet')
        ->assertSee('Test Pet');
});

test('success message is shown after submission', function () {
    $user = User::factory()->create();
    $species = Species::factory()->create();
    $pet = Pet::factory()->create([
        'species_id' => $species->id,
        'status' => 'available',
    ]);

    actingAs($user);

    Livewire::test(Create::class, ['petId' => $pet->id])
        ->set('living_situation', 'House with fenced yard')
        ->set('reason_for_adoption', 'Looking for a companion')
        ->call('submit')
        ->assertSessionHas('message', 'Your adoption application has been submitted successfully!');
});

test('prefilled pet is shown as protected and not editable', function () {
    $user = User::factory()->create();
    $species = Species::factory()->create(['name' => 'Dog']);
    $pet = Pet::factory()->create([
        'species_id' => $species->id,
        'status' => 'available',
        'name' => 'Buddy',
    ]);

    actingAs($user);

    Livewire::test(Create::class, ['petId' => $pet->id])
        ->assertSet('pet_id', $pet->id)
        ->assertSet('selectedPet.name', 'Buddy')
        ->assertSee('Applying to Adopt')
        ->assertSee('Buddy')
        ->assertSee('This application is for Buddy')
        ->assertDontSee('Select a pet');
});

test('pet status is updated to pending when application is submitted', function () {
    $user = User::factory()->create();
    $species = Species::factory()->create();
    $pet = Pet::factory()->create([
        'species_id' => $species->id,
        'status' => 'available',
    ]);

    actingAs($user);

    Livewire::test(Create::class, ['petId' => $pet->id])
        ->set('living_situation', 'House with fenced yard')
        ->set('reason_for_adoption', 'Looking for a companion')
        ->call('submit')
        ->assertHasNoErrors();

    assertDatabaseHas(Pet::class, [
        'id' => $pet->id,
        'status' => 'pending',
    ]);
});

test('submission fails when pet is no longer available', function () {
    $user = User::factory()->create();
    $species = Species::factory()->create();
    $pet = Pet::factory()->create([
        'species_id' => $species->id,
        'status' => 'available',
    ]);

    actingAs($user);

    // Simulate another user adopting the pet while this form is open
    $pet->update(['status' => 'pending']);

    Livewire::test(Create::class, ['petId' => $pet->id])
        ->set('living_situation', 'House with fenced yard')
        ->set('reason_for_adoption', 'Looking for a companion')
        ->call('submit')
        ->assertHasErrors(['pet_id' => 'This pet is no longer available for adoption.']);

    // Verify no application was created
    expect(AdoptionApplication::where('pet_id', $pet->id)->count())->toBe(0);
});
