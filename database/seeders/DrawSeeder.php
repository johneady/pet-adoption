<?php

namespace Database\Seeders;

use App\Models\Draw;
use App\Models\DrawTicket;
use App\Models\User;
use Illuminate\Database\Seeder;

class DrawSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('is_admin', false)->get();

        if ($users->isEmpty()) {
            $users = User::factory(5)->withProfilePicture()->create();
        }

        // Create a current active draw
        $activeDraw = Draw::factory()->active()->create([
            'name' => 'Spring 50/50 Draw',
            'description' => 'Support our shelter animals with this exciting 50/50 draw! Half of all proceeds go directly to animal care and the other half goes to one lucky winner. Get your tickets today!',
        ]);

        // Add tickets to active draw
        $prices = [1.00, 3.00, 5.00];
        foreach ($users->random(min(3, $users->count())) as $user) {
            $numTickets = fake()->numberBetween(1, 10);
            for ($i = 0; $i < $numTickets; $i++) {
                DrawTicket::create([
                    'draw_id' => $activeDraw->id,
                    'user_id' => $user->id,
                    'ticket_number' => $activeDraw->nextTicketNumber(),
                    'amount_paid' => fake()->randomElement($prices),
                    'is_winner' => false,
                ]);
            }
        }

        // Create a past finalized draw with winner
        $pastDraw = Draw::factory()->finalized()->create([
            'name' => 'Winter Wonderland 50/50',
            'description' => 'Our winter draw was a huge success! Thank you to everyone who participated.',
        ]);

        // Add tickets to past draw
        $pastTickets = [];
        foreach ($users->random(min(4, $users->count())) as $user) {
            $numTickets = fake()->numberBetween(2, 15);
            for ($i = 0; $i < $numTickets; $i++) {
                $ticket = DrawTicket::create([
                    'draw_id' => $pastDraw->id,
                    'user_id' => $user->id,
                    'ticket_number' => $pastDraw->nextTicketNumber(),
                    'amount_paid' => fake()->randomElement($prices),
                    'is_winner' => false,
                ]);
                $pastTickets[] = $ticket;
            }
        }

        // Select a winner for past draw
        if (! empty($pastTickets)) {
            $winnerTicket = $pastTickets[array_rand($pastTickets)];
            $winnerTicket->update(['is_winner' => true]);
            $pastDraw->update(['winner_ticket_id' => $winnerTicket->id]);
        }

        // Create an upcoming draw
        Draw::factory()->upcoming()->create([
            'name' => 'Summer Splash 50/50',
            'description' => 'Coming soon! Our biggest draw of the year. Mark your calendars and get ready to support our furry friends!',
        ]);
    }
}
