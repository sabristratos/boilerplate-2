<?php

declare(strict_types=1);

namespace Database\Factories;

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
            'draft_name' => null,
            'settings' => null,
            'draft_settings' => null,
            'elements' => null,
            'draft_elements' => null,
            'last_draft_at' => null,
        ];
    }

    /**
     * Create a form with draft data.
     */
    public function withDraft(): static
    {
        return $this->state(fn (array $attributes): array => [
            'draft_name' => [
                'en' => fake()->words(3, true) . ' (Draft)',
                'fr' => fake()->words(3, true) . ' (Brouillon)',
            ],
            'draft_elements' => [
                [
                    'id' => 'field_1',
                    'type' => 'text',
                    'order' => 0,
                    'properties' => ['label' => 'Draft Field'],
                    'validation' => ['rules' => []],
                ],
            ],
            'draft_settings' => ['backgroundColor' => '#f0f0f0'],
            'last_draft_at' => now(),
        ]);
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
                    'type' => 'text',
                    'order' => 0,
                    'properties' => ['label' => 'Name'],
                    'validation' => ['rules' => ['required']],
                ],
                [
                    'id' => 'field_2',
                    'type' => 'email',
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
            'draft_name' => null,
        ]);
    }
}
