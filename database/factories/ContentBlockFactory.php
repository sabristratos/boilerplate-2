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
            'type' => $this->faker->randomElement([
                'content-area',
                'call-to-action',
                'contact',
                'hero',
                'image-gallery',
                'testimonials'
            ]),
            'data' => $this->getDefaultData(),
            'order' => $this->faker->numberBetween(1, 100),
            'visible' => $this->faker->boolean(80), // 80% chance of being visible
        ];
    }

    /**
     * Get default data based on block type.
     *
     * @return array<string, mixed>
     */
    protected function getDefaultData(): array
    {
        $type = $this->faker->randomElement([
            'content-area',
            'call-to-action',
            'contact',
            'hero',
            'image-gallery',
            'testimonials'
        ]);

        return match ($type) {
            'content-area' => [
                'content' => ['en' => $this->faker->paragraph()],
                'background_color' => $this->faker->randomElement(['bg-white', 'bg-gray-100', 'bg-blue-50']),
                'text_color' => $this->faker->randomElement(['text-gray-900', 'text-gray-800', 'text-blue-900']),
            ],
            'call-to-action' => [
                'title' => ['en' => $this->faker->sentence()],
                'content' => ['en' => $this->faker->paragraph()],
                'button_text' => ['en' => $this->faker->words(2, true)],
                'button_url' => $this->faker->url(),
                'background_color' => $this->faker->randomElement(['bg-blue-600', 'bg-green-600', 'bg-purple-600']),
                'text_color' => 'text-white',
            ],
            'contact' => [
                'title' => ['en' => $this->faker->sentence()],
                'description' => ['en' => $this->faker->paragraph()],
                'form_id' => $this->faker->numberBetween(1, 10),
            ],
            'hero' => [
                'title' => ['en' => $this->faker->sentence()],
                'subtitle' => ['en' => $this->faker->sentence()],
                'background_image' => $this->faker->imageUrl(1920, 1080),
                'overlay_color' => $this->faker->randomElement(['bg-black/50', 'bg-blue-900/50', 'bg-gray-900/50']),
            ],
            'image-gallery' => [
                'title' => ['en' => $this->faker->sentence()],
                'images' => [
                    $this->faker->imageUrl(800, 600),
                    $this->faker->imageUrl(800, 600),
                    $this->faker->imageUrl(800, 600),
                ],
                'columns' => $this->faker->randomElement([2, 3, 4]),
            ],
            'testimonials' => [
                'title' => ['en' => $this->faker->sentence()],
                'testimonials' => [
                    [
                        'name' => $this->faker->name(),
                        'position' => $this->faker->jobTitle(),
                        'company' => $this->faker->company(),
                        'content' => ['en' => $this->faker->paragraph()],
                        'rating' => $this->faker->numberBetween(4, 5),
                        'avatar' => $this->faker->imageUrl(100, 100),
                    ],
                    [
                        'name' => $this->faker->name(),
                        'position' => $this->faker->jobTitle(),
                        'company' => $this->faker->company(),
                        'content' => ['en' => $this->faker->paragraph()],
                        'rating' => $this->faker->numberBetween(4, 5),
                        'avatar' => $this->faker->imageUrl(100, 100),
                    ],
                ],
            ],
            default => [
                'content' => ['en' => $this->faker->paragraph()],
            ],
        };
    }

    /**
     * Indicate that the block is a content area.
     */
    public function contentArea(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'content-area',
            'data' => [
                'content' => ['en' => $this->faker->paragraph()],
                'background_color' => $this->faker->randomElement(['bg-white', 'bg-gray-100', 'bg-blue-50']),
                'text_color' => $this->faker->randomElement(['text-gray-900', 'text-gray-800', 'text-blue-900']),
            ],
        ]);
    }

    /**
     * Indicate that the block is a call to action.
     */
    public function callToAction(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'call-to-action',
            'data' => [
                'title' => ['en' => $this->faker->sentence()],
                'content' => ['en' => $this->faker->paragraph()],
                'button_text' => ['en' => $this->faker->words(2, true)],
                'button_url' => $this->faker->url(),
                'background_color' => $this->faker->randomElement(['bg-blue-600', 'bg-green-600', 'bg-purple-600']),
                'text_color' => 'text-white',
            ],
        ]);
    }

    /**
     * Indicate that the block is a contact form.
     */
    public function contact(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'contact',
            'data' => [
                'title' => ['en' => $this->faker->sentence()],
                'description' => ['en' => $this->faker->paragraph()],
                'form_id' => $this->faker->numberBetween(1, 10),
            ],
        ]);
    }

    /**
     * Indicate that the block is a hero section.
     */
    public function hero(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'hero',
            'data' => [
                'title' => ['en' => $this->faker->sentence()],
                'subtitle' => ['en' => $this->faker->sentence()],
                'background_image' => $this->faker->imageUrl(1920, 1080),
                'overlay_color' => $this->faker->randomElement(['bg-black/50', 'bg-blue-900/50', 'bg-gray-900/50']),
            ],
        ]);
    }

    /**
     * Indicate that the block is an image gallery.
     */
    public function imageGallery(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'image-gallery',
            'data' => [
                'title' => ['en' => $this->faker->sentence()],
                'images' => [
                    $this->faker->imageUrl(800, 600),
                    $this->faker->imageUrl(800, 600),
                    $this->faker->imageUrl(800, 600),
                ],
                'columns' => $this->faker->randomElement([2, 3, 4]),
            ],
        ]);
    }

    /**
     * Indicate that the block is testimonials.
     */
    public function testimonials(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'testimonials',
            'data' => [
                'title' => ['en' => $this->faker->sentence()],
                'testimonials' => [
                    [
                        'name' => $this->faker->name(),
                        'position' => $this->faker->jobTitle(),
                        'company' => $this->faker->company(),
                        'content' => ['en' => $this->faker->paragraph()],
                        'rating' => $this->faker->numberBetween(4, 5),
                        'avatar' => $this->faker->imageUrl(100, 100),
                    ],
                    [
                        'name' => $this->faker->name(),
                        'position' => $this->faker->jobTitle(),
                        'company' => $this->faker->company(),
                        'content' => ['en' => $this->faker->paragraph()],
                        'rating' => $this->faker->numberBetween(4, 5),
                        'avatar' => $this->faker->imageUrl(100, 100),
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
        return $this->state(fn (array $attributes) => [
            'visible' => true,
        ]);
    }

    /**
     * Indicate that the block is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'visible' => false,
        ]);
    }
} 