<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        $this->call([
            SpeciesSeeder::class,
            PetSeeder::class,
            AdoptionApplicationSeeder::class,
            ApplicationStatusHistorySeeder::class,
            InterviewSeeder::class,
            SettingSeeder::class,
        ]);
    }
}
