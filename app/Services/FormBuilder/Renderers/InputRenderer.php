<?php

namespace App\Services\FormBuilder\Renderers;

use App\Services\FormBuilder\ElementDTO;

/**
 * Renderer for input form elements (text, email).
 */
class InputRenderer extends BaseElementRenderer
{
    /**
     * Check if this renderer supports the given element type.
     *
     * @param string $type
     * @return bool
     */
    public function supports(string $type): bool
    {
        return in_array($type, ['text', 'email']);
    }

    /**
     * Get the default label for this element type.
     *
     * @return string
     */
    protected function getDefaultLabel(): string
    {
        return 'New Input';
    }

    /**
     * Get the view name for this element type.
     *
     * @return string
     */
    protected function getViewName(): string
    {
        return 'components.form-builder.elements.input';
    }

    /**
     * Get default properties for this element type.
     *
     * @return array
     */
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
