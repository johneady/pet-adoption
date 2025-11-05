<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Setting>
 */
class SettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $key = fake()->unique()->slug(2);

        return [
            'key' => $key,
            'value' => fake()->sentence(),
            'type' => 'string',
            'group' => 'general',
            'description' => fake()->optional()->sentence(),
        ];
    }

    public function boolean(): static
    {
        return $this->state(fn (array $attributes) => [
            'value' => fake()->boolean() ? '1' : '0',
            'type' => 'boolean',
        ]);
    }

    public function integer(): static
    {
        return $this->state(fn (array $attributes) => [
            'value' => (string) fake()->numberBetween(1, 1000),
            'type' => 'integer',
        ]);
    }

    public function float(): static
    {
        return $this->state(fn (array $attributes) => [
            'value' => (string) fake()->randomFloat(2, 0, 1000),
            'type' => 'float',
        ]);
    }

    public function json(): static
    {
        return $this->state(fn (array $attributes) => [
            'value' => json_encode([
                'key1' => fake()->word(),
                'key2' => fake()->word(),
            ]),
            'type' => 'json',
        ]);
    }

    public function group(string $group): static
    {
        return $this->state(fn (array $attributes) => [
            'group' => $group,
        ]);
    }
}
