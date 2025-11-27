<?php

namespace Database\Seeders;

use App\Models\AdoptionApplication;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdoptionApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::factory()->count(20)->withProfilePicture()->create();

        $pendingPets = Pet::where('status', 'available')->take(5)->get();
        foreach ($pendingPets as $pet) {
            AdoptionApplication::factory()->create([
                'user_id' => fake()->randomElement($users->pluck('id')->toArray()),
                'pet_id' => $pet->id,
                'status' => 'submitted',
            ]);
        }

        $underReviewPets = Pet::where('status', 'available')->skip(5)->take(5)->get();
        foreach ($underReviewPets as $pet) {
            AdoptionApplication::factory()->create([
                'user_id' => fake()->randomElement($users->pluck('id')->toArray()),
                'pet_id' => $pet->id,
                'status' => 'under_review',
            ]);
        }

        $interviewScheduledPets = Pet::where('status', 'pending')->take(5)->get();
        foreach ($interviewScheduledPets as $pet) {
            AdoptionApplication::factory()->create([
                'user_id' => fake()->randomElement($users->pluck('id')->toArray()),
                'pet_id' => $pet->id,
                'status' => 'interview_scheduled',
            ]);
        }

        $approvedPets = Pet::where('status', 'pending')->skip(5)->take(3)->get();
        foreach ($approvedPets as $pet) {
            AdoptionApplication::factory()->create([
                'user_id' => fake()->randomElement($users->pluck('id')->toArray()),
                'pet_id' => $pet->id,
                'status' => 'approved',
            ]);
        }

        $rejectedPets = Pet::where('status', 'available')->skip(10)->take(4)->get();
        foreach ($rejectedPets as $pet) {
            AdoptionApplication::factory()->create([
                'user_id' => fake()->randomElement($users->pluck('id')->toArray()),
                'pet_id' => $pet->id,
                'status' => 'rejected',
            ]);
        }

        $archivedPets = Pet::where('status', 'adopted')->take(5)->get();
        foreach ($archivedPets as $pet) {
            AdoptionApplication::factory()->create([
                'user_id' => fake()->randomElement($users->pluck('id')->toArray()),
                'pet_id' => $pet->id,
                'status' => 'archived',
            ]);
        }
    }
}
