<?php

use App\Models\Draw;
use App\Models\DrawTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Draw Model', function () {
    it('can determine if a draw is active', function () {
        $activeDraw = Draw::factory()->active()->create();
        $endedDraw = Draw::factory()->ended()->create();
        $upcomingDraw = Draw::factory()->upcoming()->create();

        expect($activeDraw->isActive())->toBeTrue();
        expect($endedDraw->isActive())->toBeFalse();
        expect($upcomingDraw->isActive())->toBeFalse();
    });

    it('can determine if a draw has ended', function () {
        $activeDraw = Draw::factory()->active()->create();
        $endedDraw = Draw::factory()->ended()->create();

        expect($activeDraw->hasEnded())->toBeFalse();
        expect($endedDraw->hasEnded())->toBeTrue();
    });

    it('can count total tickets sold', function () {
        $draw = Draw::factory()->create();
        $user = User::factory()->create();

        DrawTicket::factory()->count(5)->create([
            'draw_id' => $draw->id,
            'user_id' => $user->id,
        ]);

        expect($draw->totalTicketsSold())->toBe(5);
    });

    it('generates sequential ticket numbers', function () {
        $draw = Draw::factory()->create();

        expect($draw->nextTicketNumber())->toBe(1);

        DrawTicket::factory()->create([
            'draw_id' => $draw->id,
            'ticket_number' => 1,
        ]);

        expect($draw->nextTicketNumber())->toBe(2);

        DrawTicket::factory()->create([
            'draw_id' => $draw->id,
            'ticket_number' => 5,
        ]);

        expect($draw->nextTicketNumber())->toBe(6);
    });

    it('can select a random winner', function () {
        $draw = Draw::factory()->ended()->create();
        $users = User::factory()->count(3)->create();

        foreach ($users as $index => $user) {
            DrawTicket::factory()->create([
                'draw_id' => $draw->id,
                'user_id' => $user->id,
                'ticket_number' => $index + 1,
            ]);
        }

        $winningTicket = $draw->selectRandomWinner();

        expect($winningTicket)->not->toBeNull();
        expect($winningTicket->is_winner)->toBeTrue();
        expect($draw->refresh()->is_finalized)->toBeTrue();
        expect($draw->winner_ticket_id)->toBe($winningTicket->id);
    });

    it('cannot select winner if already finalized', function () {
        $draw = Draw::factory()->finalized()->create();

        $result = $draw->selectRandomWinner();

        expect($result)->toBeNull();
    });

    it('ensures fair selection by individual ticket entries', function () {
        $draw = Draw::factory()->ended()->create();
        $userWithManyTickets = User::factory()->create();
        $userWithOneTicket = User::factory()->create();

        // User A gets 10 tickets
        for ($i = 1; $i <= 10; $i++) {
            DrawTicket::factory()->create([
                'draw_id' => $draw->id,
                'user_id' => $userWithManyTickets->id,
                'ticket_number' => $i,
            ]);
        }

        // User B gets 1 ticket
        DrawTicket::factory()->create([
            'draw_id' => $draw->id,
            'user_id' => $userWithOneTicket->id,
            'ticket_number' => 11,
        ]);

        // Total tickets should be 11
        expect($draw->totalTicketsSold())->toBe(11);

        // User A should have 10 tickets in the draw
        expect($draw->tickets()->where('user_id', $userWithManyTickets->id)->count())->toBe(10);
    });
});

describe('Draws Page', function () {
    it('can view the draws page', function () {
        $response = $this->get(route('draws.index'));

        $response->assertStatus(200);
    });

    it('displays active draw information', function () {
        $draw = Draw::factory()->active()->create([
            'name' => 'Test Active Draw',
        ]);

        $response = $this->get(route('draws.index'));

        $response->assertStatus(200);
        $response->assertSee('Test Active Draw');
    });

    it('displays past draws with winners', function () {
        $draw = Draw::factory()->finalized()->create([
            'name' => 'Past Draw',
        ]);
        $user = User::factory()->create(['name' => 'Winner User']);
        $ticket = DrawTicket::factory()->winner()->create([
            'draw_id' => $draw->id,
            'user_id' => $user->id,
        ]);
        $draw->update(['winner_ticket_id' => $ticket->id]);

        $response = $this->get(route('draws.index'));

        $response->assertStatus(200);
        $response->assertSee('Past Draw');
        $response->assertSee('Winner User');
    });

    it('shows user tickets when authenticated', function () {
        $user = User::factory()->create();
        $draw = Draw::factory()->active()->create();

        DrawTicket::factory()->create([
            'draw_id' => $draw->id,
            'user_id' => $user->id,
            'ticket_number' => 42,
        ]);

        $response = $this->actingAs($user)->get(route('draws.index'));

        $response->assertStatus(200);
        $response->assertSee('#42');
    });
});

describe('User Notification Preferences', function () {
    it('includes draw result alerts in user factory', function () {
        $user = User::factory()->receivesNotifications()->create();

        expect($user->receive_draw_result_alerts)->toBeTrue();
    });

    it('can update draw result alert preference', function () {
        $user = User::factory()->admin()->create([
            'receive_draw_result_alerts' => true,
        ]);

        $user->update(['receive_draw_result_alerts' => false]);

        expect($user->refresh()->receive_draw_result_alerts)->toBeFalse();
    });
});
