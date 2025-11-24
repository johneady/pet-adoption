<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FirstUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@petadoption.test'],
            [
                'name' => 'Admin User',
                'email' => 'admin@petadoption.test',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_admin' => true,
                'receive_new_user_alerts' => true,
                'receive_new_adoption_alerts' => true,
                'receive_draw_result_alerts' => true,
                'receive_ticket_purchase_alerts' => true,
            ]
        );
    }
}
