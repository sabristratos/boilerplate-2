<?php

namespace Database\Factories;

use App\Models\Form;
use App\Models\FormField;
use Illuminate\Database\Eloquent\Factories\Factory;

class FormFactory extends Factory
{
    protected $model = Form::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'title' => [
                'en' => $this->faker->sentence,
            ],
            'description' => [
                'en' => $this->faker->paragraph,
            ],
            'success_message' => [
                'en' => 'Your submission was successful.',
            ],
            'recipient_email' => $this->faker->safeEmail,
            'send_notification' => true,
        ];
    }

    public function hasFields(int $count = 1, array $attributes = []): static
    {
        return $this->afterCreating(function (Form $form) use ($count, $attributes) {
            FormField::factory()->count($count)->create(
                array_merge(['form_id' => $form->id], $attributes)
            );
        });
    }
} 