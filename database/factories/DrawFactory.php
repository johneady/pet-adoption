<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Draw>
 */
class DrawFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startsAt = fake()->dateTimeBetween('-1 month', '+1 week')->format('Y-m-d');
        $endsAt = fake()->dateTimeBetween($startsAt, $startsAt.' +30 days')->format('Y-m-d');

        return [
            'name' => fake()->words(3, true).' Draw',
            'description' => fake()->paragraph(),
            'ticket_price_tiers' => [
                ['quantity' => 1, 'price' => 1.00],
                ['quantity' => 5, 'price' => 3.00],
                ['quantity' => 10, 'price' => 5.00],
            ],
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'is_finalized' => false,
            'winner_ticket_id' => null,
        ];
    }

    /**
     * Indicate that the draw is currently active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'starts_at' => now()->subDays(fake()->numberBetween(1, 7))->toDateString(),
            'ends_at' => now()->addDays(fake()->numberBetween(7, 30))->toDateString(),
            'is_finalized' => false,
        ]);
    }

    /**
     * Indicate that the draw has ended but winner not selected.
     */
    public function ended(): static
    {
        return $this->state(fn (array $attributes) => [
            'starts_at' => now()->subDays(fake()->numberBetween(14, 30))->toDateString(),
            'ends_at' => now()->subDays(fake()->numberBetween(1, 7))->toDateString(),
            'is_finalized' => false,
        ]);
    }

    /**
     * Indicate that the draw is finalized with a winner.
     */
    public function finalized(): static
    {
        return $this->state(fn (array $attributes) => [
            'starts_at' => now()->subDays(fake()->numberBetween(30, 60))->toDateString(),
            'ends_at' => now()->subDays(fake()->numberBetween(14, 30))->toDateString(),
            'is_finalized' => true,
        ]);
    }

    /**
     * Indicate that the draw has not started yet.
     */
    public function upcoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'starts_at' => now()->addDays(fake()->numberBetween(1, 7))->toDateString(),
            'ends_at' => now()->addDays(fake()->numberBetween(14, 30))->toDateString(),
            'is_finalized' => false,
        ]);
    }
}
