<?php

namespace App\Services\FormBuilder;

use App\Services\FormBuilder\Contracts\ElementRendererInterface;
use App\Services\FormBuilder\Renderers\CheckboxRenderer;
use App\Services\FormBuilder\Renderers\DateRenderer;
use App\Services\FormBuilder\Renderers\FileRenderer;
use App\Services\FormBuilder\Renderers\InputRenderer;
use App\Services\FormBuilder\Renderers\NumberRenderer;
use App\Services\FormBuilder\Renderers\PasswordRenderer;
use App\Services\FormBuilder\Renderers\RadioRenderer;
use App\Services\FormBuilder\Renderers\SelectRenderer;
use App\Services\FormBuilder\Renderers\TextareaRenderer;

class PreviewRenderer
{
    private readonly array $renderers;

    public function __construct()
    {
        $this->renderers = [
            new InputRenderer,
            new TextareaRenderer,
            new SelectRenderer,
            new CheckboxRenderer,
            new RadioRenderer,
            new DateRenderer,
            new NumberRenderer,
            new PasswordRenderer,
            new FileRenderer,
        ];
    }

    /**
     * Render an element as a preview form input
     */
    public function renderPreviewElement(array $element, string $fieldName): string
    {
        $renderer = $this->getRenderer($element['type']);

        if (!$renderer instanceof \App\Services\FormBuilder\Contracts\ElementRendererInterface) {
            return '<div class="text-red-500">Unsupported element type: '.$element['type'].'</div>';
        }

        return $this->renderPreviewInput($element, $fieldName);
    }

    /**
     * Get the appropriate renderer for an element type
     */
    private function getRenderer(string $type): ?ElementRendererInterface
    {
        foreach ($this->renderers as $renderer) {
            if ($renderer->supports($type)) {
                return $renderer;
            }
        }

        return null;
    }

    /**
     * Render a preview input based on element type
     */
    private function renderPreviewInput(array $element, string $fieldName): string
    {
        $properties = $element['properties'] ?? [];
        return match ($element['type']) {
            'text', 'email' => $this->renderTextInput($fieldName, $properties, $element['type']),
            'textarea' => $this->renderTextarea($fieldName, $properties),
            'select' => $this->renderSelect($fieldName, $properties),
            'checkbox' => $this->renderCheckbox($fieldName, $properties),
            'radio' => $this->renderRadio($fieldName, $properties),
            'date' => $this->renderDatePicker($fieldName, $properties),
            'number' => $this->renderNumberInput($fieldName, $properties),
            'password' => $this->renderPasswordInput($fieldName, $properties),
            'file' => $this->renderFileInput($fieldName, $properties),
            default => '<div class="text-red-500">Unsupported element type: '.$element['type'].'</div>',
        };
    }

    private function renderTextInput(string $fieldName, array $properties, string $type): string
    {
        $label = $properties['label'] ?? 'Text Input';
        $placeholder = $properties['placeholder'] ?? '';
        $required = in_array('required', $properties['validation']['rules'] ?? []);

        return view('components.form-builder.preview.text-input', [
            'fieldName' => $fieldName,
            'label' => $label,
            'placeholder' => $placeholder,
            'type' => $type,
            'required' => $required,
            'properties' => $properties,
        ])->render();
    }

    private function renderTextarea(string $fieldName, array $properties): string
    {
        $label = $properties['label'] ?? 'Text Area';
        $placeholder = $properties['placeholder'] ?? '';
        $required = in_array('required', $properties['validation']['rules'] ?? []);

        return view('components.form-builder.preview.textarea', [
            'fieldName' => $fieldName,
            'label' => $label,
            'placeholder' => $placeholder,
            'required' => $required,
            'properties' => $properties,
        ])->render();
    }

