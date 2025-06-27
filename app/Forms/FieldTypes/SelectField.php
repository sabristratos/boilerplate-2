<?php

namespace App\Forms\FieldTypes;

class SelectField extends FieldType
{
    public function getName(): string
    {
        return 'select';
    }

    public function getLabel(): string
    {
        return 'Select';
    }

    public function getPreviewComponent(): string
    {
        return 'forms.previews.select';
    }

    public function getComponentOptions(): array
    {
        return array_merge(parent::getComponentOptions(), [
            'variant' => ['type' => 'select', 'label' => 'Variant', 'options' => [
                'default' => 'Default',
                'listbox' => 'Listbox',
            ]],
            'multiple' => ['type' => 'boolean', 'label' => 'Multiple'],
            'searchable' => ['type' => 'boolean', 'label' => 'Searchable'],
        ]);
    }
} 