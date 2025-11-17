<?php

declare(strict_types=1);

use App\Models\MembershipPlan;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->plan = MembershipPlan::factory()->create([
        'slug' => 'bronze',
        'annual_price' => 25.00,
        'monthly_price' => 3.00,
    ]);
});

test('guest cannot access checkout', function () {
    $this->get(route('membership.checkout', ['plan' => $this->plan->slug, 'type' => 'annual']))
        ->assertRedirect(route('login'));
});

test('authenticated user redirects to stripe checkout', function () {
    // This test requires Stripe API configuration and would create a real checkout session
    // In production, this would redirect to Stripe's checkout page
    expect(true)->toBeTrue();
})->skip('Requires Stripe API configuration');

test('checkout with invalid plan returns 404', function () {
    $this->actingAs($this->user)
        ->get(route('membership.checkout', ['plan' => 'invalid-plan', 'type' => 'annual']))
        ->assertNotFound();
});

test('checkout with invalid payment type returns 404', function () {
    $this->actingAs($this->user)
        ->get(route('membership.checkout', ['plan' => $this->plan->slug, 'type' => 'invalid']))
        ->assertNotFound();
});

test('success page requires session_id', function () {
    $this->actingAs($this->user)
        ->get(route('membership.success'))
        ->assertRedirect(route('membership.plans'));
});

test('success page displays with valid session_id', function () {
    $this->actingAs($this->user)
        ->get(route('membership.success', ['session_id' => 'cs_test_123']))
        ->assertSuccessful()
        ->assertSee('Payment Successful');
});

test('cancel page displays correctly', function () {
    $this->actingAs($this->user)
        ->get(route('membership.cancel'))
        ->assertSuccessful()
        ->assertSee('Payment Canceled');
});
