<?php

declare(strict_types=1);

use App\Filament\Resources\TicketPurchaseRequests\Pages\ListTicketPurchaseRequests;
use App\Filament\Resources\TicketPurchaseRequests\TicketPurchaseRequestResource;
use App\Mail\TicketRegistrationConfirmation;
use App\Models\Draw;
use App\Models\DrawTicket;
use App\Models\TicketPurchaseRequest;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

uses()->group('filament', 'ticket-purchase-requests');

beforeEach(function () {
    $this->admin = User::factory()->create(['is_admin' => true]);
    $this->user = User::factory()->create(['is_admin' => false]);

    Filament::setCurrentPanel('app');
});

// Resource Access Tests
it('allows admin to access ticket purchase request resource', function () {
    $this->actingAs($this->admin)
        ->get(TicketPurchaseRequestResource::getUrl('index'))
        ->assertSuccessful();
});

it('denies non-admin access to ticket purchase request resource', function () {
    $this->actingAs($this->user)
        ->get(TicketPurchaseRequestResource::getUrl('index'))
        ->assertForbidden();
});

it('denies guest access to ticket purchase request resource', function () {
    $this->get(TicketPurchaseRequestResource::getUrl('index'))
        ->assertRedirect();
});

// Navigation Badge Tests
it('displays navigation badge with pending request count', function () {
    TicketPurchaseRequest::factory()->count(3)->create(['status' => 'pending']);
    TicketPurchaseRequest::factory()->count(2)->create(['status' => 'fulfilled']);

    expect(TicketPurchaseRequestResource::getNavigationBadge())->toBe('3');
});

it('does not display badge when no pending requests', function () {
    TicketPurchaseRequest::factory()->count(2)->create(['status' => 'fulfilled']);

    expect(TicketPurchaseRequestResource::getNavigationBadge())->toBeNull();
});

it('returns warning color for navigation badge', function () {
    expect(TicketPurchaseRequestResource::getNavigationBadgeColor())->toBe('warning');
});

// Table Display Tests
it('displays all requests in table', function () {
    $requests = TicketPurchaseRequest::factory()->count(3)->create();

    Livewire::actingAs($this->admin)
        ->test(ListTicketPurchaseRequests::class)
        ->assertCanSeeTableRecords($requests);
});

it('displays status badge with correct color for pending requests', function () {
    $request = TicketPurchaseRequest::factory()->create(['status' => 'pending']);

    Livewire::actingAs($this->admin)
        ->test(ListTicketPurchaseRequests::class)
        ->assertTableColumnExists('status')
        ->assertCanSeeTableRecords([$request]);
});

it('displays status badge with correct color for fulfilled requests', function () {
    $request = TicketPurchaseRequest::factory()->fulfilled()->create();

    Livewire::actingAs($this->admin)
        ->test(ListTicketPurchaseRequests::class)
        ->assertCanSeeTableRecords([$request]);
});

it('displays status badge with correct color for cancelled requests', function () {
    $request = TicketPurchaseRequest::factory()->cancelled()->create();

    Livewire::actingAs($this->admin)
        ->test(ListTicketPurchaseRequests::class)
        ->assertCanSeeTableRecords([$request]);
});

it('displays draw name in table', function () {
    $draw = Draw::factory()->create(['name' => 'Test Draw 123']);
    $request = TicketPurchaseRequest::factory()->create(['draw_id' => $draw->id]);

    Livewire::actingAs($this->admin)
        ->test(ListTicketPurchaseRequests::class)
        ->assertCanSeeTableRecords([$request])
        ->assertSee('Test Draw 123');
});

it('displays user name in table', function () {
    $user = User::factory()->create(['name' => 'John Doe']);
    $request = TicketPurchaseRequest::factory()->create(['user_id' => $user->id]);

    Livewire::actingAs($this->admin)
        ->test(ListTicketPurchaseRequests::class)
        ->assertCanSeeTableRecords([$request])
        ->assertSee('John Doe');
});

