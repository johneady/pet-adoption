<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= 'password',
            'remember_token' => Str::random(10),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'profile_picture' => null,
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
            'is_admin' => false,
            'receive_new_user_alerts' => true,
            'receive_new_adoption_alerts' => true,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the model does not have two-factor authentication configured.
     */
    public function withoutTwoFactor(): static
    {
        return $this->state(fn (array $attributes) => [
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ]);
    }

    /**
     * Indicate that the model is an admin user.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_admin' => true,
        ]);
    }

    /**
     * Indicate that the admin receives notification alerts.
     */
    public function receivesNotifications(): static
    {
        return $this->state(fn (array $attributes) => [
            'receive_new_user_alerts' => true,
            'receive_new_adoption_alerts' => true,
        ]);
    }

    /**
     * Indicate that the user has an incomplete profile (no phone or address).
     */
    public function incompleteProfile(): static
    {
        return $this->state(fn (array $attributes) => [
            'phone' => null,
            'address' => null,
        ]);
    }
}
