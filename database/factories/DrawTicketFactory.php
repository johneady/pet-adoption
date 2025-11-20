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
        $prices = [1.00, 3.00, 5.00];
        $draw = Draw::factory()->create();

        return [
            'draw_id' => $draw->id,
            'user_id' => User::factory(),
            'ticket_number' => $draw->nextTicketNumber(),
            'amount_paid' => fake()->randomElement($prices),
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
