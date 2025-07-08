<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ContentBlock;
use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ContentBlock>
 */
class ContentBlockFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ContentBlock::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'page_id' => Page::factory(),
            'type' => fake()->randomElement([
                'content-area',
                'call-to-action',
                'contact',
                'hero',
                'image-gallery',
                'testimonials',
            ]),
            'data' => $this->getDefaultData(),
            'order' => fake()->numberBetween(1, 100),
            'visible' => fake()->boolean(80), // 80% chance of being visible
        ];
    }

    /**
     * Get default data based on block type.
     *
     * @return array<string, mixed>
     */
    protected function getDefaultData(): array
    {
        $type = fake()->randomElement([
            'content-area',
            'call-to-action',
            'contact',
            'hero',
            'image-gallery',
            'testimonials',
        ]);

        return match ($type) {
            'content-area' => [
                'content' => ['en' => fake()->paragraph()],
                'background_color' => fake()->randomElement(['bg-white', 'bg-gray-100', 'bg-blue-50']),
                'text_color' => fake()->randomElement(['text-gray-900', 'text-gray-800', 'text-blue-900']),
            ],
            'call-to-action' => [
                'title' => ['en' => fake()->sentence()],
                'content' => ['en' => fake()->paragraph()],
                'button_text' => ['en' => fake()->words(2, true)],
                'button_url' => fake()->url(),
                'background_color' => fake()->randomElement(['bg-blue-600', 'bg-green-600', 'bg-purple-600']),
                'text_color' => 'text-white',
            ],
            'contact' => [
                'title' => ['en' => fake()->sentence()],
                'description' => ['en' => fake()->paragraph()],
                'form_id' => fake()->numberBetween(1, 10),
            ],
            'hero' => [
                'title' => ['en' => fake()->sentence()],
                'subtitle' => ['en' => fake()->sentence()],
                'background_image' => fake()->imageUrl(1920, 1080),
                'overlay_color' => fake()->randomElement(['bg-black/50', 'bg-blue-900/50', 'bg-gray-900/50']),
            ],
            'image-gallery' => [
                'title' => ['en' => fake()->sentence()],
                'images' => [
                    fake()->imageUrl(800, 600),
                    fake()->imageUrl(800, 600),
                    fake()->imageUrl(800, 600),
                ],
                'columns' => fake()->randomElement([2, 3, 4]),
            ],
            'testimonials' => [
                'title' => ['en' => fake()->sentence()],
                'testimonials' => [
                    [
                        'name' => fake()->name(),
                        'position' => fake()->jobTitle(),
                        'company' => fake()->company(),
                        'content' => ['en' => fake()->paragraph()],
                        'rating' => fake()->numberBetween(4, 5),
                        'avatar' => fake()->imageUrl(100, 100),
                    ],
                    [
                        'name' => fake()->name(),
                        'position' => fake()->jobTitle(),
                        'company' => fake()->company(),
                        'content' => ['en' => fake()->paragraph()],
                        'rating' => fake()->numberBetween(4, 5),
                        'avatar' => fake()->imageUrl(100, 100),
                    ],
                ],
            ],
            default => [
                'content' => ['en' => fake()->paragraph()],
            ],
        };
    }

    /**
     * Indicate that the block is a content area.
     */
    public function contentArea(): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => 'content-area',
            'data' => [
                'content' => ['en' => fake()->paragraph()],
                'background_color' => fake()->randomElement(['bg-white', 'bg-gray-100', 'bg-blue-50']),
                'text_color' => fake()->randomElement(['text-gray-900', 'text-gray-800', 'text-blue-900']),
            ],
        ]);
    }

    /**
     * Indicate that the block is a call to action.
     */
    public function callToAction(): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => 'call-to-action',
            'data' => [
                'title' => ['en' => fake()->sentence()],
                'content' => ['en' => fake()->paragraph()],
                'button_text' => ['en' => fake()->words(2, true)],
                'button_url' => fake()->url(),
                'background_color' => fake()->randomElement(['bg-blue-600', 'bg-green-600', 'bg-purple-600']),
                'text_color' => 'text-white',
            ],
        ]);
    }

    /**
     * Indicate that the block is a contact form.
     */
    public function contact(): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => 'contact',
            'data' => [
                'title' => ['en' => fake()->sentence()],
                'description' => ['en' => fake()->paragraph()],
                'form_id' => fake()->numberBetween(1, 10),
            ],
        ]);
    }

    /**
     * Indicate that the block is a hero section.
     */
    public function hero(): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => 'hero',
            'data' => [
                'title' => ['en' => fake()->sentence()],
                'subtitle' => ['en' => fake()->sentence()],
                'background_image' => fake()->imageUrl(1920, 1080),
                'overlay_color' => fake()->randomElement(['bg-black/50', 'bg-blue-900/50', 'bg-gray-900/50']),
            ],
        ]);
    }

    /**
     * Indicate that the block is an image gallery.
     */
    public function imageGallery(): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => 'image-gallery',
            'data' => [
                'title' => ['en' => fake()->sentence()],
                'images' => [
                    fake()->imageUrl(800, 600),
                    fake()->imageUrl(800, 600),
                    fake()->imageUrl(800, 600),
                ],
                'columns' => fake()->randomElement([2, 3, 4]),
            ],
        ]);
    }

    /**
     * Indicate that the block is testimonials.
     */
    public function testimonials(): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => 'testimonials',
            'data' => [
                'title' => ['en' => fake()->sentence()],
                'testimonials' => [
                    [
                        'name' => fake()->name(),
                        'position' => fake()->jobTitle(),
                        'company' => fake()->company(),
                        'content' => ['en' => fake()->paragraph()],
                        'rating' => fake()->numberBetween(4, 5),
                        'avatar' => fake()->imageUrl(100, 100),
                    ],
                    [
                        'name' => fake()->name(),
                        'position' => fake()->jobTitle(),
                        'company' => fake()->company(),
                        'content' => ['en' => fake()->paragraph()],
                        'rating' => fake()->numberBetween(4, 5),
                        'avatar' => fake()->imageUrl(100, 100),
                    ],
                ],
            ],
        ]);
    }

    /**
     * Indicate that the block is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes): array => [
            // No changes needed, default is published
        ]);
    }
}
