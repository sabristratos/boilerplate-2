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
     * Render the element as HTML.
     *
     * @param ElementDTO $element
     * @param string $mode
     * @param string|null $fieldName
     * @return string
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
     * @return array
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
     * @return array
     */
    public function getDefaultStyles(): array
    {
        return config('forms.elements.default_styles');
    }

    /**
     * Get default validation for this element type.
     *
     * @return array
     */
    public function getDefaultValidation(): array
    {
        return config('forms.elements.default_validation');
    }

    /**
     * Get the default label for this element type.
     *
     * @return string
     */
    abstract protected function getDefaultLabel(): string;

    /**
     * Get the view name for this element type.
     *
     * @return string
     */
    abstract protected function getViewName(): string;

    /**
     * Prepare data for the view.
     *
     * @param ElementDTO $element
     * @return array
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
