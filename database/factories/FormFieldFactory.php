<?php

namespace Database\Factories;

use App\Enums\FormFieldType;
use App\Models\Form;
use App\Models\FormField;
use Illuminate\Database\Eloquent\Factories\Factory;

class FormFieldFactory extends Factory
{
    protected $model = FormField::class;

    public function definition(): array
    {
        return [
            'form_id' => Form::factory(),
            'type' => FormFieldType::TEXT,
            'name' => $this->faker->slug,
            'label' => $this->faker->words(3, true),
            'sort_order' => 0,
            'validation_rules' => null,
            'placeholder' => null,
            'options' => null,
        ];
    }
}