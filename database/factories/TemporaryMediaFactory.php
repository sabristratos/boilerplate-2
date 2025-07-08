<?php

namespace Database\Factories;

use App\Models\TemporaryMedia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TemporaryMedia>
 */
class TemporaryMediaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TemporaryMedia::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'session_id' => fake()->uuid(),
            'field_name' => fake()->word(),
            'model_type' => \App\Models\Testimonial::class,
            'collection_name' => 'avatar',
        ];
    }
}
