<?php

namespace Database\Factories;

use App\Enums\FormType;
use App\Enums\QuestionType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FormQuestion>
 */
class FormQuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(QuestionType::cases());

        return [
            'form_type' => fake()->randomElement(FormType::cases()),
            'label' => fake()->sentence(4),
            'type' => $type,
            'options' => $type === QuestionType::Dropdown ? fake()->words(4) : null,
            'is_required' => fake()->boolean(70),
            'sort_order' => fake()->numberBetween(0, 100),
            'is_active' => fake()->boolean(90),
        ];
    }

    /**
     * Indicate that the question is for adoption forms.
     */
    public function adoption(): static
    {
        return $this->state(fn (array $attributes) => [
            'form_type' => FormType::Adoption,
        ]);
    }

    /**
     * Indicate that the question is for fostering forms.
     */
    public function fostering(): static
    {
        return $this->state(fn (array $attributes) => [
            'form_type' => FormType::Fostering,
        ]);
    }

    /**
     * Indicate that the question is a dropdown with options.
     */
    public function dropdown(array $options = []): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => QuestionType::Dropdown,
            'options' => $options ?: ['Option 1', 'Option 2', 'Option 3'],
        ]);
    }

    /**
     * Indicate that the question is required.
     */
    public function required(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_required' => true,
        ]);
    }

    /**
     * Indicate that the question is optional.
     */
    public function optional(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_required' => false,
        ]);
    }

    /**
     * Indicate that the question is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the question is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
