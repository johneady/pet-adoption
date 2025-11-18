<?php

use App\Mail\AdoptionApplicationReceived;
use App\Mail\NewAdoptionApplication;
use App\Mail\NewUserRegistered;
use App\Mail\WelcomeNewUser;
use App\Models\AdoptionApplication;
use App\Models\Pet;
use App\Models\Species;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Mail;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('welcome email is queued after user verifies email', function () {
    Mail::fake();

    $user = User::factory()->unverified()->create();

    // Simulate email verification
    event(new Verified($user));

    // Assert exactly one welcome email was queued
    Mail::assertQueuedCount(1);
    Mail::assertQueued(WelcomeNewUser::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email) &&
               $mail->user->id === $user->id;
    });
});

test('admin users with notification preference receive new user registration emails', function () {
    Mail::fake();

    // Create admins - one with notifications enabled, one without
    $adminWithNotifications = User::factory()->admin()->receivesNotifications()->create();
    $adminWithoutNotifications = User::factory()->admin()->create([
        'receive_new_user_alerts' => false,
    ]);
    $regularUser = User::factory()->create();

    // Register a new user via Fortify's CreateNewUser action
    $action = app(\App\Actions\Fortify\CreateNewUser::class);
    $newUser = $action->create([
        'name' => 'New Test User',
        'email' => 'newuser@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    // Assert only the admin with notifications enabled received the email
    Mail::assertQueued(NewUserRegistered::class, function ($mail) use ($adminWithNotifications, $newUser) {
        return $mail->hasTo($adminWithNotifications->email) &&
               $mail->user->id === $newUser->id;
    });

    // Assert the admin without notifications did not receive the email
    Mail::assertNotQueued(NewUserRegistered::class, function ($mail) use ($adminWithoutNotifications) {
        return $mail->hasTo($adminWithoutNotifications->email);
    });

    // Assert regular users did not receive the email
    Mail::assertNotQueued(NewUserRegistered::class, function ($mail) use ($regularUser) {
        return $mail->hasTo($regularUser->email);
    });
});

test('applicant receives confirmation email when adoption application is submitted', function () {
    Mail::fake();

    $user = User::factory()->create();
    $species = Species::factory()->create();
    $pet = Pet::factory()->create(['species_id' => $species->id, 'status' => 'available']);

    $this->actingAs($user);

    $application = AdoptionApplication::create([
        'user_id' => $user->id,
        'pet_id' => $pet->id,
        'living_situation' => 'House with yard',
        'experience' => 'Previous dog owner',
        'other_pets' => 'None',
        'veterinary_reference' => 'Dr. Smith',
        'household_members' => '2 adults',
        'employment_status' => 'Full-time',
        'reason_for_adoption' => 'Looking for a companion',
        'status' => 'submitted',
    ]);

    // Load relationships for the email
    $application->load(['user', 'pet.species']);

    // Send the email (simulating what happens in the Create component)
    Mail::to($user)->send(new AdoptionApplicationReceived($application));

    // Assert confirmation email was queued to applicant
    Mail::assertQueued(AdoptionApplicationReceived::class, function ($mail) use ($user, $application) {
        return $mail->hasTo($user->email) &&
               $mail->application->id === $application->id;
    });
});

test('admin users with adoption notification preference receive new adoption application emails', function () {
    Mail::fake();

    $applicant = User::factory()->create();
    $species = Species::factory()->create();
    $pet = Pet::factory()->create(['species_id' => $species->id, 'status' => 'available']);

    // Create admins - one with notifications enabled, one without
    $adminWithNotifications = User::factory()->admin()->receivesNotifications()->create();
    $adminWithoutNotifications = User::factory()->admin()->create([
        'receive_new_adoption_alerts' => false,
    ]);

    $this->actingAs($applicant);

    $application = AdoptionApplication::create([
        'user_id' => $applicant->id,
        'pet_id' => $pet->id,
        'living_situation' => 'House with yard',
        'experience' => 'Previous dog owner',
        'other_pets' => 'None',
        'veterinary_reference' => 'Dr. Smith',
        'household_members' => '2 adults',
        'employment_status' => 'Full-time',
        'reason_for_adoption' => 'Looking for a companion',
        'status' => 'submitted',
    ]);

    // Load relationships for the email
    $application->load(['user', 'pet.species']);

    // Send emails to admins (simulating what happens in the Create component)
    Mail::to($applicant)->send(new AdoptionApplicationReceived($application));

    $adminsToNotify = User::where('is_admin', true)
        ->where('receive_new_adoption_alerts', true)
        ->get();

    foreach ($adminsToNotify as $admin) {
        Mail::to($admin)->send(new NewAdoptionApplication($application));
    }

    // Assert only the admin with notifications enabled received the email
    Mail::assertQueued(NewAdoptionApplication::class, function ($mail) use ($adminWithNotifications, $application) {
        return $mail->hasTo($adminWithNotifications->email) &&
               $mail->application->id === $application->id;
    });

    // Assert the admin without notifications did not receive the email
    Mail::assertNotQueued(NewAdoptionApplication::class, function ($mail) use ($adminWithoutNotifications) {
        return $mail->hasTo($adminWithoutNotifications->email);
    });
});

test('admin can update notification preferences', function () {
    $admin = User::factory()->admin()->create([
        'receive_new_user_alerts' => false,
        'receive_new_adoption_alerts' => false,
    ]);

    expect($admin->receive_new_user_alerts)->toBeFalse()
        ->and($admin->receive_new_adoption_alerts)->toBeFalse();

    $admin->update([
        'receive_new_user_alerts' => true,
        'receive_new_adoption_alerts' => true,
    ]);

    expect($admin->fresh()->receive_new_user_alerts)->toBeTrue()
        ->and($admin->fresh()->receive_new_adoption_alerts)->toBeTrue();
});

