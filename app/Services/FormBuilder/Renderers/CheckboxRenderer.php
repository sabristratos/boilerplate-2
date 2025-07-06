<?php

namespace App\Services\FormBuilder\Renderers;

/**
 * Renderer for checkbox form elements.
 */
class CheckboxRenderer extends BaseElementRenderer
{
    /**
     * Check if this renderer supports the given element type.
     *
     * @param string $type
     * @return bool
     */
    public function supports(string $type): bool
    {
        return $type === 'checkbox';
    }

    /**
     * Get the default label for this element type.
     *
     * @return string
     */
    protected function getDefaultLabel(): string
    {
        return 'New Checkbox';
    }

    /**
     * Get the view name for this element type.
     *
     * @return string
     */
    protected function getViewName(): string
    {
        return 'components.form-builder.elements.checkbox';
    }

    /**
     * Get supported element types for this renderer
     */
    public function getSupportedTypes(): array
    {
        return ['checkbox'];
    }
}
