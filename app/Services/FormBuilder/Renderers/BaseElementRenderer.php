<?php

namespace App\Services\FormBuilder\Renderers;

use App\Services\FormBuilder\Contracts\ElementRendererInterface;
use App\Services\FormBuilder\ElementDTO;

/**
 * Base class for form element renderers.
 */
abstract class BaseElementRenderer implements ElementRendererInterface
{
    /**
     * Render the element as HTML using the view and prepared data.
     *
     * @param ElementDTO $element The element DTO
     * @param string $mode The rendering mode ('edit', 'preview', etc.)
     * @param string|null $fieldName The field name for the element
     * @return string The rendered HTML
     */
    public function render(ElementDTO $element, string $mode = 'edit', ?string $fieldName = null): string
    {
        $data = $this->prepareViewData($element);
        $data['mode'] = $mode;
        $data['fieldName'] = $fieldName;

        return view($this->getViewName(), $data)->render();
    }

    /**
     * Get default properties for this element type.
     *
     * @return array The default properties
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
     * Get default styles for this element type.
     *
     * @return array The default styles
     */
    public function getDefaultStyles(): array
    {
        return config('forms.elements.default_styles');
    }

    /**
     * Get default validation for this element type.
     *
     * @return array The default validation rules
     */
    public function getDefaultValidation(): array
    {
        return config('forms.elements.default_validation');
    }

    /**
     * Get the default label for this element type.
     *
     * @return string The default label
     */
    abstract protected function getDefaultLabel(): string;

    /**
     * Get the view name for this element type.
     *
     * @return string The view name
     */
    abstract protected function getViewName(): string;

    /**
     * Prepare data for the view.
     *
     * @param ElementDTO $element The element DTO
     * @return array The data for the view
     */
    protected function prepareViewData(ElementDTO $element): array
    {
        return [
            'element' => $element,
            'properties' => $element->properties ?? [],
            'fluxProps' => $element->properties['fluxProps'] ?? [],
        ];
    }
}
