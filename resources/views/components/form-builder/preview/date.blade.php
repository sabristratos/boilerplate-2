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
    $locale = $properties['locale'] ?? '';
@endphp

<flux:date-picker 
    wire:model="previewFormData.{{ $fieldName }}" 
    label="{{ $label }}"
    placeholder="{{ $placeholder }}"
    mode="{{ $mode }}"
    :months="$months"
    :description-trailing="$descriptionTrailing"
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
    :min-range="$minRange"
    :max-range="$maxRange"
    :min="$min"
    :max="$max"
    :description="$description"
    :badge="$badge"
    :presets="$presets"
    :locale="$locale"
>
    <x-slot name="trigger">
        <flux:date-picker.input />
    </x-slot>
</flux:date-picker>
@error("previewFormData.{$fieldName}")
    <flux:error>{{ $message }}</flux:error>
@enderror 