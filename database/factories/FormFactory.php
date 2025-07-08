<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\FormStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Form>
 */
class FormFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => [
                'en' => fake()->words(3, true),
                'fr' => fake()->words(3, true),
            ],
            'settings' => null,
            'elements' => null,
            'status' => FormStatus::DRAFT,
        ];
    }

    /**
     * Create a form with published data.
     */
    public function withElements(): static
    {
        return $this->state(fn (array $attributes): array => [
            'elements' => [
                [
                    'id' => 'field_1',
                    'type' => FormElementType::TEXT->value,
                    'order' => 0,
                    'properties' => ['label' => 'Name'],
                    'validation' => ['rules' => ['required']],
                ],
                [
                    'id' => 'field_2',
                    'type' => FormElementType::EMAIL->value,
                    'order' => 1,
                    'properties' => ['label' => 'Email'],
                    'validation' => ['rules' => ['required', 'email']],
                ],
            ],
            'settings' => ['backgroundColor' => '#ffffff'],
        ]);
    }

    /**
     * Create a form with only the 'en' locale for name fields.
     */
    public function onlyEnLocale(): static
    {
        return $this->state(fn (array $attributes): array => [
            'name' => [
                'en' => fake()->words(3, true),
            ],
        ]);
    }

    /**
     * Indicate that the form is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => FormStatus::PUBLISHED,
        ]);
    }

    /**
     * Indicate that the form is archived.
     */
    public function archived(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => FormStatus::ARCHIVED,
        ]);
    }
}
