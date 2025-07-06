<?php

namespace App\Services\FormBuilder\Renderers;

/**
 * Renderer for number form elements.
 */
class NumberRenderer extends BaseElementRenderer
{
    /**
     * Check if this renderer supports the given element type.
     *
     * @param string $type
     * @return bool
     */
    public function supports(string $type): bool
    {
        return $type === 'number';
    }

    /**
     * Get the default label for this element type.
     *
     * @return string
     */
    protected function getDefaultLabel(): string
    {
        return 'New Number';
    }

    /**
     * Get the view name for this element type.
     *
     * @return string
     */
    protected function getViewName(): string
    {
        return 'components.form-builder.elements.number';
    }

    /**
<<<<<<< HEAD
     * Get supported element types for this renderer
     */
    public function getSupportedTypes(): array
    {
        return ['number'];
    }

=======
     * Get default properties for this element type.
     *
     * @return array
     */
>>>>>>> 3d646ebc8597a7b3e698f9f41fc701b941fde20d
    public function getDefaultProperties(): array
    {
        $properties = parent::getDefaultProperties();
        $properties['min'] = '';
        $properties['max'] = '';
        $properties['step'] = '';
        return $properties;
    }
} 