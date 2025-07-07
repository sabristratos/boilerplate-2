@props(['element', 'properties', 'fluxProps', 'mode' => 'edit', 'fieldName' => null])

@php
    $min = $properties['min'] ?? '';
    $max = $properties['max'] ?? '';
    $step = $properties['step'] ?? '1';
    $clearable = $properties['clearable'] ?? true;
    $copyable = $properties['copyable'] ?? false;
    $hasIcon = $fluxProps['icon'] ?? false;
    $hasIconTrailing = $fluxProps['iconTrailing'] ?? false;
    $isPreview = $mode === 'preview';
    $wireModel = $isPreview && $fieldName ? "previewFormData.{$fieldName}" : null;
    $required = $isPreview ? (in_array('required', $properties['validation']['rules'] ?? []) ? 'true' : '') : '';
    
    // Build attributes string
    $attributes = [];
    if ($min) {
        $attributes[] = 'min="' . $min . '"';
    }
    if ($max) {
        $attributes[] = 'max="' . $max . '"';
    }
    $attributesString = implode(' ', $attributes);
@endphp

<x-forms.input 
    type="number"
    label="{{ $properties['label'] }}" 
    placeholder="{{ $properties['placeholder'] }}"
    :clearable="$fluxProps['clearable'] ?? false"
    :copyable="$fluxProps['copyable'] ?? false"
    :viewable="$fluxProps['viewable'] ?? false"
    icon="{{ $fluxProps['icon'] ?? null }}"
    iconTrailing="{{ $fluxProps['iconTrailing'] ?? null }}"
    wireModel="{{ $wireModel }}"
    :required="$required"
    min="{{ $properties['min'] ?? '' }}"
    max="{{ $properties['max'] ?? '' }}"
    step="{{ $properties['step'] ?? '1' }}"
    :error="$isPreview && $fieldName ? $errors->first("formData.{$fieldName}") : null"
/> 
