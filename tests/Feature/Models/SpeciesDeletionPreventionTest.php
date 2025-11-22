<?php

declare(strict_types=1);

use App\Models\Pet;
use App\Models\Species;

test('can delete species without pets', function () {
    $species = Species::factory()->create();

    expect($species->delete())->toBeTrue();
    expect(Species::find($species->id))->toBeNull();
});

test('cannot delete species with existing pets', function () {
    $species = Species::factory()->create();
    Pet::factory()->for($species)->create();

    expect(fn () => $species->delete())
        ->toThrow(Exception::class, 'Cannot delete species that has existing pets.');

    expect(Species::find($species->id))->not->toBeNull();
});

test('can delete species after all pets are removed', function () {
    $species = Species::factory()->create();
    $pet = Pet::factory()->for($species)->create();

    expect(fn () => $species->delete())
        ->toThrow(Exception::class, 'Cannot delete species that has existing pets.');

    $pet->forceDelete();

    expect($species->delete())->toBeTrue();
    expect(Species::find($species->id))->toBeNull();
});
