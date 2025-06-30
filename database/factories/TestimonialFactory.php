<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Testimonial>
 */
class TestimonialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name,
            'title' => fake()->jobTitle,
            'content' => fake()->realText,
            'rating' => fake()->numberBetween(4, 5),
            'source' => fake()->randomElement(['Twitter', 'Google', 'Facebook']),
            'order' => fake()->unique()->numberBetween(1, 100),
        ];
    }
}
