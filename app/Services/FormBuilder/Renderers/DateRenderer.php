<?php

namespace App\Services\FormBuilder\Renderers;

/**
 * Renderer for date form elements.
 */
class DateRenderer extends BaseElementRenderer
{
    /**
     * Check if this renderer supports the given element type.
     *
     * @param string $type
     * @return bool
     */
    public function supports(string $type): bool
    {
        return $type === 'date';
    }

    /**
     * Get the default label for this element type.
     *
     * @return string
     */
    protected function getDefaultLabel(): string
    {
        return 'New Date';
    }

    /**
     * Get the view name for this element type.
     *
     * @return string
     */
    protected function getViewName(): string
    {
        return 'components.form-builder.elements.date';
    }

    /**
     * Get default properties for this element type.
     *
     * @return array
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
} 