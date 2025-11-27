<?php

namespace Database\Factories;

use Database\Factories\Concerns\CopiesSeederImages;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    use CopiesSeederImages;

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
            'phone' => fake()->numerify('###-####'),
            'address' => fake()->address(),
            'profile_picture' => null,
            'timezone' => 'America/Toronto',
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
            'is_admin' => false,
            'banned' => false,
            'receive_new_user_alerts' => true,
            'receive_new_adoption_alerts' => true,
            'receive_draw_result_alerts' => true,
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
     * Indicate that the model has two-factor authentication configured.
     */
    public function withTwoFactor(): static
    {
        return $this->state(fn (array $attributes) => [
            'two_factor_secret' => encrypt('test-secret'),
            'two_factor_recovery_codes' => encrypt(json_encode(['code1', 'code2'])),
            'two_factor_confirmed_at' => now(),
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
            'receive_draw_result_alerts' => true,
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

    /**
     * Indicate that the user has a complete profile (with phone and address).
     */
    public function withCompleteProfile(): static
    {
        return $this->state(fn (array $attributes) => [
            'phone' => fake()->numerify('###-####'),
            'address' => fake()->address(),
        ]);
    }

    /**
     * Indicate that the user is banned.
     */
    public function banned(): static
    {
        return $this->state(fn (array $attributes) => [
            'banned' => true,
        ]);
    }

    /**
     * Indicate that the user has a profile picture.
     */
    public function withProfilePicture(): static
    {
        return $this->state(fn (array $attributes) => [
            'profile_picture' => $this->copyRandomSeederImage('profile_samples', 'profile-pictures'),
        ]);
    }
}
