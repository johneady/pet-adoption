<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FirstInstallSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@petadoption.test'],
            [
                'name' => 'Tony Testing',
                'email' => 'tony@testing.com',
                'password' => 'password',
                'email_verified_at' => now(),
                'is_admin' => true,
                'receive_new_user_alerts' => true,
                'receive_new_adoption_alerts' => true,
                'receive_draw_result_alerts' => true,
                'receive_ticket_purchase_alerts' => true,
            ]
        );

        $this->call([
            SpeciesSeeder::class,
            MembershipPlanSeeder::class,
            FormQuestionSeeder::class,
            //PetSeeder::class,
            //AdoptionApplicationSeeder::class,
            //ApplicationStatusHistorySeeder::class,
            //InterviewSeeder::class,
            SettingSeeder::class,
            TagSeeder::class,
            //BlogPostSeeder::class,
            MenuSeeder::class,
            PageSeeder::class,
            //MembershipSeeder::class,
            DrawSeeder::class,
        ]);


    }
}
