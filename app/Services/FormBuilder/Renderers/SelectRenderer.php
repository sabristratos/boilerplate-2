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
     * Get default properties for this element type.
     *
     * @return array
     */
    public function getDefaultProperties(): array
    {
        $properties = parent::getDefaultProperties();
        $properties['options'] = '';
        return $properties;
    }
}
