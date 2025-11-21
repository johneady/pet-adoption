<?php

declare(strict_types=1);

use App\Livewire\Draws\PurchaseTickets;
use App\Mail\TicketPurchaseRequest as TicketPurchaseRequestMail;
use App\Models\Draw;
use App\Models\TicketPurchaseRequest;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

uses()->group('draws', 'ticket-purchase');

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->admin = User::factory()->create([
        'is_admin' => true,
        'receive_ticket_purchase_alerts' => true,
    ]);
});

it('can view the purchase tickets page for an active draw', function () {
    $draw = Draw::factory()->active()->create();

    $response = $this->actingAs($this->user)->get(route('draws.purchase', ['draw' => $draw->id]));

    $response->assertSuccessful();
    $response->assertSee('Purchase Tickets');
    $response->assertSee($draw->name);
});

it('cannot view purchase tickets page for inactive draw', function () {
    $draw = Draw::factory()->upcoming()->create();

    $this->actingAs($this->user)
        ->get(route('draws.purchase', ['draw' => $draw->id]))
        ->assertNotFound();
});

it('cannot view purchase tickets page when not authenticated', function () {
    $draw = Draw::factory()->active()->create();

    $this->get(route('draws.purchase', ['draw' => $draw->id]))
        ->assertRedirect(route('login'));
});

it('can submit a ticket purchase request', function () {
    Mail::fake();

    $draw = Draw::factory()->active()->create();
    $pricingTier = ['quantity' => 3, 'price' => 3.00];

    Livewire::actingAs($this->user)
        ->test(PurchaseTickets::class, ['draw' => $draw])
        ->set('selectedPricingTier', json_encode($pricingTier))
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirect(route('draws.index'));

    expect(TicketPurchaseRequest::count())->toBe(1);

    $request = TicketPurchaseRequest::first();
    expect($request->draw_id)->toBe($draw->id);
    expect($request->user_id)->toBe($this->user->id);
    expect($request->quantity)->toBe(3);
    expect($request->pricing_tier['quantity'])->toBe($pricingTier['quantity']);
    expect((float) $request->pricing_tier['price'])->toBe($pricingTier['price']);
    expect($request->status)->toBe('pending');

    Mail::assertQueued(TicketPurchaseRequestMail::class, function ($mail) {
        return $mail->hasTo($this->admin->email);
    });
});

it('can submit a ticket purchase request for the first ticket option', function () {
    Mail::fake();

    $draw = Draw::factory()->active()->create();
    $firstTier = $draw->ticket_price_tiers[0];

    Livewire::actingAs($this->user)
        ->test(PurchaseTickets::class, ['draw' => $draw])
        ->set('selectedPricingTier', json_encode($firstTier))
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirect(route('draws.index'));

    expect(TicketPurchaseRequest::count())->toBe(1);

    $request = TicketPurchaseRequest::first();
    expect($request->draw_id)->toBe($draw->id);
    expect($request->user_id)->toBe($this->user->id);
    expect($request->quantity)->toBe(1);
    expect($request->pricing_tier['quantity'])->toBe(1);
    expect((float) $request->pricing_tier['price'])->toBe(1.00);
    expect($request->status)->toBe('pending');
});

it('requires selecting a pricing tier', function () {
    $draw = Draw::factory()->active()->create();

    Livewire::actingAs($this->user)
        ->test(PurchaseTickets::class, ['draw' => $draw])
        ->call('submit')
        ->assertHasErrors(['selectedPricingTier']);
});

it('only sends email to admins with notification preference enabled', function () {
    Mail::fake();

    $adminWithPreference = User::factory()->create([
        'is_admin' => true,
        'receive_ticket_purchase_alerts' => true,
    ]);

    $adminWithoutPreference = User::factory()->create([
        'is_admin' => true,
        'receive_ticket_purchase_alerts' => false,
    ]);

    $draw = Draw::factory()->active()->create();
    $pricingTier = ['quantity' => 1, 'price' => 1.00];

    Livewire::actingAs($this->user)
        ->test(PurchaseTickets::class, ['draw' => $draw])
        ->set('selectedPricingTier', json_encode($pricingTier))
        ->call('submit');

    Mail::assertQueued(TicketPurchaseRequestMail::class, 2); // 2 admins with preference (including $this->admin)
    Mail::assertQueued(TicketPurchaseRequestMail::class, function ($mail) use ($adminWithPreference) {
        return $mail->hasTo($adminWithPreference->email);
    });
    Mail::assertNotQueued(TicketPurchaseRequestMail::class, function ($mail) use ($adminWithoutPreference) {
        return $mail->hasTo($adminWithoutPreference->email);
    });
});

it('displays purchase button on draws index page for authenticated users', function () {
    $draw = Draw::factory()->active()->create();

    $response = $this->actingAs($this->user)->get(route('draws.index'));

    $response->assertSuccessful();
    $response->assertSee('Purchase Tickets');
});

it('displays login prompt on draws index page for guests', function () {
    $draw = Draw::factory()->active()->create();

    $response = $this->get(route('draws.index'));

    $response->assertSuccessful();
    $response->assertSee('Log in to purchase tickets');
    $response->assertSee('Log In');
});