it('displays quantity in table', function () {
    $request = TicketPurchaseRequest::factory()->create(['quantity' => 5]);

    Livewire::actingAs($this->admin)
        ->test(ListTicketPurchaseRequests::class)
        ->assertCanSeeTableRecords([$request])
        ->assertSee('5');
});

it('displays formatted pricing tier in table', function () {
    $request = TicketPurchaseRequest::factory()->create([
        'pricing_tier' => ['quantity' => 3, 'price' => 3.00],
    ]);

    Livewire::actingAs($this->admin)
        ->test(ListTicketPurchaseRequests::class)
        ->assertCanSeeTableRecords([$request])
        ->assertSee('3 tickets - $3.00');
});

// Filter Tests
it('filters requests by status', function () {
    $pendingRequest = TicketPurchaseRequest::factory()->create(['status' => 'pending']);
    $fulfilledRequest = TicketPurchaseRequest::factory()->fulfilled()->create();

    Livewire::actingAs($this->admin)
        ->test(ListTicketPurchaseRequests::class)
        ->filterTable('status', 'pending')
        ->assertCanSeeTableRecords([$pendingRequest])
        ->assertCanNotSeeTableRecords([$fulfilledRequest]);
});

it('filters requests by draw', function () {
    $draw1 = Draw::factory()->create();
    $draw2 = Draw::factory()->create();

    $request1 = TicketPurchaseRequest::factory()->create(['draw_id' => $draw1->id]);
    $request2 = TicketPurchaseRequest::factory()->create(['draw_id' => $draw2->id]);

    Livewire::actingAs($this->admin)
        ->test(ListTicketPurchaseRequests::class)
        ->filterTable('draw_id', $draw1->id)
        ->assertCanSeeTableRecords([$request1])
        ->assertCanNotSeeTableRecords([$request2]);
});

// Confirm Payment Action Tests
it('confirms payment and creates tickets', function () {
    Mail::fake();

    $draw = Draw::factory()->create();
    $user = User::factory()->create();
    $request = TicketPurchaseRequest::factory()->create([
        'draw_id' => $draw->id,
        'user_id' => $user->id,
        'quantity' => 3,
        'pricing_tier' => ['quantity' => 3, 'price' => 3.00],
        'status' => 'pending',
    ]);

    Livewire::actingAs($this->admin)
        ->test(ListTicketPurchaseRequests::class)
        ->callTableAction('confirm_payment', $request);

    expect(DrawTicket::where('draw_id', $draw->id)->where('user_id', $user->id)->count())->toBe(3);
    expect($request->refresh()->status)->toBe('fulfilled');
});

it('creates tickets with correct ticket numbers', function () {
    Mail::fake();

    $draw = Draw::factory()->create();
    $user = User::factory()->create();
    $request = TicketPurchaseRequest::factory()->create([
        'draw_id' => $draw->id,
        'user_id' => $user->id,
        'quantity' => 3,
        'pricing_tier' => ['quantity' => 3, 'price' => 3.00],
        'status' => 'pending',
    ]);

    $expectedFirst = (int) ($draw->id.str_pad('1', 5, '0', STR_PAD_LEFT));
    $expectedSecond = (int) ($draw->id.str_pad('2', 5, '0', STR_PAD_LEFT));
    $expectedThird = (int) ($draw->id.str_pad('3', 5, '0', STR_PAD_LEFT));

    Livewire::actingAs($this->admin)
        ->test(ListTicketPurchaseRequests::class)
        ->callTableAction('confirm_payment', $request);

    $tickets = DrawTicket::where('draw_id', $draw->id)->where('user_id', $user->id)->get();
    expect($tickets->pluck('ticket_number')->toArray())->toBe([$expectedFirst, $expectedSecond, $expectedThird]);
});

