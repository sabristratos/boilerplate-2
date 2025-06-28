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
            'resize' => ['type' => 'select', 'label' => __('forms.field_types.textarea.resize_label'), 'options' => [
            'vertical' => __('forms.field_types.textarea.resize_vertical'),
            'horizontal' => __('forms.field_types.textarea.resize_horizontal'),
            'both' => __('forms.field_types.textarea.resize_both'),
            'none' => __('forms.field_types.textarea.resize_none'),
            ]],
        ]);
    }
} 