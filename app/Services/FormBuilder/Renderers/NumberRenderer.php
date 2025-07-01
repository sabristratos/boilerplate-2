<?php

namespace App\Services\FormBuilder\Renderers;

class NumberRenderer extends BaseElementRenderer
{
    public function render(array $element): string
    {
        $data = $this->prepareViewData($element);

        return view('components.form-builder.elements.number', $data)->render();
    }

    public function supports(string $type): bool
    {
        return $type === 'number';
    }

    protected function getDefaultLabel(): string
    {
        return 'New Number Input';
    }

    protected function getViewName(): string
    {
        return 'components.form-builder.elements.number';
    }

    /**
     * Get supported element types for this renderer
     */
    public function getSupportedTypes(): array
    {
        return ['number'];
    }

    public function getDefaultProperties(): array
    {
        $properties = parent::getDefaultProperties();

        // Add number-specific properties
        $properties['min'] = '';
        $properties['max'] = '';
        $properties['step'] = '1';
        $properties['clearable'] = true;
        $properties['copyable'] = false;

        return $properties;
    }
} 