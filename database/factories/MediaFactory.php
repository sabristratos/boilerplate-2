<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Spatie\MediaLibrary\MediaCollections\Models\Media>
 */
class MediaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Media::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'model_type' => 'App\Models\Testimonial',
            'model_id' => 1,
            'uuid' => $this->faker->uuid(),
            'collection_name' => 'avatar',
            'name' => $this->faker->word().'.jpg',
            'file_name' => $this->faker->word().'.jpg',
            'mime_type' => 'image/jpeg',
            'disk' => 'public',
            'conversions_disk' => 'public',
            'size' => $this->faker->numberBetween(1000, 1000000),
            'manipulations' => [],
            'custom_properties' => [],
            'generated_conversions' => [],
            'responsive_images' => [],
            'order_column' => $this->faker->numberBetween(1, 100),
        ];
    }

    /**
     * Indicate that the media is an image.
     */
    public function image(): static
    {
        return $this->state(fn (array $attributes) => [
            'mime_type' => 'image/jpeg',
            'name' => $this->faker->word().'.jpg',
            'file_name' => $this->faker->word().'.jpg',
        ]);
    }

    /**
     * Indicate that the media is a PDF.
     */
    public function pdf(): static
    {
        return $this->state(fn (array $attributes) => [
            'mime_type' => 'application/pdf',
            'name' => $this->faker->word().'.pdf',
            'file_name' => $this->faker->word().'.pdf',
        ]);
    }
}
