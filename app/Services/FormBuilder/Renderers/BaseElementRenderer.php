<?php

namespace App\Services\FormBuilder\Renderers;

use App\Services\FormBuilder\Contracts\ElementRendererInterface;

abstract class BaseElementRenderer implements ElementRendererInterface
{
    /**
     * Get default properties for this element type
     */
    public function getDefaultProperties(): array
    {
        $config = config('forms.elements.default_properties');

        return [
            'label' => $this->getDefaultLabel(),
            'placeholder' => '',
            'fluxProps' => $config['fluxProps'],
        ];
    }

    /**
     * Get default styles for this element type
     */
    public function getDefaultStyles(): array
    {
        return config('forms.elements.default_styles');
    }

    /**
     * Get default validation for this element type
     */
    public function getDefaultValidation(): array
    {
        return config('forms.elements.default_validation');
    }

    /**
     * Get the default label for this element type
     */
    abstract protected function getDefaultLabel(): string;

    /**
     * Get the view name for this element type
     */
    abstract protected function getViewName(): string;

    /**
     * Prepare data for the view
     */
    protected function prepareViewData(array $element): array
    {
        return [
            'element' => $element,
            'properties' => $element['properties'] ?? [],
            'fluxProps' => $element['properties']['fluxProps'] ?? [],
        ];
    }
}
