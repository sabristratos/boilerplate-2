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
        $type = $this->faker->randomElement(FormFieldType::cases());
        $label = $this->faker->words(2, true);

        return [
            'form_id' => Form::factory(),
            'type' => $type,
            'name' => str_replace(' ', '_', strtolower($label)),
            'label' => [
                'en' => ucfirst($label),
            ],
            'placeholder' => [
                'en' => 'Enter your ' . $label,
            ],
            'options' => $type === FormFieldType::SELECT ? [
                'en' => ['Option 1', 'Option 2', 'Option 3'],
            ] : null,
            'validation_rules' => 'nullable|string',
            'is_required' => false,
            'sort_order' => $this->faker->numberBetween(1, 100),
        ];
    }
} 