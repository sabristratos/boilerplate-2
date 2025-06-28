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
        return __('forms.field_types.checkbox.name');
    }

    public function getPreviewComponent(): string
    {
        return 'forms.previews.checkbox';
    }

    public function getComponentOptions(): array
    {
        return array_merge(parent::getComponentOptions(), [
            'variant' => ['type' => 'select', 'label' => __('forms.field_types.checkbox.variant_label'), 'options' => [
                'default' => __('forms.field_types.checkbox.variant_default'),
                'cards' => __('forms.field_types.checkbox.variant_cards'),
            ]],
        ]);
    }
} 