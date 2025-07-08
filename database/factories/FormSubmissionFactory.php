<?php

namespace Database\Factories;

use App\Models\Form;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FormSubmission>
 */
class FormSubmissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'form_id' => Form::factory(),
            'data' => [
                'name' => fake()->name(),
                'email' => fake()->email(),
                'message' => fake()->paragraph(),
            ],
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
        ];
    }

    /**
     * Create a submission with specific data
     */
    public function withData(array $data): static
    {
        return $this->state(fn (array $attributes): array => [
            'data' => $data,
        ]);
    }

    /**
     * Create a submission for a specific form
     */
    public function forForm(Form $form): static
    {
        return $this->state(fn (array $attributes): array => [
            'form_id' => $form->id,
        ]);
    }
}
