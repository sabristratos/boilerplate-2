<?php

namespace App\Services\FormBuilder\Renderers;

use App\Enums\FormElementType;
use App\Services\FormBuilder\ElementDTO;

/**
 * Renderer for textarea form elements.
 */
class TextareaRenderer extends BaseElementRenderer
{
    /**
     * Check if this renderer supports the given element type.
     */
    public function supports(string $type): bool
    {
        return $type === FormElementType::TEXTAREA->value;
    }

    /**
     * Get the default label for this element type.
     */
    protected function getDefaultLabel(): string
    {
        return 'New Textarea';
    }

    /**
     * Get the view name for this element type.
     */
    protected function getViewName(): string
    {
        return 'components.forms.textarea';
    }

    /**
     * Get supported element types for this renderer
     */
    public function getSupportedTypes(): array
    {
        return ['textarea'];
    }

    /**
     * Prepare data for the view.
     */
    protected function prepareViewData(ElementDTO $element): array
    {
        $properties = $element->properties ?? [];
        $fluxProps = $properties['fluxProps'] ?? [];

        return [
            'label' => $properties['label'] ?? 'Text Area',
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
            'rows' => $properties['rows'] ?? 4,
            'resize' => $properties['resize'] ?? 'vertical',
        ];
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
     * Override the render method to use the fieldName parameter.
     */
    public function render(ElementDTO $element, string $mode = 'edit', ?string $fieldName = null): string
    {
        $data = $this->prepareViewData($element);
        $data['mode'] = $mode;

        // Use the provided fieldName if available
        if ($fieldName !== null && $fieldName !== '' && $fieldName !== '0') {
            $data['wireModel'] = "previewFormData.{$fieldName}";
        }

        return view($this->getViewName(), $data)->render();
    }
}
