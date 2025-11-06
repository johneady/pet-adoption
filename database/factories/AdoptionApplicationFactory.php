<?php

namespace Database\Factories;

use App\Models\Pet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AdoptionApplication>
 */
class AdoptionApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'pet_id' => Pet::factory(),
            'status' => fake()->randomElement(['submitted', 'under_review', 'interview_scheduled', 'approved', 'rejected', 'archived']),
            'living_situation' => fake()->randomElement(['House with yard', 'Apartment', 'Condo', 'Farm', 'Other']),
            'experience' => fake()->optional()->paragraph(),
            'other_pets' => fake()->optional()->sentence(),
            'veterinary_reference' => fake()->optional()->phoneNumber(),
            'household_members' => fake()->optional()->sentence(),
            'employment_status' => fake()->randomElement(['Employed Full-time', 'Employed Part-time', 'Self-employed', 'Retired', 'Other']),
            'reason_for_adoption' => fake()->paragraph(),
        ];
    }
}
