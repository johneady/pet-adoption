<?php

namespace Database\Seeders;

use App\Models\Breed;
use App\Models\Species;
use Illuminate\Database\Seeder;

class SpeciesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $speciesData = [
            [
                'name' => 'Dog',
                'slug' => 'dog',
                'description' => 'Man\'s best friend and loyal companion',
                'breeds' => [
                    'Labrador Retriever',
                    'German Shepherd',
                    'Golden Retriever',
                    'French Bulldog',
                    'Bulldog',
                    'Poodle',
                    'Beagle',
                    'Rottweiler',
                    'German Shorthaired Pointer',
                    'Dachshund',
                    'Yorkshire Terrier',
                    'Boxer',
                    'Siberian Husky',
                    'Mixed Breed',
                ],
            ],
            [
                'name' => 'Cat',
                'slug' => 'cat',
                'description' => 'Independent and affectionate feline companions',
                'breeds' => [
                    'Domestic Shorthair',
                    'Domestic Longhair',
                    'Siamese',
                    'Persian',
                    'Maine Coon',
                    'Ragdoll',
                    'Bengal',
                    'British Shorthair',
                    'Abyssinian',
                    'Scottish Fold',
                    'Sphynx',
                    'Mixed Breed',
                ],
            ],
            [
                'name' => 'Rabbit',
                'slug' => 'rabbit',
                'description' => 'Gentle and social small pets',
                'breeds' => [
                    'Holland Lop',
                    'Netherland Dwarf',
                    'Mini Rex',
                    'Lionhead',
                    'Flemish Giant',
                    'Dutch',
                    'Mixed Breed',
                ],
            ],
            [
                'name' => 'Bird',
                'slug' => 'bird',
                'description' => 'Colorful and intelligent avian companions',
                'breeds' => [
                    'Parakeet',
                    'Cockatiel',
                    'Canary',
                    'Finch',
                    'Cockatoo',
                    'Parrot',
                ],
            ],
            [
                'name' => 'Guinea Pig',
                'slug' => 'guinea-pig',
                'description' => 'Social and vocal small pets',
                'breeds' => [
                    'American',
                    'Abyssinian',
                    'Peruvian',
                    'Silkie',
                    'Texel',
                ],
            ],
        ];

        foreach ($speciesData as $speciesInfo) {
            $breeds = $speciesInfo['breeds'];
            unset($speciesInfo['breeds']);

            $species = Species::create($speciesInfo);

            foreach ($breeds as $breedName) {
                Breed::create([
                    'species_id' => $species->id,
                    'name' => $breedName,
                    'slug' => \Illuminate\Support\Str::slug($breedName),
                ]);
            }
        }
    }
}
