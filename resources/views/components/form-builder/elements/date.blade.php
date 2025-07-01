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
    $wireModel = $isPreview && $fieldName ? "previewFormData.{$fieldName}" : null;
    $required = $isPreview ? (in_array('required', $properties['validation']['rules'] ?? []) ? 'true' : '') : '';
@endphp

<flux:date-picker 
    label="{{ $properties['label'] }}" 
    placeholder="{{ $properties['placeholder'] }}"
    mode="{{ $mode }}"
    :months="$months"
    :description:trailing="$descriptionTrailing"
    size="{{ $size }}"
    :week-numbers="$weekNumbers"
    :selectable-header="$selectableHeader"
    :with-today="$withToday"
    :with-inputs="$withInputs"
    :with-confirmation="$withConfirmation"
    :with-presets="$withPresets"
    :clearable="$clearable"
    :disabled="$disabled"
    :invalid="$invalid"
    locale="{{ $locale }}"
    :wire:model="$wireModel"
    :required="$required"
    :min-range="$isPreview && $minRange ? $minRange : null"
    :max-range="$isPreview && $maxRange ? $maxRange : null"
    :min="$isPreview && $min ? $min : null"
    :max="$isPreview && $max ? $max : null"
    :description="$isPreview && $description ? $description : null"
    :badge="$isPreview && $badge ? $badge : null"
    :presets="$isPreview && $presets ? $presets : null"
>
    <x-slot name="trigger">
        <flux:date-picker.input />
    </x-slot>
</flux:date-picker>

@if($isPreview && $fieldName)
    @error("previewFormData.{$fieldName}")
        <flux:error>{{ $message }}</flux:error>
    @enderror
@endif 