<?php

namespace App\Forms\FieldTypes;

class TextareaField extends FieldType
{
    public function getName(): string
    {
        return 'textarea';
    }

    public function getLabel(): string
    {
        return 'Textarea';
    }

    public function getPreviewComponent(): string
    {
        return 'forms.previews.textarea';
    }

    public function getComponentOptions(): array
    {
        return array_merge(parent::getComponentOptions(), [
            'rows' => ['type' => 'number', 'label' => 'Rows'],
            'resize' => ['type' => 'select', 'label' => 'Resize', 'options' => [
                'vertical' => 'Vertical',
                'horizontal' => 'Horizontal',
                'both' => 'Both',
                'none' => 'None',
            ]],
        ]);
    }
} 