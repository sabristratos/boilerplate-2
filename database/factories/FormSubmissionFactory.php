<?php

namespace Database\Factories;

use App\Models\Form;
use App\Models\FormSubmission;
use Illuminate\Database\Eloquent\Factories\Factory;

class FormSubmissionFactory extends Factory
{
    protected $model = FormSubmission::class;

    public function definition(): array
    {
        return [
            'form_id' => Form::factory(),
            'data' => [
                'name' => $this->faker->name,
                'email' => $this->faker->safeEmail,
                'message' => $this->faker->paragraph,
            ],
        ];
    }
} 