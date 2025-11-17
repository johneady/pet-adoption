<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MembershipPlan>
 */
class MembershipPlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tiers = ['Bronze', 'Silver', 'Gold'];
        $tier = fake()->randomElement($tiers);
        $multiplier = ['Bronze' => 1, 'Silver' => 2, 'Gold' => 4][$tier];

        return [
            'name' => $tier,
            'slug' => \Str::slug($tier),
            'price' => 25 * $multiplier,
            'description' => fake()->sentence(),
            'features' => [
                'Badge of honor on profile',
                'Support the agency',
                'Help pets find homes',
            ],
            'badge_color' => ['Bronze' => '#cd7f32', 'Silver' => '#c0c0c0', 'Gold' => '#ffd700'][$tier],
            'badge_icon' => 'star',
            'display_order' => ['Bronze' => 1, 'Silver' => 2, 'Gold' => 3][$tier],
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the plan is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
