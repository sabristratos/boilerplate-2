<?php

namespace App\Forms\FieldTypes;

class CheckboxField extends FieldType
{
    public function getName(): string
    {
        return 'checkbox';
    }

    public function getLabel(): string
    {
        return 'Checkbox';
    }

    public function getPreviewComponent(): string
    {
        return 'forms.previews.checkbox';
    }

    public function getComponentOptions(): array
    {
        return array_merge(parent::getComponentOptions(), [
            'variant' => ['type' => 'select', 'label' => 'Variant', 'options' => [
                'default' => 'Default',
                'cards' => 'Cards',
            ]],
        ]);
    }
} 