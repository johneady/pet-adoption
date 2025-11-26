<?php

use App\Models\User;

test('banned users cannot log in', function () {
    $user = User::factory()->banned()->create([
        'password' => 'password',
    ]);

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors(['email' => 'Your account has been locked. Please contact support for assistance.']);
    $this->assertGuest();
});

test('non-banned users can still log in', function () {
    $user = User::factory()->withoutTwoFactor()->create([
        'password' => 'password',
        'banned' => false,
    ]);

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});

test('banned users with correct password still cannot log in', function () {
    $user = User::factory()->banned()->create([
        'password' => 'password',
    ]);

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('unbanning a user allows them to log in again', function () {
    $user = User::factory()->banned()->withoutTwoFactor()->create([
        'password' => 'password',
    ]);

    // First, confirm they cannot log in
    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();

    // Unban the user
    $user->update(['banned' => false]);

    // Now they should be able to log in
    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});
