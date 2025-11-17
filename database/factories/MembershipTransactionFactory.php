<?php

namespace Database\Factories;

use App\Models\Membership;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MembershipTransaction>
 */
class MembershipTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'membership_id' => Membership::factory(),
            'type' => 'payment',
            'amount' => fake()->randomFloat(2, 10, 100),
            'payment_method' => 'card',
            'stripe_payment_id' => 'ch_'.fake()->regexify('[A-Za-z0-9]{24}'),
            'status' => 'completed',
            'processed_by' => null,
            'notes' => null,
        ];
    }

    /**
     * Indicate that the transaction is a refund.
     */
    public function refund(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'refund',
            'stripe_payment_id' => 're_'.fake()->regexify('[A-Za-z0-9]{24}'),
            'processed_by' => User::factory()->state(['is_admin' => true]),
            'notes' => fake()->sentence(),
        ]);
    }

    /**
     * Indicate that the transaction is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the transaction failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'notes' => fake()->sentence(),
        ]);
    }
}
