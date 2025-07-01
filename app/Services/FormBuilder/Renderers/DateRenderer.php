<?php

namespace App\Services\FormBuilder\Renderers;

class DateRenderer extends BaseElementRenderer
{
    public function render(array $element): string
    {
        $data = $this->prepareViewData($element);

        return view('components.form-builder.elements.date', $data)->render();
    }

    public function supports(string $type): bool
    {
        return $type === 'date';
    }

    protected function getDefaultLabel(): string
    {
        return 'New Date Picker';
    }

    protected function getViewName(): string
    {
        return 'components.form-builder.elements.date';
    }

    /**
     * Get supported element types for this renderer
     */
    public function getSupportedTypes(): array
    {
        return ['date'];
    }

    public function getDefaultProperties(): array
    {
        $properties = parent::getDefaultProperties();

        // Add comprehensive date-picker properties based on Flux documentation
        $properties['mode'] = 'single';
        $properties['minRange'] = '';
        $properties['maxRange'] = '';
        $properties['min'] = '';
        $properties['max'] = '';
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
        $properties['locale'] = '';

        return $properties;
    }
} 