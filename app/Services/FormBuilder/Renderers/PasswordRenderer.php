<?php

namespace App\Services\FormBuilder\Renderers;

/**
 * Renderer for password form elements.
 */
class PasswordRenderer extends BaseElementRenderer
{
    /**
     * Check if this renderer supports the given element type.
     *
     * @param string $type
     * @return bool
     */
    public function supports(string $type): bool
    {
        return $type === 'password';
    }

    /**
     * Get the default label for this element type.
     *
     * @return string
     */
    protected function getDefaultLabel(): string
    {
        return 'New Password';
    }

    /**
     * Get the view name for this element type.
     *
     * @return string
     */
    protected function getViewName(): string
    {
        return 'components.form-builder.elements.password';
    }

    public function getDefaultProperties(): array
    {
        $properties = parent::getDefaultProperties();

        // Add password-specific properties
        $properties['viewable'] = true;
        $properties['clearable'] = true;
        $properties['copyable'] = false;

        return $properties;
    }
} 