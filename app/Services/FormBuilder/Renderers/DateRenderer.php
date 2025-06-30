<?php

namespace App\Services\FormBuilder\Renderers;

class DateRenderer extends BaseElementRenderer
{
    public function render(array $element): string
    {
        $data = $this->prepareViewData($element);

        return view('components.form-builder.elements.date', $data)->render();
    }

    public function supports(string $type): bool
    {
        return $type === 'date';
    }

    protected function getDefaultLabel(): string
    {
        return 'New Date Picker';
    }

    protected function getViewName(): string
    {
        return 'components.form-builder.elements.date';
    }

    public function getDefaultProperties(): array
    {
        $properties = parent::getDefaultProperties();

        // Add date-specific properties
        $properties['mode'] = 'single';
        $properties['withPresets'] = false;
        $properties['clearable'] = true;
        $properties['min'] = '';
        $properties['max'] = '';

        return $properties;
    }
} 