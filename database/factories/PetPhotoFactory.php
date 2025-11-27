<?php

namespace Database\Factories;

use App\Models\Pet;
use Database\Factories\Concerns\CopiesSeederImages;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PetPhoto>
 */
class PetPhotoFactory extends Factory
{
    use CopiesSeederImages;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pet_id' => Pet::factory(),
            'file_path' => $this->copyRandomSeederImage('pet_samples', 'pets'),
            'is_primary' => false,
            'display_order' => fake()->numberBetween(0, 10),
        ];
    }

    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => true,
            'display_order' => 0,
        ]);
    }
}
