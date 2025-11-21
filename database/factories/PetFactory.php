<?php

namespace Database\Factories;

use App\Models\Breed;
use App\Models\Species;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pet>
 */
class PetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->firstName();

        return [
            'species_id' => Species::factory(),
            'breed_id' => Breed::factory(),
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name.'-'.fake()->randomNumber(4)),
            'age' => fake()->numberBetween(0, 15),
            'gender' => fake()->randomElement(['male', 'female', 'unknown']),
            'size' => fake()->randomElement(['small', 'medium', 'large', 'extra_large']),
            'color' => fake()->randomElement(['Black', 'White', 'Brown', 'Gray', 'Golden', 'Mixed']),
            'description' => fake()->paragraphs(3, true),
            'medical_notes' => fake()->optional()->paragraph(),
            'vaccination_status' => fake()->boolean(),
            'special_needs' => fake()->boolean(30),
            'intake_date' => fake()->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'status' => fake()->randomElement(['available', 'pending', 'adopted', 'coming_soon']),
        ];
    }
}
