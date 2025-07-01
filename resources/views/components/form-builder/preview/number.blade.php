@php
    $attributes = [];
    if (!empty($properties['min'])) {
        $attributes[] = 'min="' . $properties['min'] . '"';
    }
    if (!empty($properties['max'])) {
        $attributes[] = 'max="' . $properties['max'] . '"';
    }
    if (!empty($properties['step'])) {
        $attributes[] = 'step="' . $properties['step'] . '"';
    }
    $attributesString = implode(' ', $attributes);
@endphp

<flux:input 
    wire:model="previewFormData.{{ $fieldName }}" 
    type="number" 
    label="{{ $label }}"
    placeholder="{{ $placeholder }}"
    {!! $attributesString !!}
/>
@error("previewFormData.{$fieldName}")
    <flux:error>{{ $message }}</flux:error>
@enderror 