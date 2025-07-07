<?php

declare(strict_types=1);

namespace App\Services\FormBuilder\Renderers;

use App\Services\FormBuilder\ElementDTO;

/**
 * Renderer for select form elements.
 */
class SelectRenderer extends BaseElementRenderer
{
    /**
     * Check if this renderer supports the given element type.
     */
    public function supports(string $type): bool
    {
        return $type === 'select';
    }

    /**
     * Get the default label for this element type.
     */
    protected function getDefaultLabel(): string
    {
        return 'New Select';
    }

    /**
     * Get the view name for this element type.
     */
    protected function getViewName(): string
    {
        return 'components.forms.select';
    }

    /**
     * Get default properties for this element type.
     */
    public function getDefaultProperties(): array
    {
        $properties = parent::getDefaultProperties();
        $properties['options'] = "option1|Option 1\noption2|Option 2\noption3|Option 3";

        return $properties;
    }

    /**
     * Prepare data for the view.
     */
    protected function prepareViewData(ElementDTO $element): array
    {
        $properties = $element->properties ?? [];
        $fluxProps = $properties['fluxProps'] ?? [];

        // Parse options using the OptionParserService
        $optionParser = app(\App\Services\FormBuilder\OptionParserService::class);
        $options = $optionParser->parseOptions($properties['options'] ?? '');

        return [
            'label' => $properties['label'] ?? 'Select',
            'placeholder' => $properties['placeholder'] ?? 'Choose an option...',
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
            'options' => $options,
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
        if ($fieldName) {
            $data['wireModel'] = "previewFormData.{$fieldName}";
        }

        // Debug: Log what we're getting
        \Log::debug('SelectRenderer render method', [
            'elementId' => $element->id ?? 'unknown',
            'properties' => $element->properties ?? [],
            'options' => $data['options'],
            'optionsCount' => count($data['options']),
            'mode' => $mode,
        ]);

        // Ensure we have options to display
        if (empty($data['options'])) {
            $data['options'] = [
                ['value' => 'option1', 'label' => 'Option 1'],
                ['value' => 'option2', 'label' => 'Option 2'],
                ['value' => 'option3', 'label' => 'Option 3'],
            ];
        }

        return view($this->getViewName(), $data)->render();
    }
}
