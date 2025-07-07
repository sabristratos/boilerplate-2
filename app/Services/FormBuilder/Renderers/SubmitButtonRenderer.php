<?php

declare(strict_types=1);

namespace App\Services\FormBuilder\Renderers;

use App\Services\FormBuilder\Contracts\ElementRendererInterface;
use App\Services\FormBuilder\ElementDTO;

/**
 * Renderer for submit button elements.
 */
class SubmitButtonRenderer extends BaseElementRenderer implements ElementRendererInterface
{
    /**
     * Check if this renderer supports the given element type.
     *
     * @param string $type The element type
     * @return bool True if supported
     */
    public function supports(string $type): bool
    {
        return $type === 'submit_button';
    }

    /**
     * Get the default label for this element type.
     *
     * @return string The default label
     */
    protected function getDefaultLabel(): string
    {
        return 'Submit';
    }

    /**
     * Get the view name for this element type.
     *
     * @return string The view name
     */
    protected function getViewName(): string
    {
        return 'components.form-builder.preview.submit-button';
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
            'buttonText' => 'Submit Form',
            'alignment' => 'center',
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
        $defaultStyles = config('forms.elements.default_styles');
        
        return array_merge($defaultStyles, [
            'desktop' => [
                'width' => 'full',
                'alignment' => 'center',
            ],
            'tablet' => [
                'width' => 'full',
                'alignment' => 'center',
            ],
            'mobile' => [
                'width' => 'full',
                'alignment' => 'center',
            ],
        ]);
    }

    /**
     * Get default validation for this element type.
     *
     * @return array The default validation rules
     */
    public function getDefaultValidation(): array
    {
        return [
            'rules' => [],
            'messages' => [],
            'values' => [],
        ];
    }

    /**
     * Override the render method to pass the active breakpoint to the view.
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
        
        // Get the active breakpoint from the element data if available
        // Since _activeBreakpoint is not a standard ElementDTO property, we need to access it differently
        $data['activeBreakpoint'] = 'desktop'; // Default fallback
        
        // Try to get the active breakpoint from the element's properties if it was stored there
        if (isset($element->properties['_activeBreakpoint'])) {
            $data['activeBreakpoint'] = $element->properties['_activeBreakpoint'];
        }

        return view($this->getViewName(), $data)->render();
    }
} 