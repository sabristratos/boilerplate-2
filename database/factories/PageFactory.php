<?php

namespace Database\Factories;

use App\Enums\PublishStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Page>
 */
class PageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(3);

        return [
            'title' => [
                'en' => $title,
            ],
            'slug' => Str::slug($title),
            'status' => PublishStatus::DRAFT,
            'meta_title' => [
                'en' => fake()->sentence(5),
            ],
            'meta_description' => [
                'en' => fake()->sentence(10),
            ],
            'no_index' => false,
        ];
    }

    /**
     * Indicate that the page is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => PublishStatus::PUBLISHED,
        ]);
    }

    /**
     * Indicate that the page should not be indexed.
     */
    public function noIndex(): static
    {
        return $this->state(fn (array $attributes): array => [
            'no_index' => true,
        ]);
    }
}
