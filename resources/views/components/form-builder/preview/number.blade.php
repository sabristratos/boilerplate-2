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
    
    $icon = !empty($properties['fluxProps']['icon'] ?? '') ? $properties['fluxProps']['icon'] : null;
    $iconTrailing = !empty($properties['fluxProps']['iconTrailing'] ?? '') ? $properties['fluxProps']['iconTrailing'] : null;
@endphp

<flux:input 
    wire:model="previewFormData.{{ $fieldName }}" 
    type="number" 
    label="{{ $label }}{{ $required ? ' *' : '' }}"
    placeholder="{{ $placeholder }}"
    required="{{ $required ? 'true' : '' }}"
    @if($icon) icon="{{ $icon }}" @endif
    @if($iconTrailing) icon:trailing="{{ $iconTrailing }}" @endif
    badge="{{ $properties['badge'] ?? '' }}"
    description="{{ $properties['description'] ?? '' }}"
    description-trailing="{{ $properties['descriptionTrailing'] ?? false ? 'true' : 'false' }}"
    {!! $attributesString !!}
/>
@error("previewFormData.{$fieldName}")
    <flux:error>{{ $message }}</flux:error>
@enderror 