    private function renderSelect(string $fieldName, array $properties): string
    {
        $label = $properties['label'] ?? 'Select';
        $placeholder = $properties['placeholder'] ?? 'Choose an option...';
        $required = in_array('required', $properties['validation']['rules'] ?? []);
        $options = $properties['options'] ?? '';

        // Parse options with value|label format
        $optionArray = $this->parseOptions($options);

        return view('components.form-builder.preview.select', [
            'fieldName' => $fieldName,
            'label' => $label,
            'placeholder' => $placeholder,
            'required' => $required,
            'options' => $optionArray,
            'properties' => $properties,
        ])->render();
    }

    private function renderCheckbox(string $fieldName, array $properties): string
    {
        $label = $properties['label'] ?? 'Checkbox';
        $required = in_array('required', $properties['validation']['rules'] ?? []);
        $options = $properties['options'] ?? '';

        // Parse options with value|label format
        $optionArray = $this->parseOptions($options);

        return view('components.form-builder.preview.checkbox', [
            'fieldName' => $fieldName,
            'label' => $label,
            'required' => $required,
            'options' => $optionArray,
            'properties' => $properties,
        ])->render();
    }

    private function renderRadio(string $fieldName, array $properties): string
    {
        $label = $properties['label'] ?? 'Radio';
        $required = in_array('required', $properties['validation']['rules'] ?? []);
        $options = $properties['options'] ?? '';

        // Parse options with value|label format
        $optionArray = $this->parseOptions($options);

        return view('components.form-builder.preview.radio', [
            'fieldName' => $fieldName,
            'label' => $label,
            'required' => $required,
            'options' => $optionArray,
            'properties' => $properties,
        ])->render();
    }

    private function renderDatePicker(string $fieldName, array $properties): string
    {
        $label = $properties['label'] ?? 'Date Picker';
        $placeholder = $properties['placeholder'] ?? 'Select a date...';
        $required = in_array('required', $properties['validation']['rules'] ?? []);

        return view('components.form-builder.preview.date-picker', [
            'fieldName' => $fieldName,
            'label' => $label,
            'placeholder' => $placeholder,
            'required' => $required,
            'properties' => $properties,
        ])->render();
    }

    private function renderNumberInput(string $fieldName, array $properties): string
    {
        $label = $properties['label'] ?? 'Number Input';
        $placeholder = $properties['placeholder'] ?? '';
        $required = in_array('required', $properties['validation']['rules'] ?? []);
        $min = $properties['min'] ?? '';
        $max = $properties['max'] ?? '';
        $step = $properties['step'] ?? '';

        return view('components.form-builder.preview.number-input', [
            'fieldName' => $fieldName,
            'label' => $label,
            'placeholder' => $placeholder,
            'required' => $required,
            'min' => $min,
            'max' => $max,
            'step' => $step,
            'properties' => $properties,
        ])->render();
    }

    private function renderPasswordInput(string $fieldName, array $properties): string
    {
        $label = $properties['label'] ?? 'Password';
        $placeholder = $properties['placeholder'] ?? '';
        $required = in_array('required', $properties['validation']['rules'] ?? []);

        return view('components.form-builder.preview.password-input', [
            'fieldName' => $fieldName,
            'label' => $label,
            'placeholder' => $placeholder,
            'required' => $required,
            'properties' => $properties,
        ])->render();
    }

    private function renderFileInput(string $fieldName, array $properties): string
    {
        $label = $properties['label'] ?? 'File Upload';
        $required = in_array('required', $properties['validation']['rules'] ?? []);
        $multiple = $properties['multiple'] ?? false;

        return view('components.form-builder.preview.file-input', [
            'fieldName' => $fieldName,
            'label' => $label,
            'required' => $required,
            'multiple' => $multiple,
            'properties' => $properties,
        ])->render();
    }

    /**
     * Parse options string into array format
     */
    private function parseOptions($options): array
    {
        // If options is already an array, return it directly
        if (is_array($options)) {
            return $options;
        }

        // If options is a string, parse it using the OptionParserService
        $optionParser = app(\App\Services\FormBuilder\OptionParserService::class);

        return $optionParser->parseOptions($options);
    }
}
