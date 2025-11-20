<?php

namespace Database\Factories;

use App\Models\Draw;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TicketPurchaseRequest>
 */
class TicketPurchaseRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tiers = [
            ['quantity' => 1, 'price' => 1.00],
            ['quantity' => 3, 'price' => 3.00],
            ['quantity' => 5, 'price' => 5.00],
        ];

        $selectedTier = fake()->randomElement($tiers);

        return [
            'draw_id' => Draw::factory(),
            'user_id' => User::factory(),
            'quantity' => $selectedTier['quantity'],
            'pricing_tier' => $selectedTier,
            'status' => 'pending',
        ];
    }

    /**
     * Indicate that the request is fulfilled.
     */
    public function fulfilled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'fulfilled',
        ]);
    }

    /**
     * Indicate that the request is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }
}
