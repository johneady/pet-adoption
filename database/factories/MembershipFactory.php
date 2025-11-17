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
        $paymentType = fake()->randomElement(['annual', 'monthly']);
        $plan = MembershipPlan::inRandomOrder()->first() ?? MembershipPlan::factory()->create();

        return [
            'user_id' => User::factory(),
            'plan_id' => $plan->id,
            'payment_type' => $paymentType,
            'status' => 'active',
            'amount_paid' => $paymentType === 'annual' ? $plan->annual_price : $plan->monthly_price,
            'stripe_subscription_id' => $paymentType === 'monthly' ? 'sub_'.fake()->regexify('[A-Za-z0-9]{24}') : null,
            'stripe_payment_intent_id' => $paymentType === 'annual' ? 'pi_'.fake()->regexify('[A-Za-z0-9]{24}') : null,
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

    /**
     * Indicate that the membership is annual.
     */
    public function annual(): static
    {
        return $this->state(function (array $attributes) {
            $plan = MembershipPlan::find($attributes['plan_id']);

            return [
                'payment_type' => 'annual',
                'amount_paid' => $plan->annual_price,
                'stripe_subscription_id' => null,
                'stripe_payment_intent_id' => 'pi_'.fake()->regexify('[A-Za-z0-9]{24}'),
            ];
        });
    }

    /**
     * Indicate that the membership is monthly.
     */
    public function monthly(): static
    {
        return $this->state(function (array $attributes) {
            $plan = MembershipPlan::find($attributes['plan_id']);

            return [
                'payment_type' => 'monthly',
                'amount_paid' => $plan->monthly_price,
                'stripe_subscription_id' => 'sub_'.fake()->regexify('[A-Za-z0-9]{24}'),
                'stripe_payment_intent_id' => null,
            ];
        });
    }
}
