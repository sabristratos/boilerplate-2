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
]; 