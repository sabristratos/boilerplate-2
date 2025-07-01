<?php

namespace App\Services\FormBuilder\Renderers;

class InputRenderer extends BaseElementRenderer
{
    public function render(array $element): string
    {
        $data = $this->prepareViewData($element);

        return view('components.form-builder.elements.input', $data)->render();
    }

    public function supports(string $type): bool
    {
        return in_array($type, ['text', 'email']);
    }

    protected function getDefaultLabel(): string
    {
        return 'New Input';
    }

    protected function getViewName(): string
    {
        return 'components.form-builder.elements.input';
    }

    public function getDefaultProperties(): array
    {
        $properties = parent::getDefaultProperties();

        // Add input-specific properties
        $properties['type'] = 'text';

        return $properties;
    }

    /**
     * Get supported element types for this renderer
     */
    public function getSupportedTypes(): array
    {
        return ['text', 'email'];
    }
}