it('creates tickets with correct amount_paid', function () {
    Mail::fake();

    $draw = Draw::factory()->create();
    $user = User::factory()->create();
    $request = TicketPurchaseRequest::factory()->create([
        'draw_id' => $draw->id,
        'user_id' => $user->id,
        'quantity' => 3,
        'pricing_tier' => ['quantity' => 5, 'price' => 5.00],
        'status' => 'pending',
    ]);

    Livewire::actingAs($this->admin)
        ->test(ListTicketPurchaseRequests::class)
        ->callTableAction('confirm_payment', $request);

    $tickets = DrawTicket::where('draw_id', $draw->id)->where('user_id', $user->id)->get();
    $expectedPricePerTicket = 5.00 / 5; // $1.00 per ticket

    foreach ($tickets as $ticket) {
        expect((float) $ticket->amount_paid)->toBe($expectedPricePerTicket);
    }
});

it('sends confirmation email when payment confirmed', function () {
    Mail::fake();

    $draw = Draw::factory()->create();
    $user = User::factory()->create();
    $request = TicketPurchaseRequest::factory()->create([
        'draw_id' => $draw->id,
        'user_id' => $user->id,
        'quantity' => 3,
        'pricing_tier' => ['quantity' => 3, 'price' => 3.00],
        'status' => 'pending',
    ]);

    Livewire::actingAs($this->admin)
        ->test(ListTicketPurchaseRequests::class)
        ->callTableAction('confirm_payment', $request);

    Mail::assertQueued(TicketRegistrationConfirmation::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email);
    });
});

it('updates request status to fulfilled after payment confirmation', function () {
    Mail::fake();

    $request = TicketPurchaseRequest::factory()->create(['status' => 'pending']);

    Livewire::actingAs($this->admin)
        ->test(ListTicketPurchaseRequests::class)
        ->callTableAction('confirm_payment', $request);

    expect($request->refresh()->status)->toBe('fulfilled');
});

it('shows success notification after payment confirmation', function () {
    Mail::fake();

    $request = TicketPurchaseRequest::factory()->create(['status' => 'pending']);

    Livewire::actingAs($this->admin)
        ->test(ListTicketPurchaseRequests::class)
        ->callTableAction('confirm_payment', $request)
        ->assertNotified();
});

it('does not show confirm payment action for fulfilled requests', function () {
    $request = TicketPurchaseRequest::factory()->fulfilled()->create();

    Livewire::actingAs($this->admin)
        ->test(ListTicketPurchaseRequests::class)
        ->assertTableActionHidden('confirm_payment', $request);
});

it('does not show confirm payment action for cancelled requests', function () {
    $request = TicketPurchaseRequest::factory()->cancelled()->create();

    Livewire::actingAs($this->admin)
        ->test(ListTicketPurchaseRequests::class)
        ->assertTableActionHidden('confirm_payment', $request);
});

// Cancel Action Tests
it('cancels pending request', function () {
    $request = TicketPurchaseRequest::factory()->create(['status' => 'pending']);

    Livewire::actingAs($this->admin)
        ->test(ListTicketPurchaseRequests::class)
        ->callTableAction('cancel', $request);

    expect($request->refresh()->status)->toBe('cancelled');
});

it('shows success notification after cancellation', function () {
    $request = TicketPurchaseRequest::factory()->create(['status' => 'pending']);

    Livewire::actingAs($this->admin)
        ->test(ListTicketPurchaseRequests::class)
        ->callTableAction('cancel', $request)
        ->assertNotified();
});

it('does not show cancel action for fulfilled requests', function () {
    $request = TicketPurchaseRequest::factory()->fulfilled()->create();

    Livewire::actingAs($this->admin)
        ->test(ListTicketPurchaseRequests::class)
        ->assertTableActionHidden('cancel', $request);
});

it('does not show cancel action for cancelled requests', function () {
    $request = TicketPurchaseRequest::factory()->cancelled()->create();

    Livewire::actingAs($this->admin)
        ->test(ListTicketPurchaseRequests::class)
        ->assertTableActionHidden('cancel', $request);
});

it('shows view action for all requests', function () {
    $pendingRequest = TicketPurchaseRequest::factory()->create(['status' => 'pending']);
    $fulfilledRequest = TicketPurchaseRequest::factory()->fulfilled()->create();
    $cancelledRequest = TicketPurchaseRequest::factory()->cancelled()->create();

    Livewire::actingAs($this->admin)
        ->test(ListTicketPurchaseRequests::class)
        ->assertTableActionVisible('view', $pendingRequest)
        ->assertTableActionVisible('view', $fulfilledRequest)
        ->assertTableActionVisible('view', $cancelledRequest);
});

