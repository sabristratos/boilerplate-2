<?php

namespace App\Forms\FieldTypes;

use App\Models\FormField;

class TextField extends FieldType
{
    public function getName(): string
    {
        return 'text';
    }

    public function getLabel(): string
    {
        return 'Text';
    }

    public function getPreviewComponent(): string
    {
        return 'forms.previews.text';
    }

    public function getComponentOptions(): array
    {
        return array_merge(parent::getComponentOptions(), [
            'viewable' => ['type' => 'boolean', 'label' => 'Viewable'],
            'copyable' => ['type' => 'boolean', 'label' => __('forms.field_types.text.copyable_label')],
        ]);
    }
}
