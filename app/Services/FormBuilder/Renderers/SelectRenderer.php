<?php

namespace App\Services\FormBuilder\Renderers;

/**
 * Renderer for select form elements.
 */
class SelectRenderer extends BaseElementRenderer
{
    /**
     * Check if this renderer supports the given element type.
     *
     * @param string $type
     * @return bool
     */
    public function supports(string $type): bool
    {
        return $type === 'select';
    }

    /**
     * Get the default label for this element type.
     *
     * @return string
     */
    protected function getDefaultLabel(): string
    {
        return 'New Select';
    }

    /**
     * Get the view name for this element type.
     *
     * @return string
     */
    protected function getViewName(): string
    {
        return 'components.form-builder.elements.select';
    }

    /**
<<<<<<< HEAD
     * Get supported element types for this renderer
     */
    public function getSupportedTypes(): array
    {
        return ['select'];
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
        $properties['options'] = '';
        return $properties;
    }
}
