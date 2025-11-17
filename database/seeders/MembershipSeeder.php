<?php

namespace Database\Seeders;

use App\Models\Membership;
use App\Models\User;
use Illuminate\Database\Seeder;

class MembershipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        // Assign memberships to about 60% of users
        $usersWithMemberships = $users->random((int) ceil($users->count() * 0.6));

        foreach ($usersWithMemberships as $user) {
            // 70% chance of active membership
            if (fake()->boolean(70)) {
                $membership = Membership::factory()
                    ->active()
                    ->create(['user_id' => $user->id]);

                // Set this as the user's current membership
                $user->update(['current_membership_id' => $membership->id]);
            }
            // 15% chance of expired membership
            elseif (fake()->boolean(50)) {
                Membership::factory()
                    ->expired()
                    ->create(['user_id' => $user->id]);
            }
            // 15% chance of canceled membership
            else {
                Membership::factory()
                    ->canceled()
                    ->create(['user_id' => $user->id]);
            }
        }

        // Give some users multiple memberships (historical + current)
        $usersWithHistory = $usersWithMemberships->random((int) min(5, $usersWithMemberships->count()));

        foreach ($usersWithHistory as $user) {
            // Add an expired membership to their history
            Membership::factory()
                ->expired()
                ->create(['user_id' => $user->id]);
        }
    }
}
