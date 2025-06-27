<?php

namespace App\Forms\FieldTypes;

use App\Models\FormField;

abstract class FieldType
{
    abstract public function getName(): string;

    abstract public function getLabel(): string;

    public function getIcon(): ?string
    {
        return null;
    }

    abstract public function getPreviewComponent(): string;

    public function getSettingsView(): ?string
    {
        return null;
    }

    public function getBaseValidationRules(FormField $field): array
    {
        return [];
    }

    public function getComponentOptions(): array
    {
        return [
            'tooltip' => ['type' => 'string', 'label' => 'Tooltip'],
        ];
    }
} 