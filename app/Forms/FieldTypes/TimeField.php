<?php

namespace App\Forms\FieldTypes;

use App\Models\FormField;

class TimeField extends FieldType
{
    public function getName(): string
    {
        return 'time';
    }

    public function getLabel(): string
    {
        return __('forms.field_types.time.name');
    }

    public function getPreviewComponent(): string
    {
        return 'forms.previews.text';
    }

    public function getBaseValidationRules(FormField $field): array
    {
        return ['date_format:H:i'];
    }
} 