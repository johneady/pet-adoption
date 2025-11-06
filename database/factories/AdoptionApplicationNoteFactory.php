<?php

namespace Database\Factories;

use App\Models\AdoptionApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AdoptionApplicationNote>
 */
class AdoptionApplicationNoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'adoption_application_id' => AdoptionApplication::factory(),
            'note' => fake()->paragraph(),
            'created_by' => User::factory(),
        ];
    }
}
