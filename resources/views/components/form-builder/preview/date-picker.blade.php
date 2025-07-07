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
@endphp

<x-forms.date-picker 
    wireModel="previewFormData.{{ $fieldName }}" 
    label="{{ $label }}{{ $required ? ' *' : '' }}"
    placeholder="{{ $placeholder }}"
    required="{{ $required ? 'true' : 'false' }}"
    mode="{{ $mode }}"
    weekNumbers="{{ $weekNumbers ? 'true' : 'false' }}"
    selectableHeader="{{ $selectableHeader ? 'true' : 'false' }}"
    withToday="{{ $withToday ? 'true' : 'false' }}"
    withInputs="{{ $withInputs ? 'true' : 'false' }}"
    withConfirmation="{{ $withConfirmation ? 'true' : 'false' }}"
    withPresets="{{ $withPresets ? 'true' : 'false' }}"
    clearable="{{ $clearable ? 'true' : 'false' }}"
    disabled="{{ $disabled ? 'true' : 'false' }}"
    min="{{ $min }}"
    max="{{ $max }}"
    description="{{ $description }}"
    badge="{{ $badge }}"
    presets="{{ $presets }}"
@php
    $error = isset($errors) ? $errors->first("previewFormData.{$fieldName}") : null;
@endphp
    error="{{ $error }}"
/> 