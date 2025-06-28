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
            'variant' => ['type' => 'select', 'label' => __('forms.field_types.radio.variant_label'), 'options' => [
            'default' => __('forms.field_types.radio.variant_default'),
            'segmented' => __('forms.field_types.radio.variant_segmented'),
            'cards' => __('forms.field_types.radio.variant_cards'),
            ]],
        ]);
    }
} 