<?php

namespace App\Forms\FieldTypes;

use App\Models\FormField;

class DateField extends FieldType
{
    public function getName(): string
    {
        return 'date';
    }

    public function getLabel(): string
    {
        return 'Date';
    }

    public function getPreviewComponent(): string
    {
        return 'forms.previews.text';
    }

    public function getBaseValidationRules(FormField $field): array
    {
        return ['date'];
    }
} 