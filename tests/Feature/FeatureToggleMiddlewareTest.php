<?php

declare(strict_types=1);

use App\Models\Draw;
use App\Models\MembershipPlan;
use App\Models\Setting;
use App\Models\User;

describe('Draw Feature Toggle', function () {
    test('draws index page is accessible when draws are enabled', function () {
        Setting::set('enable_draws', true, 'boolean', 'application');

        $response = $this->get(route('draws.index'));

        $response->assertSuccessful();
    });

    test('draws index page returns 404 when draws are disabled', function () {
        Setting::set('enable_draws', false, 'boolean', 'application');

        $response = $this->get(route('draws.index'));

        $response->assertNotFound();
    });

    test('draws purchase page is accessible when draws are enabled', function () {
        Setting::set('enable_draws', true, 'boolean', 'application');

        $user = User::factory()->create();
        $draw = Draw::factory()->active()->create();

        $response = $this->actingAs($user)->get(route('draws.purchase', $draw));

        $response->assertSuccessful();
    });

    test('draws purchase page returns 404 when draws are disabled', function () {
        Setting::set('enable_draws', false, 'boolean', 'application');

        $user = User::factory()->create();
        $draw = Draw::factory()->active()->create();

        $response = $this->actingAs($user)->get(route('draws.purchase', $draw));

        $response->assertNotFound();
    });
});

describe('Membership Feature Toggle', function () {
    test('membership plans page is accessible when memberships are enabled', function () {
        Setting::set('enable_memberships', true, 'boolean', 'application');

        $response = $this->get(route('membership.plans'));

        $response->assertSuccessful();
    });

    test('membership plans page returns 404 when memberships are disabled', function () {
        Setting::set('enable_memberships', false, 'boolean', 'application');

        $response = $this->get(route('membership.plans'));

        $response->assertNotFound();
    });

    test('membership checkout page is accessible when memberships are enabled', function () {
        Setting::set('enable_memberships', true, 'boolean', 'application');

        $user = User::factory()->create();
        $plan = MembershipPlan::factory()->create();

        $response = $this->actingAs($user)->get(route('membership.checkout', $plan->slug));

        $response->assertSuccessful();
    });

    test('membership checkout page returns 404 when memberships are disabled', function () {
        Setting::set('enable_memberships', false, 'boolean', 'application');

        $user = User::factory()->create();
        $plan = MembershipPlan::factory()->create();

        $response = $this->actingAs($user)->get(route('membership.checkout', $plan->slug));

        $response->assertNotFound();
    });

    test('membership manage page is accessible when memberships are enabled', function () {
        Setting::set('enable_memberships', true, 'boolean', 'application');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('membership.manage'));

        $response->assertSuccessful();
    });

    test('membership manage page returns 404 when memberships are disabled', function () {
        Setting::set('enable_memberships', false, 'boolean', 'application');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('membership.manage'));

        $response->assertNotFound();
    });
});
