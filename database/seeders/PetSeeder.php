<?php

namespace Database\Seeders;

use App\Models\Breed;
use App\Models\Pet;
use App\Models\PetPhoto;
use App\Models\Species;
use Illuminate\Database\Seeder;

class PetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $species = Species::with('breeds')->get();

        $dogNames = ['Max', 'Bella', 'Charlie', 'Luna', 'Cooper', 'Daisy', 'Rocky', 'Sadie', 'Buddy', 'Molly', 'Duke', 'Maggie', 'Bear', 'Sophie', 'Tucker'];
        $catNames = ['Whiskers', 'Shadow', 'Mittens', 'Simba', 'Oliver', 'Cleo', 'Felix', 'Nala', 'Tiger', 'Princess', 'Smokey', 'Kitty', 'Oscar', 'Bella', 'Lucy'];
        $rabbitNames = ['Thumper', 'Cottontail', 'Snowball', 'Oreo', 'Cinnamon', 'Flopsy', 'Hoppy', 'Patches', 'Nibbles', 'Velvet'];
        $birdNames = ['Tweety', 'Rio', 'Kiwi', 'Polly', 'Mango', 'Sky', 'Sunny', 'Peaches', 'Coco', 'Blue'];
        $guineaPigNames = ['Peanut', 'Bubbles', 'Ginger', 'Caramel', 'Pumpkin', 'Squeaky', 'Fuzzy', 'Cocoa', 'Marshmallow', 'Pepper'];

        $dogBreeds = Breed::whereHas('species', fn ($q) => $q->where('slug', 'dog'))->pluck('id')->toArray();
        $catBreeds = Breed::whereHas('species', fn ($q) => $q->where('slug', 'cat'))->pluck('id')->toArray();
        $rabbitBreeds = Breed::whereHas('species', fn ($q) => $q->where('slug', 'rabbit'))->pluck('id')->toArray();
        $birdBreeds = Breed::whereHas('species', fn ($q) => $q->where('slug', 'bird'))->pluck('id')->toArray();
        $guineaPigBreeds = Breed::whereHas('species', fn ($q) => $q->where('slug', 'guinea-pig'))->pluck('id')->toArray();

        $dogSpeciesId = Species::where('slug', 'dog')->first()->id;
        $catSpeciesId = Species::where('slug', 'cat')->first()->id;
        $rabbitSpeciesId = Species::where('slug', 'rabbit')->first()->id;
        $birdSpeciesId = Species::where('slug', 'bird')->first()->id;
        $guineaPigSpeciesId = Species::where('slug', 'guinea-pig')->first()->id;

        foreach ($dogNames as $name) {
            Pet::factory()->create([
                'name' => $name,
                'slug' => \Illuminate\Support\Str::slug($name.'-'.fake()->randomNumber(4)),
                'species_id' => $dogSpeciesId,
                'breed_id' => fake()->randomElement($dogBreeds),
                'status' => fake()->randomElement(['available', 'available', 'available', 'pending', 'adopted', 'unavailable']),
            ]);
        }

        foreach ($catNames as $name) {
            Pet::factory()->create([
                'name' => $name,
                'slug' => \Illuminate\Support\Str::slug($name.'-'.fake()->randomNumber(4)),
                'species_id' => $catSpeciesId,
                'breed_id' => fake()->randomElement($catBreeds),
                'status' => fake()->randomElement(['available', 'available', 'available', 'pending', 'adopted', 'unavailable']),
            ]);
        }

        foreach ($rabbitNames as $name) {
            Pet::factory()->create([
                'name' => $name,
                'slug' => \Illuminate\Support\Str::slug($name.'-'.fake()->randomNumber(4)),
                'species_id' => $rabbitSpeciesId,
                'breed_id' => fake()->randomElement($rabbitBreeds),
                'status' => fake()->randomElement(['available', 'available', 'pending', 'adopted']),
            ]);
        }

        foreach ($birdNames as $name) {
            Pet::factory()->create([
                'name' => $name,
                'slug' => \Illuminate\Support\Str::slug($name.'-'.fake()->randomNumber(4)),
                'species_id' => $birdSpeciesId,
                'breed_id' => fake()->randomElement($birdBreeds),
                'status' => fake()->randomElement(['available', 'available', 'pending', 'adopted']),
            ]);
        }

        foreach ($guineaPigNames as $name) {
            Pet::factory()->create([
                'name' => $name,
                'slug' => \Illuminate\Support\Str::slug($name.'-'.fake()->randomNumber(4)),
                'species_id' => $guineaPigSpeciesId,
                'breed_id' => fake()->randomElement($guineaPigBreeds),
                'status' => fake()->randomElement(['available', 'available', 'pending']),
            ]);
        }

        $pets = Pet::all();
        foreach ($pets as $pet) {
            $photoCount = fake()->numberBetween(1, 4);
            for ($i = 0; $i < $photoCount; $i++) {
                PetPhoto::factory()->create([
                    'pet_id' => $pet->id,
                    'is_primary' => $i === 0,
                ]);
            }
        }
    }
}