// Integration Tests
it('creates multiple tickets with incrementing ticket numbers', function () {
    Mail::fake();

    $draw = Draw::factory()->create();
    $user = User::factory()->create();

    // Create first request
    $request1 = TicketPurchaseRequest::factory()->create([
        'draw_id' => $draw->id,
        'user_id' => $user->id,
        'quantity' => 2,
        'pricing_tier' => ['quantity' => 2, 'price' => 2.00],
        'status' => 'pending',
    ]);

    Livewire::actingAs($this->admin)
        ->test(ListTicketPurchaseRequests::class)
        ->callTableAction('confirm_payment', $request1);

    // Create second request
    $request2 = TicketPurchaseRequest::factory()->create([
        'draw_id' => $draw->id,
        'user_id' => $user->id,
        'quantity' => 3,
        'pricing_tier' => ['quantity' => 3, 'price' => 3.00],
        'status' => 'pending',
    ]);

    Livewire::actingAs($this->admin)
        ->test(ListTicketPurchaseRequests::class)
        ->callTableAction('confirm_payment', $request2);

    $allTickets = DrawTicket::where('draw_id', $draw->id)->where('user_id', $user->id)->orderBy('ticket_number')->get();

    $expectedFirst = (int) ($draw->id.str_pad('1', 5, '0', STR_PAD_LEFT));
    $expectedLast = (int) ($draw->id.str_pad('5', 5, '0', STR_PAD_LEFT));

    expect($allTickets->count())->toBe(5);
    expect($allTickets->first()->ticket_number)->toBe($expectedFirst);
    expect($allTickets->last()->ticket_number)->toBe($expectedLast);
});

it('sends email with correct ticket information', function () {
    Mail::fake();

    $draw = Draw::factory()->create(['name' => 'Test Draw']);
    $user = User::factory()->create(['email' => 'test@example.com']);
    $request = TicketPurchaseRequest::factory()->create([
        'draw_id' => $draw->id,
        'user_id' => $user->id,
        'quantity' => 3,
        'pricing_tier' => ['quantity' => 3, 'price' => 3.00],
        'status' => 'pending',
    ]);

    Livewire::actingAs($this->admin)
        ->test(ListTicketPurchaseRequests::class)
        ->callTableAction('confirm_payment', $request);

    Mail::assertQueued(TicketRegistrationConfirmation::class, function ($mail) use ($draw) {
        return $mail->hasTo('test@example.com')
            && $mail->draw->id === $draw->id
            && $mail->tickets->count() === 3
            && $mail->totalAmount === 3.0;
    });
});

it('handles complex pricing tier calculations correctly', function () {
    Mail::fake();

    $draw = Draw::factory()->create();
    $user = User::factory()->create();

    // Buying 5 tickets from a "10 tickets for $5" tier
    $request = TicketPurchaseRequest::factory()->create([
        'draw_id' => $draw->id,
        'user_id' => $user->id,
        'quantity' => 5,
        'pricing_tier' => ['quantity' => 10, 'price' => 5.00],
        'status' => 'pending',
    ]);

    Livewire::actingAs($this->admin)
        ->test(ListTicketPurchaseRequests::class)
        ->callTableAction('confirm_payment', $request);

    $tickets = DrawTicket::where('draw_id', $draw->id)->where('user_id', $user->id)->get();
    $expectedPricePerTicket = 5.00 / 10; // $0.50 per ticket
    $expectedTotal = $expectedPricePerTicket * 5; // $2.50 total

    expect($tickets->count())->toBe(5);

    foreach ($tickets as $ticket) {
        expect((float) $ticket->amount_paid)->toBe($expectedPricePerTicket);
    }

    expect($tickets->sum('amount_paid'))->toBe($expectedTotal);
});
