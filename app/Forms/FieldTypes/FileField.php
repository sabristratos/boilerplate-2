<?php

namespace App\Forms\FieldTypes;

use App\Models\FormField;

class FileField extends FieldType
{
    public function getName(): string
    {
        return 'file';
    }

    public function getLabel(): string
    {
        return 'File';
    }

    public function getPreviewComponent(): string
    {
        return 'forms.previews.file';
    }

    public function getBaseValidationRules(FormField $field): array
    {
        return ['file'];
    }
} 