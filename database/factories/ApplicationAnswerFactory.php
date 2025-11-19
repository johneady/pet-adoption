<?php

namespace Database\Factories;

use App\Models\AdoptionApplication;
use App\Models\FormQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ApplicationAnswer>
 */
class ApplicationAnswerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $question = FormQuestion::factory()->make();

        return [
            'answerable_type' => AdoptionApplication::class,
            'answerable_id' => AdoptionApplication::factory(),
            'question_snapshot' => $question->toSnapshot(),
            'answer' => fake()->sentence(),
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }

    /**
     * Use a specific question for the answer.
     */
    public function forQuestion(FormQuestion $question): static
    {
        return $this->state(fn (array $attributes) => [
            'question_snapshot' => $question->toSnapshot(),
            'sort_order' => $question->sort_order,
        ]);
    }

    /**
     * Use a specific application for the answer.
     */
    public function forApplication(AdoptionApplication $application): static
    {
        return $this->state(fn (array $attributes) => [
            'answerable_type' => AdoptionApplication::class,
            'answerable_id' => $application->id,
        ]);
    }
}
