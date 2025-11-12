<?php

use App\Models\User;

test('users are redirected to dashboard after direct login', function () {
    $user = User::factory()->withoutTwoFactor()->create();

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});

test('users are redirected to intended page after login from protected route', function () {
    $user = User::factory()->withoutTwoFactor()->create();

    // Try to access a protected page while not authenticated
    $this->get(route('dashboard'))
        ->assertRedirect(route('login'));

    // Now login
    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    // Should be redirected back to the intended page (dashboard)
    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});

test('users are redirected to intended page after login from application create route', function () {
    $user = User::factory()->withoutTwoFactor()->create();

    // Try to access the application create page while not authenticated
    $this->get(route('applications.create'))
        ->assertRedirect(route('login'));

    // Now login
    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    // Should be redirected back to the intended page (applications.create)
    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('applications.create', absolute: false));

    $this->assertAuthenticated();
});
