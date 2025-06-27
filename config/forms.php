<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Form Values
    |--------------------------------------------------------------------------
    |
    | Here you can specify the default values for newly created forms.
    | These values will be used when a form is created through the
    | form builder.
    |
    */
    'defaults' => [
        'success_message' => [
            'en' => 'Your submission was successful.',
            'fr' => 'Votre soumission a été envoyée avec succès.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Predefined Validation Rules
    |--------------------------------------------------------------------------
    |
    | Define a list of common validation rules that users can easily
    | apply to form fields. You can customize the rule and provide
    | a user-friendly label.
    |
    */
    'validation_rules' => [
        'required' => [
            'label' => 'Required',
            'rule' => 'required',
        ],
        'email' => [
            'label' => 'Email',
            'rule' => 'email',
        ],
        'min' => [
            'label' => 'Minimum Length',
            'rule' => 'min:', // Example: min:5
        ],
        'max' => [
            'label' => 'Maximum Length',
            'rule' => 'max:', // Example: max:255
        ],
        'numeric' => [
            'label' => 'Numeric',
            'rule' => 'numeric',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Field Types
    |--------------------------------------------------------------------------
    |
    | Here you can register your custom field types. The key is the field type
    | name, and the value is the class name of the field type.
    |
    */
    'field_types' => [
        'text' => \App\Forms\FieldTypes\TextField::class,
        'textarea' => \App\Forms\FieldTypes\TextareaField::class,
        'select' => \App\Forms\FieldTypes\SelectField::class,
        'checkbox' => \App\Forms\FieldTypes\CheckboxField::class,
        'radio' => \App\Forms\FieldTypes\RadioField::class,
        'file' => \App\Forms\FieldTypes\FileField::class,
        'email' => \App\Forms\FieldTypes\EmailField::class,
        'number' => \App\Forms\FieldTypes\NumberField::class,
        'date' => \App\Forms\FieldTypes\DateField::class,
        'time' => \App\Forms\FieldTypes\TimeField::class,
        'section' => \App\Forms\FieldTypes\SectionField::class,
    ],
]; 