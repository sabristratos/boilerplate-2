<?php

declare(strict_types=1);

namespace App\Services\FormBuilder\Renderers;

use App\Services\FormBuilder\ElementDTO;

/**
 * Renderer for date form elements.
 */
class DateRenderer extends BaseElementRenderer
{
    /**
     * Check if this renderer supports the given element type.
     */
    public function supports(string $type): bool
    {
        return $type === 'date';
    }

    /**
     * Get the default label for this element type.
     */
    protected function getDefaultLabel(): string
    {
        return 'New Date';
    }

    /**
     * Get the view name for this element type.
     */
    protected function getViewName(): string
    {
        return 'components.form-builder.elements.date';
    }

    /**
     * Get default properties for this element type.
     */
    public function getDefaultProperties(): array
    {
        $properties = parent::getDefaultProperties();
        $properties['min'] = '';
        $properties['max'] = '';
        $properties['locale'] = app()->getLocale() ?? 'en';
        $properties['mode'] = 'single';
        $properties['months'] = 1;
        $properties['description'] = '';
        $properties['descriptionTrailing'] = false;
        $properties['badge'] = '';
        $properties['size'] = 'default';
        $properties['weekNumbers'] = false;
        $properties['selectableHeader'] = false;
        $properties['withToday'] = false;
        $properties['withInputs'] = false;
        $properties['withConfirmation'] = false;
        $properties['withPresets'] = false;
        $properties['presets'] = 'today yesterday thisWeek last7Days thisMonth yearToDate allTime';
        $properties['clearable'] = true;
        $properties['disabled'] = false;
        $properties['invalid'] = false;

        return $properties;
    }

    /**
     * Prepare data for the view.
     */
    protected function prepareViewData(ElementDTO $element): array
    {
        $properties = $element->properties ?? [];
        $fluxProps = $properties['fluxProps'] ?? [];
        
        return [
            'element' => $element,
            'properties' => $properties,
            'fluxProps' => $fluxProps,
            'mode' => 'preview',
            'fieldName' => $this->generateFieldName($element),
        ];
    }

    /**
     * Generate a field name for the element.
     */
    private function generateFieldName(ElementDTO $element): string
    {
        $properties = $element->properties ?? [];
        $label = $properties['label'] ?? '';
        $id = $element->id ?? '';
        
        // Create a field name from the label or ID
        $fieldName = \Illuminate\Support\Str::slug($label, '_') ?: 'field_' . $id;
        
        return $fieldName;
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
            $data['fieldName'] = $fieldName;
        }
        
        return view($this->getViewName(), $data)->render();
    }
}
