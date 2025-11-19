<?php

namespace Database\Factories;

use App\Models\Draw;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DrawTicket>
 */
class DrawTicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'draw_id' => Draw::factory(),
            'user_id' => User::factory(),
            'ticket_number' => fake()->unique()->numberBetween(1, 10000),
            'is_winner' => false,
        ];
    }

    /**
     * Indicate that the ticket is a winner.
     */
    public function winner(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_winner' => true,
        ]);
    }
}
