<?php

namespace App\Services\FormBuilder\Renderers;

class PasswordRenderer extends BaseElementRenderer
{
    public function render(array $element): string
    {
        $data = $this->prepareViewData($element);

        return view('components.form-builder.elements.password', $data)->render();
    }

    public function supports(string $type): bool
    {
        return $type === 'password';
    }

    protected function getDefaultLabel(): string
    {
        return 'New Password Input';
    }

    protected function getViewName(): string
    {
        return 'components.form-builder.elements.password';
    }

    /**
     * Get supported element types for this renderer
     */
    public function getSupportedTypes(): array
    {
        return ['password'];
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