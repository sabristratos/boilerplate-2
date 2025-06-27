<?php

namespace App\Forms\FieldTypes;

class RadioField extends FieldType
{
    public function getName(): string
    {
        return 'radio';
    }

    public function getLabel(): string
    {
        return 'Radio';
    }

    public function getPreviewComponent(): string
    {
        return 'forms.previews.radio';
    }

    public function getComponentOptions(): array
    {
        return array_merge(parent::getComponentOptions(), [
            'variant' => ['type' => 'select', 'label' => 'Variant', 'options' => [
                'default' => 'Default',
                'segmented' => 'Segmented',
                'cards' => 'Cards',
            ]],
        ]);
    }
} 