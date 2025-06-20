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
            'name' => $this->faker->name,
            'title' => $this->faker->jobTitle,
            'content' => $this->faker->realText,
            'rating' => $this->faker->numberBetween(4, 5),
            'source' => $this->faker->randomElement(['Twitter', 'Google', 'Facebook']),
            'order' => $this->faker->unique()->numberBetween(1, 100),
        ];
    }
}
