<?php

namespace Database\Factories;

use App\Models\MembershipPlan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Membership>
 */
class MembershipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startedAt = fake()->dateTimeBetween('-6 months', 'now');
        $plan = MembershipPlan::inRandomOrder()->first() ?? MembershipPlan::factory()->create();

        return [
            'user_id' => User::factory(),
            'plan_id' => $plan->id,
            'status' => 'active',
            'amount_paid' => $plan->price,
            'paypal_transaction_id' => fake()->regexify('[A-Z0-9]{17}'),
            'started_at' => $startedAt,
            'expires_at' => (clone $startedAt)->modify('+1 year'),
            'canceled_at' => null,
        ];
    }

    /**
     * Indicate that the membership is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'expired',
            'started_at' => now()->subYear()->subMonth(),
            'expires_at' => now()->subMonth(),
        ]);
    }

    /**
     * Indicate that the membership is canceled.
     */
    public function canceled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'canceled',
            'canceled_at' => now()->subDays(fake()->numberBetween(1, 30)),
        ]);
    }

    /**
     * Indicate that the membership is refunded.
     */
    public function refunded(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'refunded',
            'canceled_at' => now()->subDays(fake()->numberBetween(1, 30)),
        ]);
    }

    /**
     * Indicate that the membership is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'started_at' => now()->subMonths(fake()->numberBetween(1, 11)),
            'expires_at' => now()->addMonths(fake()->numberBetween(1, 11)),
        ]);
    }
}
