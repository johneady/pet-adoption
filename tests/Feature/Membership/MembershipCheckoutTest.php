<?php

declare(strict_types=1);

use App\Models\MembershipPlan;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->plan = MembershipPlan::factory()->create([
        'slug' => 'bronze',
        'price' => 25.00,
    ]);
});

test('guest cannot access checkout', function () {
    $this->get(route('membership.checkout', ['plan' => $this->plan->slug]))
        ->assertRedirect(route('login'));
});

test('checkout page displays for authenticated user', function () {
    $this->actingAs($this->user)
        ->get(route('membership.checkout', ['plan' => $this->plan->slug]))
        ->assertSuccessful()
        ->assertSee('Checkout')
        ->assertSee($this->plan->name);
});

test('checkout with invalid plan returns 404', function () {
    $this->actingAs($this->user)
        ->get(route('membership.checkout', ['plan' => 'invalid-plan']))
        ->assertNotFound();
});

test('success page redirects without session_id', function () {
    $this->actingAs($this->user)
        ->get(route('membership.success'))
        ->assertRedirect(route('membership.plans'));
});

test('success page displays with session_id', function () {
    $this->actingAs($this->user)
        ->get(route('membership.success', ['session_id' => 'test_session_123']))
        ->assertSuccessful()
        ->assertSee('Payment Successful');
});

test('cancel page displays correctly', function () {
    $this->actingAs($this->user)
        ->get(route('membership.cancel'))
        ->assertSuccessful()
        ->assertSee('Payment Canceled');
});
