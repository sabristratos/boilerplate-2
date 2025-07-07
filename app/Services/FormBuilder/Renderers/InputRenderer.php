<?php

namespace App\Services\FormBuilder\Renderers;

use App\Services\FormBuilder\ElementDTO;

/**
 * Renderer for input form elements (text, email).
 */
class InputRenderer extends BaseElementRenderer
{
    /**
     * Check if this renderer supports the given element type.
     */
    public function supports(string $type): bool
    {
        return in_array($type, ['text', 'email']);
    }

    /**
     * Get the default label for this element type.
     */
    protected function getDefaultLabel(): string
    {
        return 'New Input';
    }

    /**
     * Get the view name for this element type.
     */
    protected function getViewName(): string
    {
        return 'components.forms.input';
    }

    /**
     * Prepare data for the view.
     */
    protected function prepareViewData(ElementDTO $element): array
    {
        $properties = $element->properties ?? [];
        $fluxProps = $properties['fluxProps'] ?? [];

        return [
            'type' => $element->type === 'email' ? 'email' : 'text',
            'label' => $properties['label'] ?? 'Text Input',
            'placeholder' => $properties['placeholder'] ?? '',
            'required' => in_array('required', $element->validation['rules'] ?? []),
            'disabled' => false,
            'clearable' => $fluxProps['clearable'] ?? false,
            'copyable' => $fluxProps['copyable'] ?? false,
            'viewable' => $fluxProps['viewable'] ?? false,
            'icon' => $fluxProps['icon'] ?? null,
            'iconTrailing' => $fluxProps['iconTrailing'] ?? null,
            'badge' => $properties['badge'] ?? '',
            'description' => $properties['description'] ?? '',
            'descriptionTrailing' => $properties['descriptionTrailing'] ?? false,
            'error' => null,
            'wireModel' => $this->getWireModel($element),
            'min' => $properties['min'] ?? null,
            'max' => $properties['max'] ?? null,
            'step' => $properties['step'] ?? null,
        ];
    }

    /**
     * Override the render method to use the fieldName parameter.
     */
    public function render(ElementDTO $element, string $mode = 'edit', ?string $fieldName = null): string
    {
        $data = $this->prepareViewData($element);
        $data['mode'] = $mode;

        // Use the provided fieldName if available
        if ($fieldName) {
            $data['wireModel'] = "previewFormData.{$fieldName}";
        }

        return view($this->getViewName(), $data)->render();
    }

    /**
     * Get the wire:model attribute for the element.
     */
    private function getWireModel(ElementDTO $element): ?string
    {
        $properties = $element->properties ?? [];
        $label = $properties['label'] ?? '';
        $id = $element->id ?? '';

        // Create a field name from the label or ID
        $fieldName = \Illuminate\Support\Str::slug($label, '_') ?: 'field_'.$id;

        return "previewFormData.{$fieldName}";
    }

    /**
     * Get default properties for this element type.
     */
    public function getDefaultProperties(): array
    {
        $properties = parent::getDefaultProperties();

        // Add input-specific properties
        $properties['type'] = 'text';

        return $properties;
    }

    /**
     * Get supported element types for this renderer
     */
    public function getSupportedTypes(): array
    {
        return ['text', 'email'];
    }
}
