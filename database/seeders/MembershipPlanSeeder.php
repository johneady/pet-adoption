<?php

namespace Database\Seeders;

use App\Models\MembershipPlan;
use Illuminate\Database\Seeder;

class MembershipPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Bronze',
                'slug' => 'bronze',
                'price' => 25.00,
                'description' => 'Show your support with a Bronze membership badge. Help us provide better care for pets awaiting adoption.',
                'features' => [
                    'Bronze badge of honor on your profile',
                    'Support the agency operations',
                    'Help pets find loving homes',
                    'Make a difference in your community',
                ],
                'badge_color' => '#cd7f32',
                'badge_icon' => 'star',
                'display_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Silver',
                'slug' => 'silver',
                'price' => 50.00,
                'description' => 'Demonstrate your commitment with a Silver membership badge. Your donation helps us improve facilities and care.',
                'features' => [
                    'Silver badge of honor on your profile',
                    'Support enhanced pet care programs',
                    'Help fund facility improvements',
                    'Contribute to community outreach',
                ],
                'badge_color' => '#c0c0c0',
                'badge_icon' => 'star',
                'display_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Gold',
                'slug' => 'gold',
                'price' => 100.00,
                'description' => 'Be a champion for pets with a Gold membership badge. Your generous support enables us to help more animals.',
                'features' => [
                    'Gold badge of honor on your profile',
                    'Support comprehensive veterinary care',
                    'Help fund rescue operations',
                    'Enable special needs pet programs',
                    'Make a significant impact',
                ],
                'badge_color' => '#ffd700',
                'badge_icon' => 'star',
                'display_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            MembershipPlan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }
    }
}
