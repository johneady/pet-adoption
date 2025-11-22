<?php

declare(strict_types=1);

use App\Models\Breed;
use App\Models\Pet;
use App\Models\Species;

test('can delete breed without pets', function () {
    $breed = Breed::factory()->create();

    expect($breed->delete())->toBeTrue();
    expect(Breed::find($breed->id))->toBeNull();
});

test('cannot delete breed with existing pets', function () {
    $species = Species::factory()->create();
    $breed = Breed::factory()->for($species)->create();
    Pet::factory()->for($species)->for($breed)->create();

    expect(fn () => $breed->delete())
        ->toThrow(Exception::class, 'Cannot delete breed that has existing pets.');

    expect(Breed::find($breed->id))->not->toBeNull();
});

test('can delete breed after all pets are removed', function () {
    $species = Species::factory()->create();
    $breed = Breed::factory()->for($species)->create();
    $pet = Pet::factory()->for($species)->for($breed)->create();

    expect(fn () => $breed->delete())
        ->toThrow(Exception::class, 'Cannot delete breed that has existing pets.');

    $pet->forceDelete();

    expect($breed->delete())->toBeTrue();
    expect(Breed::find($breed->id))->toBeNull();
});
