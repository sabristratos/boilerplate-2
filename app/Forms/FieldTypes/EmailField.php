<?php

namespace App\Forms\FieldTypes;

use App\Models\FormField;

class EmailField extends FieldType
{
    public function getName(): string
    {
        return 'email';
    }

    public function getLabel(): string
    {
        return 'Email';
    }

    public function getPreviewComponent(): string
    {
        return 'forms.previews.text';
    }

    public function getBaseValidationRules(FormField $field): array
    {
        return ['email'];
    }
} 