@props(['element', 'properties', 'fluxProps', 'mode' => 'edit', 'fieldName' => null])

@php
    // Extract all properties with defaults
    $mode = $properties['mode'] ?? 'single';
    $minRange = $properties['minRange'] ?? '';
    $maxRange = $properties['maxRange'] ?? '';
    $min = $properties['min'] ?? '';
    $max = $properties['max'] ?? '';
    $months = $properties['months'] ?? 1;
    $description = $properties['description'] ?? '';
    $descriptionTrailing = $properties['descriptionTrailing'] ?? false;
    $badge = $properties['badge'] ?? '';
    $size = $properties['size'] ?? 'default';
    $weekNumbers = $properties['weekNumbers'] ?? false;
    $selectableHeader = $properties['selectableHeader'] ?? false;
    $withToday = $properties['withToday'] ?? false;
    $withInputs = $properties['withInputs'] ?? false;
    $withConfirmation = $properties['withConfirmation'] ?? false;
    $withPresets = $properties['withPresets'] ?? false;
    $presets = $properties['presets'] ?? 'today yesterday thisWeek last7Days thisMonth yearToDate allTime';
    $clearable = $properties['clearable'] ?? true;
    $disabled = $properties['disabled'] ?? false;
    $invalid = $properties['invalid'] ?? false;
    $locale = $properties['locale'] ?? app()->getLocale() ?? 'en';
    // Ensure locale is always a valid value
    $locale = !empty($locale) ? $locale : 'en';
    
    // Preview mode specific properties
    $isPreview = $mode === 'preview';
    $wireModel = $isPreview && $fieldName ? "formData.{$fieldName}" : null;
    $required = $isPreview ? (in_array('required', $properties['validation']['rules'] ?? []) ? 'true' : '') : '';
@endphp

<x-forms.date-picker 
    label="{{ $properties['label'] }}" 
    placeholder="{{ $properties['placeholder'] }}"
    mode="{{ $mode }}"
    :weekNumbers="$weekNumbers"
    :selectableHeader="$selectableHeader"
    :withToday="$withToday"
    :withInputs="$withInputs"
    :withConfirmation="$withConfirmation"
    :withPresets="$withPresets"
    :clearable="$clearable"
    :disabled="$disabled"
    wireModel="{{ $wireModel }}"
    :required="$required"
    min="{{ $min }}"
    max="{{ $max }}"
    description="{{ $description }}"
    badge="{{ $badge }}"
    presets="{{ $presets }}"
    :error="$isPreview && $fieldName ? $errors->first("formData.{$fieldName}") : null"
/> 