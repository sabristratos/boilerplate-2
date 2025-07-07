<?php

declare(strict_types=1);

namespace App\Services\FormBuilder\Renderers;

/**
 * Renderer for number form elements.
 */
class NumberRenderer extends BaseElementRenderer
{
    /**
     * Check if this renderer supports the given element type.
     */
    public function supports(string $type): bool
    {
        return $type === 'number';
    }

    /**
     * Get the default label for this element type.
     */
    protected function getDefaultLabel(): string
    {
        return 'New Number';
    }

    /**
     * Get the view name for this element type.
     */
    protected function getViewName(): string
    {
        return 'components.forms.input';
    }

    /**
     * Get default properties for this element type.
     */
    public function getDefaultProperties(): array
    {
        $properties = parent::getDefaultProperties();
        $properties['min'] = '';
        $properties['max'] = '';
        $properties['step'] = '';

        return $properties;
    }
}
