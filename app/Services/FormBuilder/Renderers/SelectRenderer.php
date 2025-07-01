<?php

namespace App\Services\FormBuilder\Renderers;

class SelectRenderer extends BaseElementRenderer
{
    public function render(array $element): string
    {
        $data = $this->prepareViewData($element);

        return view('components.form-builder.elements.select', $data)->render();
    }

    public function supports(string $type): bool
    {
        return $type === 'select';
    }

    protected function getDefaultLabel(): string
    {
        return 'New Select';
    }

    protected function getViewName(): string
    {
        return 'components.form-builder.elements.select';
    }

    /**
     * Get supported element types for this renderer
     */
    public function getSupportedTypes(): array
    {
        return ['select'];
    }

    public function getDefaultProperties(): array
    {
        $properties = parent::getDefaultProperties();

        // Add select-specific properties
        $properties['options'] = [];

        return $properties;
    }
}
