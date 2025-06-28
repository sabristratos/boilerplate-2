<?php

namespace App\Forms\FieldTypes;

class SectionField extends FieldType
{
    public function getName(): string
    {
        return 'section';
    }

    public function getLabel(): string
    {
        return __('forms.field_types.section.name');
    }

    public function getPreviewComponent(): string
    {
        return 'forms.previews.section';
    }
} 