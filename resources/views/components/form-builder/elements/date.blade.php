@props(['element', 'properties', 'fluxProps'])

@php
    $mode = $properties['mode'] ?? 'single';
    $withPresets = $properties['withPresets'] ?? false;
    $clearable = $properties['clearable'] ?? true;
    $min = $properties['min'] ?? '';
    $max = $properties['max'] ?? '';
    
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

<flux:date-picker 
    label="{{ $properties['label'] }}" 
    placeholder="{{ $properties['placeholder'] }}"
    mode="{{ $mode }}"
    :with-presets="$withPresets"
    :clearable="$clearable"
    {!! $attributesString !!}
>
    <x-slot name="trigger">
        <flux:date-picker.input />
    </x-slot>
</flux:date-picker> 