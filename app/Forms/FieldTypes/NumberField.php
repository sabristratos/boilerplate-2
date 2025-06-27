<?php

namespace App\Forms\FieldTypes;

use App\Models\FormField;

class NumberField extends FieldType
{
    public function getName(): string
    {
        return 'number';
    }

    public function getLabel(): string
    {
        return 'Number';
    }

    public function getPreviewComponent(): string
    {
        return 'forms.previews.text';
    }

    public function getBaseValidationRules(FormField $field): array
    {
        return ['numeric'];
    }
} 